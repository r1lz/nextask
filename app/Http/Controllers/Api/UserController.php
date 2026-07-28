<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ApiResponse;
use App\Http\Resources\UserResource;
use App\Http\Resources\UserPublicResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use ApiResponse;

    /**
     * <en>List Users</en><id>Daftar Pengguna</id>
     *
     * <en>Returns a paginated list of all registered users. Only public fields (`id`, `name`) are exposed — email addresses are intentionally hidden to protect user privacy.

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.

**Why you'd use this:** To find user IDs for task assignment. When creating or updating a task, the `assigned_to` field requires a valid user `id`. Look up the user here and copy their `id`.</en>
     * <id>Mengembalikan daftar semua pengguna yang terdaftar dalam format paginasi. Hanya field publik (`id`, `name`) yang ditampilkan — alamat email sengaja disembunyikan untuk melindungi privasi pengguna.

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.

**Kenapa menggunakan ini:** Untuk menemukan ID pengguna saat menugaskan tugas. Ketika membuat atau memperbarui tugas, field `assigned_to` membutuhkan `id` pengguna yang valid. Cari penggunanya di sini dan salin `id`-nya.</id>
     */
    public function index()
    {
        $users = User::latest()->paginate(10);
        return $this->success(
            UserPublicResource::collection($users)->response()->getData(true),
            'Users retrieved successfully'
        );
    }

    /**
     * <en>User Details</en><id>Detail Pengguna</id>
     *
     * <en>Retrieve the public profile of a specific user by their ID. Only non-sensitive fields (`id`, `name`) are returned — email is not exposed for privacy reasons.

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.

**Steps:**
1. Call **List Users** to browse all registered users and find their IDs.
2. Copy the `id` of the user you want to view.
3. Enter it in the path parameter and send.</en>
     * <id>Mengambil profil publik pengguna tertentu berdasarkan ID-nya. Hanya field non-sensitif (`id`, `name`) yang dikembalikan — email tidak diekspos demi alasan privasi.

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.

**Langkah-langkah:**
1. Panggil **Daftar Pengguna** untuk melihat semua pengguna terdaftar dan ID-nya.
2. Salin `id` dari pengguna yang ingin kamu lihat.
3. Masukkan di parameter path, lalu kirim.</id>
     */
    public function show(User $user)
    {
        return $this->success(new UserPublicResource($user));
    }

    /**
     * <en>Update User</en><id>Perbarui Data Pengguna</id>
     *
     * <en>Update a user's `name` or `email`. 

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.
>
> **Additionally:** you can only update **your own** account.

Attempting to update another user's profile will return `403 Forbidden`.</en>
     * <id>Memperbarui `name` atau `email` pengguna. 

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.
>
> **Selain itu:** kamu hanya bisa memperbarui akun milik **kamu sendiri**.

Mencoba memperbarui profil pengguna lain akan mengembalikan `403 Forbidden`.</id>
     */
    public function update(Request $request, User $user)
    {
        if ($user->id !== \Illuminate\Support\Facades\Auth::id()) {
            return $this->error('Unauthorized', 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update($validated);

        return $this->success(new UserResource($user), 'User updated successfully');
    }

    /**
     * <en>Delete User</en><id>Hapus Pengguna</id>
     *
     * <en>Permanently delete a user account and all associated data. 

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.
>
> **Additionally:** you can only delete **your own** account.

This action is irreversible. Attempting to delete another user's account will return `403 Forbidden`.</en>
     * <id>Menghapus akun pengguna beserta semua data yang terkait secara permanen. 

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.
>
> **Selain itu:** kamu hanya bisa menghapus akun milik **kamu sendiri**.

Aksi ini tidak dapat dibatalkan. Mencoba menghapus akun orang lain akan mengembalikan `403 Forbidden`.</id>
     */
    public function destroy(User $user)
    {
        if ($user->id !== \Illuminate\Support\Facades\Auth::id()) {
            return $this->error('Unauthorized', 403);
        }

        $user->delete();

        return $this->success(null, 'User deleted successfully');
    }
}
