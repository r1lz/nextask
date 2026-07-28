<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Traits\ApiResponse;
use App\Http\Resources\ProjectResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    use ApiResponse;

    /**
     * <en>List Projects</en><id>Daftar Project</id>
     *
     * <en>Returns a paginated list of all projects owned by the authenticated user, ordered by newest first. Each item includes `tasks_count` showing how many tasks exist inside.

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.

Projects from other users will never appear in this list — the API enforces strict ownership.</en>
     * <id>Mengembalikan daftar project milik pengguna yang sedang login, diurutkan dari yang terbaru. Setiap item menyertakan `tasks_count` yang menunjukkan jumlah tugas di dalamnya.

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.

Project milik pengguna lain tidak akan pernah muncul di sini — API menerapkan kepemilikan secara ketat.</id>
     */
    public function index()
    {
        $projects = Auth::user()->projects()->withCount('tasks')->latest()->paginate(20);
        return $this->success(
            ProjectResource::collection($projects)->response()->getData(true),
            'Projects retrieved successfully'
        );
    }

    /**
     * <en>Create Project</en><id>Buat Project Baru</id>
     *
     * <en>Create a new project under your account. Only `name` is required; `description` is optional.

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.

After creating a project, copy its `id` from the response — you'll need it to create tasks inside this project (`POST /projects/{id}/tasks`).</en>
     * <id>Buat project baru di akun kamu. Hanya `name` yang wajib diisi; `description` bersifat opsional.

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.

Setelah membuat project, salin `id`-nya dari respons — kamu butuh itu untuk membuat tugas di dalam project ini (`POST /projects/{id}/tasks`).</id>
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project = Auth::user()->projects()->create($validated);

        return $this->success(
            new ProjectResource($project),
            'Project created successfully',
            201
        );
    }

    /**
     * <en>Project Details</en><id>Detail Project</id>
     *
     * <en>Retrieve full details of a single project by its ID, including owner profile and task count.

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.
>
> **Additionally:** the project must belong to you.

**Steps:**
1. First, call **List Projects** to get a list of your projects and their IDs.
2. Copy the `id` of the project you want.
3. Enter it in the `id` path parameter and send.

Attempting to access another user's project will return `403 Forbidden`.</en>
     * <id>Mengambil detail lengkap dari satu project berdasarkan ID-nya, termasuk profil pemilik dan jumlah tugas.

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.
>
> **Selain itu:** project tersebut harus milik kamu.

**Langkah-langkah:**
1. Panggil **Daftar Project** terlebih dahulu untuk mendapatkan daftar project dan ID-nya.
2. Salin `id` dari project yang ingin kamu lihat.
3. Masukkan di parameter path `id`, lalu kirim.

Mencoba mengakses project milik pengguna lain akan mengembalikan `403 Forbidden`.</id>
     */
    public function show(Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            return $this->error('Unauthorized', 403);
        }

        return $this->success(new ProjectResource($project->load('user')->loadCount('tasks')));
    }

    /**
     * <en>Update Project</en><id>Perbarui Project</id>
     *
     * <en>Update the `name` or `description` of an existing project. Only fields included in the request body will be changed.

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.
>
> **Additionally:** the project must belong to you.

**Steps:**
1. Call **List Projects** to find your project's ID.
2. Enter the project `id` in the path parameter.
3. In the request body, include only the fields you want to change.

Returns `403 Forbidden` if the project belongs to another user.</en>
     * <id>Perbarui `name` atau `description` dari project yang sudah ada. Hanya field yang disertakan dalam request body yang akan diubah.

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.
>
> **Selain itu:** project tersebut harus milik kamu.

**Langkah-langkah:**
1. Panggil **Daftar Project** untuk menemukan ID project kamu.
2. Masukkan `id` project di parameter path.
3. Di request body, sertakan hanya field yang ingin kamu ubah.

Mengembalikan `403 Forbidden` jika project bukan milik kamu.</id>
     */
    public function update(Request $request, Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            return $this->error('Unauthorized', 403);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $project->update($validated);

        return $this->success(new ProjectResource($project), 'Project updated successfully');
    }

    /**
     * <en>Delete Project</en><id>Hapus Project</id>
     *
     * <en>Soft delete a project. The record is flagged as deleted but not physically removed, so data can potentially be recovered.

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.
>
> **Additionally:** the project must belong to you.

**Steps:**
1. Call **List Projects** to find the project ID you want to delete.
2. Enter that `id` in the path parameter.
3. Send the request.

**Note:** Deleting a project does not automatically delete its tasks from the database — they will simply no longer be accessible via the API.</en>
     * <id>Hapus project secara soft delete. Record ditandai sebagai terhapus tapi tidak dihapus secara fisik, sehingga data berpotensi bisa dipulihkan.

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.
>
> **Selain itu:** project tersebut harus milik kamu.

**Langkah-langkah:**
1. Panggil **Daftar Project** untuk menemukan ID project yang ingin dihapus.
2. Masukkan `id` tersebut di parameter path.
3. Kirim request.

**Catatan:** Menghapus project tidak otomatis menghapus tugas-tugasnya dari database — tugas tersebut hanya tidak bisa diakses lagi via API.</id>
     */
    public function destroy(Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            return $this->error('Unauthorized', 403);
        }

        $project->delete();

        return $this->success(null, 'Project deleted successfully');
    }
}
