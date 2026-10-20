<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\Process\Process;

class TerminalController extends Controller
{
    /**
     * Token perintah pertama yang diblokir demi keamanan server.
     */
    private const BLOCKED_STARTS = [
        'sudo', 'su', 'shutdown', 'reboot', 'halt', 'poweroff', 'init',
        'systemctl', 'service', 'mkfs', 'dd', 'fdisk', 'parted',
        'kill', 'killall', 'pkill', 'crontab', 'passwd', 'adduser',
        'useradd', 'userdel', 'usermod', 'visudo', 'rm', 'apt', 'apt-get',
        'yum', 'dnf', 'chmod', 'chown',
    ];

    /**
     * Pola berbahaya di tengah perintah (fork bomb, pipe ke shell, dsb).
     */
    private const BLOCKED_PATTERNS = [
        '/:\(\)\s*\{.*\}\s*;\s*:/'                      => 'fork bomb',
        '/\/dev\/sd[a-z]/i'                              => 'menulis ke device disk',
        '/>\s*\/dev\//i'                                 => 'redirect ke device',
        '/(\bcurl|\bwget)\b[^\n]*\|\s*(ba)?sh(\s|$)/i'   => 'download di-pipe ke shell',
        '/\|\s*(ba)?sh(\s|$)/i'                          => 'pipe ke shell',
        '/\brm\s+(-[a-z]*[rf][a-z]*[rf])/i'              => 'rm rekursif/force',
    ];

    /** Batas output (byte) yang dikirim balik ke browser */
    private const MAX_OUTPUT = 65536;

    /** Timeout proses (detik) */
    public const TIMEOUT = 60;

    public function index()
    {
        $this->authorizeAdmin();

        return view('terminal.index', [
            'phpVersion' => PHP_VERSION,
            'basePath' => base_path(),
        ]);
    }

    public function run(Request $request): JsonResponse
    {
        $this->authorizeAdmin();

        $validated = $request->validate([
            'command' => 'required|string|max:500',
        ]);

        $command = trim($validated['command']);
        $user = $request->user();

        // ---- Filter perintah berbahaya ----
        if ($reason = $this->blockedReason($command)) {
            activity()->causedBy($user)->log("Terminal [DIBLOKIR]: {$command}");

            return response()->json([
                'command' => $command,
                'output' => "⛔ Perintah diblokir demi keamanan server ({$reason}).",
                'exitCode' => 126,
                'blocked' => true,
            ]);
        }

        activity()->causedBy($user)->log("Terminal: {$command}");

        // ---- Eksekusi ----
        $start = microtime(true);
        $timedOut = false;

        try {
            $process = Process::fromShellCommandline($command, base_path(), null, null, self::TIMEOUT);
            $process->run();

            $output = $process->getOutput() . $process->getErrorOutput();
            $exitCode = $process->getExitCode();
        } catch (\Symfony\Component\Process\Exception\ProcessTimedOutException) {
            $output = "⏱ Proses dihentikan karena melebihi batas waktu " . self::TIMEOUT . " detik.";
            $exitCode = 124;
            $timedOut = true;
        } catch (\Throwable $e) {
            $output = '❌ Gagal menjalankan perintah: ' . $e->getMessage();
            $exitCode = 1;
        }

        // Truncate output agar respons tetap ringan
        if (strlen($output) > self::MAX_OUTPUT) {
            $output = substr($output, -self::MAX_OUTPUT);
            $output = "…(output dipotong, hanya " . self::MAX_OUTPUT . " byte terakhir ditampilkan)…\n\n" . $output;
        }

        return response()->json([
            'command' => $command,
            'output' => $output !== '' ? $output : '(tidak ada output)',
            'exitCode' => $exitCode ?? 0,
            'duration' => round((microtime(true) - $start) * 1000),
            'timedOut' => $timedOut,
        ]);
    }

    /**
     * Cek apakah perintah diblokir. Return alasan jika diblokir, null jika aman.
     */
    private function blockedReason(string $command): ?string
    {
        $first = strtolower(explode(' ', $command)[0]);

        if (in_array($first, array_map('strtolower', self::BLOCKED_STARTS), true)) {
            return "perintah `{$first}` tidak diizinkan";
        }

        foreach (self::BLOCKED_PATTERNS as $pattern => $label) {
            if (preg_match($pattern, $command)) {
                return $label;
            }
        }

        return null;
    }

    /**
     * Double protection: hanya Super Administrator / Administrator.
     */
    private function authorizeAdmin(): void
    {
        $user = auth()->user();

        abort_unless(
            $user && $user->hasRole(['Super Administrator', 'Administrator']),
            403,
            'Menu Terminal hanya dapat diakses oleh Administrator.'
        );
    }
}
