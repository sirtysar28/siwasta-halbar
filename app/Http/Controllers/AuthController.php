<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Models\LoginActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Kode captcha login (5 digit) - STABIL / tidak berubah-ubah
     */
    public const CAPTCHA_CODE = '86420';

    public function showLogin()
    {
        return view('auth.login', ['captchaCode' => self::CAPTCHA_CODE]);
    }

    public function login(LoginRequest $request)
    {
        // Validasi captcha (kode stabil, tidak pernah berubah)
        if ($request->input('captcha') !== self::CAPTCHA_CODE) {
            throw ValidationException::withMessages([
                'captcha' => 'Kode captcha salah. Masukkan 5 digit kode sesuai tampilan.',
            ]);
        }

        $credentials = $request->only('email', 'password');
        $credentials['is_active'] = true;

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah, atau akun nonaktif.',
            ]);
        }

        $request->session()->regenerate();

        $user = Auth::user();
        $user->update([
            'last_login_at' => now(),
            'last_login_ip' => $request->ip(),
            'device_info' => $request->userAgent(),
        ]);

        LoginActivity::create([
            'user_id' => $user->id,
            'ip_address' => $request->ip(),
            'device' => $request->header('User-Agent'),
            'browser' => $request->header('User-Agent'),
            'platform' => $request->header('User-Agent'),
            'login_at' => now(),
        ]);

        activity()->causedBy($user)->log('Login ke sistem');

        return redirect()->intended(route('dashboard'))->with('success', "Selamat datang, {$user->name}!");
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            activity()->causedBy($user)->log('Logout dari sistem');
        }

        LoginActivity::where('user_id', $user?->id)
            ->whereNull('logout_at')
            ->latest()
            ->first()?->update(['logout_at' => now()]);

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah keluar dari sistem.');
    }

    public function profile()
    {
        return view('users.profile', ['user' => Auth::user()]);
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('avatar')) {
            $path = $request->file('avatar')->store('avatars', 'public');
            $validated['avatar'] = $path;
        }

        $user->update($validated);
        activity()->causedBy($user)->log('Memperbarui profil');

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => 'required|current_password',
            'password' => 'required|string|min:8|confirmed',
        ]);

        Auth::user()->update([
            'password' => $validated['password'],
        ]);

        activity()->causedBy(Auth::user())->log('Mengubah password');

        return back()->with('success', 'Password berhasil diubah.');
    }
}
