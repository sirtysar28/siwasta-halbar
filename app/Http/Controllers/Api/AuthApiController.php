<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthApiController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'device_name' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->where('is_active', true)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah, atau akun nonaktif.'],
            ]);
        }

        $token = $user->createToken($request->device_name)->plainTextToken;

        $user->update(['last_login_at' => now(), 'last_login_ip' => $request->ip()]);

        return response()->json([
            'status' => 'success',
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user->load('roles', 'opd'),
                'token' => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string',
            'opd_id' => 'nullable|exists:opd,id',
        ]);
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $user->assignRole('Guest');

        $token = $user->createToken('mobile-app')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Registrasi berhasil',
            'data' => ['user' => $user, 'token' => $token],
        ], 201);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()->load('roles', 'opd'),
        ]);
    }

    public function refresh(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        $token = $request->user()->createToken('refresh')->plainTextToken;
        return response()->json(['status' => 'success', 'data' => ['token' => $token]]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['status' => 'success', 'message' => 'Logout berhasil']);
    }
}
