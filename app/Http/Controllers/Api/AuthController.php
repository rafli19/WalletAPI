<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:50', 'unique:users,username', 'alpha_num'],
            'email'    => ['required', 'email:rfc,dns', 'unique:users,email', 'max:255'],
            'phone'    => ['nullable', 'string', 'max:15', 'unique:users,phone'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'email.email'        => 'Format email tidak valid.',
            'email.unique'       => 'Email sudah terdaftar.',
            'username.unique'    => 'Username sudah digunakan.',
            'username.alpha_num' => 'Username hanya boleh huruf dan angka.',
            'password.min'       => 'Password minimal 8 karakter.',
            'password.confirmed' => 'Konfirmasi password tidak cocok.',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'phone'    => $request->phone ?? null,
            'password' => Hash::make($request->password),
            'balance'  => 0,
            'role'     => 'user',
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Registrasi berhasil.',
            'data'    => [
                'user'       => $this->formatUser($user),
                'token'      => $token,
                'token_type' => 'Bearer',
            ],
        ], 201);
    }

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Email atau password salah.',
                'errors'  => [
                    'credentials' => ['Email atau password tidak valid.'],
                ],
            ], 401);
        }

        $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login berhasil.',
            'data'    => [
                'user'       => $this->formatUser($user),
                'token'      => $token,
                'token_type' => 'Bearer',
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Logout berhasil.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $this->formatUser($request->user()),
        ]);
    }

    public function updateProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'             => ['sometimes', 'string', 'max:255'],
            'email'            => ['sometimes', 'email:rfc,dns', 'unique:users,email,' . $user->id],
            'phone'            => ['nullable', 'string', 'max:15', 'unique:users,phone,' . $user->id],
            'current_password' => ['required_with:password', 'string'],
            'password'         => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar'           => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'email.unique'                   => 'Email sudah digunakan oleh akun lain.',
            'phone.unique'                   => 'Nomor telepon sudah terdaftar.',
            'password.min'                   => 'Password minimal 8 karakter.',
            'password.confirmed'             => 'Konfirmasi password tidak cocok.',
            'current_password.required_with' => 'Password saat ini wajib diisi untuk mengubah password.',
            'avatar.image'                   => 'File harus berupa gambar.',
            'avatar.mimes'                   => 'Format gambar harus: jpg, jpeg, png, atau webp.',
            'avatar.max'                     => 'Ukuran gambar maksimal 2MB.',
        ]);

        $this->applyProfileUpdates($user, $validated, $request);

        $user->save();

        return response()->json([
            'data' => $this->formatUser($user),
        ]);
    }

    private function applyProfileUpdates(User $user, array $validated, Request $request): void
    {
        foreach (['name', 'email', 'phone'] as $field) {
            if (isset($validated[$field])) {
                $user->$field = $validated[$field];
            }
        }

        if ($request->filled('password')) {
            if (! Hash::check($request->current_password, $user->password)) {
                abort(response()->json([
                    'message' => 'Password saat ini tidak valid.',
                    'errors'  => ['current_password' => ['Password saat ini salah.']],
                ], 422));
            }
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            $this->replaceAvatar($user, $request->file('avatar'));
        }
    }

    private function replaceAvatar(User $user, $file): void
    {
        if ($user->avatar) {
            $oldPath = 'avatars/' . $user->avatar;
            if (Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
        }

        $filename     = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $user->avatar = $filename;
        $file->storeAs('avatars', $filename, 'public');
    }

    private function formatUser(User $user): array
    {
        return [
            'id'       => $user->id,
            'name'     => $user->name,
            'username' => $user->username,
            'email'    => $user->email,
            'phone'    => $user->phone,
            'avatar'   => $user->avatar,
            'role'     => $user->role,   // ← tambahan, dibutuhkan frontend
        ];
    }
}