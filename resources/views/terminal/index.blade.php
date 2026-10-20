@extends('layouts.app')

@section('title', 'Terminal')
@section('page-title', 'Terminal')

@section('content')
<div class="row justify-content-center">
    <div class="col-12 col-xl-10">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white d-flex align-items-center justify-content-between py-2" style="border-radius: .65rem .65rem 0 0 !important;">
                <div class="d-flex align-items-center gap-2">
                    <span class="terminal-dot bg-danger"></span>
                    <span class="terminal-dot bg-warning"></span>
                    <span class="terminal-dot bg-success"></span>
                    <span class="ms-2 small fw-semibold"><i class="bi bi-terminal me-1"></i> SIWASTA Terminal — admin@siwasta</span>
                </div>
                <span class="badge bg-danger text-uppercase" style="font-size:.6rem">Admin Only</span>
            </div>
            <div class="card-body p-0">
                {{-- Layar terminal --}}
                <div id="terminal-screen" class="terminal-screen">
                    <div class="text-success">SIWASTA-HALBAR Web Terminal v1.0</div>
                    <div class="text-secondary-muted">PHP {{ $phpVersion }} · Direktori: {{ $basePath }}</div>
                    <div class="text-secondary-muted">Perintah berbahaya (rm, sudo, dd, shutdown, dll) otomatis diblokir · Timeout {{ \App\Http\Controllers\TerminalController::TIMEOUT }} detik</div>
                    <div class="text-secondary-muted">Ketik <span class="text-warning">clear</span> untuk membersihkan layar · <kbd>↑</kbd>/<kbd>↓</kbd> riwayat perintah</div>
                    <div class="mt-2"></div>
                </div>

                {{-- Input perintah --}}
                <form id="terminal-form" class="terminal-input-bar">
                    <span class="prompt">➜ ~$</span>
                    <input type="text" id="terminal-input" class="terminal-input" placeholder="ketik perintah…" autocomplete="off" spellcheck="false" autofocus>
                    <button type="submit" class="btn btn-sm btn-success px-3" id="terminal-run">
                        <i class="bi bi-play-fill"></i><span class="d-none d-sm-inline ms-1">Jalankan</span>
                    </button>
                </form>
            </div>
            <div class="card-footer bg-white">
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="text-muted small me-1"><i class="bi bi-lightning-charge-fill text-warning"></i> Cepat:</span>
                    @foreach([
                        'php artisan --version' => 'Artisan Ver',
                        'php artisan route:list' => 'Route List',
                        'php artisan migrate:status' => 'Migrate',
                        'php artisan optimize:clear' => 'Optimize Clear',
                        'php artisan queue:status' => 'Queue',
                        'ls -la' => 'ls -la',
                        'df -h' => 'Disk',
                        'php -v' => 'PHP Ver',
                    ] as $cmd => $label)
                        <button type="button" class="btn btn-sm btn-outline-secondary quick-cmd" data-cmd="{{ $cmd }}">{{ $label }}</button>
                    @endforeach
                    <button type="button" class="btn btn-sm btn-outline-danger ms-auto" id="terminal-clear"><i class="bi bi-trash3 me-1"></i>Bersihkan</button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .terminal-screen {
        background: #0d1117;
        color: #c9d1d9;
        font-family: 'SF Mono', 'Fira Code', 'Courier New', monospace;
        font-size: .82rem;
        line-height: 1.55;
        height: 55vh;
        min-height: 320px;
        overflow-y: auto;
        padding: 1rem 1.1rem;
        white-space: pre-wrap;
        word-break: break-word;
    }
    .terminal-screen::-webkit-scrollbar { width: 8px; }
    .terminal-screen::-webkit-scrollbar-thumb { background: #30363d; border-radius: 10px; }
    .terminal-screen .cmd-line { color: #58a6ff; }
    .terminal-screen .out-ok { color: #c9d1d9; }
    .terminal-screen .out-err { color: #f85149; }
    .terminal-screen .out-blocked { color: #f85149; font-weight: 700; }
    .terminal-screen .meta { color: #8b949e; font-size: .72rem; }
    .terminal-screen .text-success { color: #3fb950; }
    .terminal-screen .text-warning { color: #d29922; }
    .terminal-screen .text-secondary-muted { color: #8b949e; font-size: .74rem; }
    .terminal-screen kbd {
        background: #21262d; border: 1px solid #30363d; border-radius: 4px;
        padding: 0 4px; font-size: .68rem; color: #c9d1d9;
    }
    .terminal-dot { width: 11px; height: 11px; border-radius: 50%; display: inline-block; }
    .terminal-input-bar {
        display: flex; align-items: center; gap: .6rem;
        background: #161b22; border-top: 1px solid #30363d;
        padding: .65rem 1rem;
    }
    .terminal-input-bar .prompt {
        color: #3fb950; font-family: 'SF Mono', 'Fira Code', 'Courier New', monospace;
        font-weight: 700; font-size: .85rem; white-space: nowrap;
    }
    .terminal-input {
        flex: 1; background: #0d1117; border: 1px solid #30363d; color: #c9d1d9;
        border-radius: .4rem; padding: .5rem .75rem; font-size: .85rem;
        font-family: 'SF Mono', 'Fira Code', 'Courier New', monospace;
    }
    .terminal-input:focus {
        outline: none; border-color: #3fb950;
        box-shadow: 0 0 0 .2rem rgba(63,185,80,.15); color: #c9d1d9;
    }
    .spinner-terminal { display: inline-block; width: 14px; height: 14px; border: 2px solid #30363d; border-top-color: #3fb950; border-radius: 50%; animation: spin 0.8s linear infinite; vertical-align: -2px; }
    @@keyframes spin { to { transform: rotate(360deg); } }
    @media (max-width: 575.98px) {
        .terminal-screen { font-size: .74rem; height: 50vh; padding: .8rem .9rem; }
        .terminal-input { font-size: .8rem; }
        .quick-cmd { font-size: .68rem; padding: .15rem .5rem; }
    }
</style>
@endpush

@push('scripts')
<script>
(function () {
    const screen   = document.getElementById('terminal-screen');
    const form     = document.getElementById('terminal-form');
    const input    = document.getElementById('terminal-input');
    const runBtn   = document.getElementById('terminal-run');
    const history  = [];
    let histIndex  = -1;
    let busy       = false;

    const esc = (s) => s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
    const scrollDown = () => { screen.scrollTop = screen.scrollHeight; };

    function print(html, cls = 'out-ok') {
        const div = document.createElement('div');
        div.className = cls;
        div.innerHTML = html;
        screen.appendChild(div);
        scrollDown();
    }

    async function runCommand(cmd) {
        if (busy) return;
        cmd = cmd.trim();
        if (!cmd) return;

        // Perintah bawaan browser
        if (cmd === 'clear' || cmd === 'cls') { clearScreen(); return; }
        if (cmd === 'help') {
            printHelp(cmd);
            return;
        }

        history.push(cmd);
        histIndex = history.length;
        print(`<span class="prompt-text">➜ ~$</span> ${esc(cmd)}`, 'cmd-line');

        busy = true;
        runBtn.disabled = true;
        const spinner = print('<span class="spinner-terminal"></span> menjalankan…', 'meta');

        try {
            const res = await fetch('{{ route('terminal.run') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': window.csrf,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ command: cmd }),
            });

            const data = await res.json();
            spinner.remove();

            if (res.status === 403) {
                print('⛔ 403 — ' + (data.message || 'Akses ditolak.'), 'out-blocked');
            } else if (res.status === 422) {
                print('⛔ Perintah tidak valid (maks 500 karakter).', 'out-err');
            } else {
                const cls = data.blocked ? 'out-blocked' : (data.exitCode === 0 ? 'out-ok' : 'out-err');
                if (data.output) print(esc(data.output), cls);
                const status = data.blocked ? 'DIBLOKIR' : (data.exitCode === 0 ? 'OK' : `exit ${data.exitCode}`);
                print(`— ${status} · ${data.duration ?? '?'} ms`, 'meta');
            }
        } catch (e) {
            spinner.remove();
            print('❌ Gagal terhubung ke server: ' + esc(e.message), 'out-err');
        } finally {
            busy = false;
            runBtn.disabled = false;
            input.focus();
        }
    }

    function clearScreen() {
        screen.querySelectorAll('div').forEach((d, i, all) => { if (i < all.length - 1) d.remove(); });
    }

    function printHelp(cmd) {
        print(`<span class="prompt-text">➜ ~$</span> ${esc(cmd)}`, 'cmd-line');
        print('Perintah shell & php artisan tersedia (mis: ls, cat .env, php artisan route:list).\nDiblokir: rm, sudo, su, dd, mkfs, shutdown, reboot, kill, chmod, chown, dll.\nPerintah bawaan: clear, help.', 'out-ok');
    }

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const v = input.value;
        input.value = '';
        runCommand(v);
    });

    // Riwayat perintah: panah atas/bawah
    input.addEventListener('keydown', (e) => {
        if (e.key === 'ArrowUp') {
            e.preventDefault();
            if (histIndex > 0) { histIndex--; input.value = history[histIndex]; }
        } else if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (histIndex < history.length - 1) { histIndex++; input.value = history[histIndex]; }
            else { histIndex = history.length; input.value = ''; }
        } else if (e.key === 'l' && (e.ctrlKey || e.metaKey)) {
            e.preventDefault();
            clearScreen();
        }
    });

    // Tombol perintah cepat
    document.querySelectorAll('.quick-cmd').forEach(btn => {
        btn.addEventListener('click', () => runCommand(btn.dataset.cmd));
    });

    document.getElementById('terminal-clear').addEventListener('click', clearScreen);
})();
</script>
<style>
    .terminal-screen .prompt-text { color: #3fb950; font-weight: 700; }
</style>
@endpush
