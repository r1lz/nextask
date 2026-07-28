<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * <en>Register New User</en><id>Registrasi Pengguna Baru</id>
     *
     * <en>Create a new user account. Provide a `name`, a unique `email`, and a `password`. On success, the response includes a Sanctum bearer token — meaning you are immediately authenticated and can start using protected endpoints right away without needing a separate login step.

> **No authentication needed.** Fill in the request body and send.

**Default test credentials (pre-seeded):** `default@example.com` / `default123`</en>
     * <id>Buat akun pengguna baru. Isi `name`, `email` unik, dan `password`. Jika berhasil, respons menyertakan Sanctum bearer token — artinya kamu langsung terautentikasi dan bisa menggunakan endpoint yang dilindungi tanpa perlu login ulang.

> **Tidak butuh autentikasi.** Langsung isi request body dan kirim.

**Kredensial default (sudah tersedia):** `default@example.com` / `default123`</id>
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'User registered successfully', 201);
    }

    /**
     * <en>Login</en><id>Masuk ke Sistem</id>
     *
     * <en>Exchange your credentials for a Sanctum bearer token. This token is required to access all protected endpoints.

> **No authentication needed.** Fill in `email` and `password`, then send.

**How to test other endpoints:**
Once you receive a successful response here, your browser automatically saves the session cookie. You can now go to any protected endpoint and test it directly without needing to copy or paste the token.

**Default test credentials:** `default@example.com` / `default123`</en>
     * <id>Tukar kredensial kamu dengan Sanctum bearer token. Token ini wajib dimiliki untuk mengakses semua endpoint yang dilindungi.

> **Tidak butuh autentikasi.** Isi `email` dan `password`, lalu kirim.

**Cara mencoba endpoint lain:**
Setelah mendapat respons sukses di sini, browser akan otomatis menyimpan cookie sesimu. Kamu sekarang bisa pergi ke endpoint mana pun yang dilindungi dan langsung mencobanya tanpa perlu repot copy-paste token.

**Kredensial default:** `default@example.com` / `default123`</id>
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return $this->error('Invalid login credentials', 401);
        }

        $user = User::where('email', $request->email)->firstOrFail();
        
        // Revoke existing tokens if we want single-device login, or let it accumulate
        // $user->tokens()->delete();

        $token = $user->createToken('auth_token')->plainTextToken;

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'Logged in successfully');
    }

    /**
     * <en>Logout</en><id>Keluar dari Sistem</id>
     *
     * <en>Revoke the currently active access token. The token is immediately invalidated server-side.

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.

After logging out, any subsequent request using the same token will receive `401 Unauthorized`. To continue using the API, log in again to obtain a new token.</en>
     * <id>Cabut token akses yang sedang aktif. Token langsung dinonaktifkan di sisi server.

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.

Setelah logout, semua request berikutnya menggunakan token yang sama akan mendapat `401 Unauthorized`. Untuk melanjutkan menggunakan API, login ulang untuk mendapatkan token baru.</id>
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return $this->success(null, 'Logged out successfully');
    }

    /**
     * <en>Check Login Status</en><id>Cek Status Login</id>
     *
     * <en>Retrieve the profile of the currently authenticated user. Use this as a quick way to verify your token is working.

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.

**How to interpret the response:**
- ✅ Returns your user profile → token is valid, you are authenticated.
- ❌ Returns `401 Unauthorized` → you haven't logged in yet or your session has expired.</en>
     * <id>Mengambil profil pengguna yang sedang aktif. Gunakan ini sebagai cara cepat untuk memverifikasi token kamu berfungsi.

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.

**Cara membaca responsnya:**
- ✅ Mengembalikan data profil kamu → token valid, kamu sudah terautentikasi.
- ❌ Mengembalikan `401 Unauthorized` → kamu belum login atau sesimu sudah berakhir.</id>
     */
    public function me(Request $request)
    {
        return $this->success(new UserResource($request->user()));
    }
}
