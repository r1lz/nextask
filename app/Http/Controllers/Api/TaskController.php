<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Task;
use App\Traits\ApiResponse;
use App\Http\Resources\TaskResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    use ApiResponse;

    /**
     * <en>List Project Tasks</en><id>Daftar Tugas dalam Project</id>
     *
     * <en>Returns a paginated list of tasks inside the specified project, including status, priority, assignee, and labels.

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.
>
> **Additionally:** the project must belong to you.

**Steps:**
1. Call **List Projects** first and copy the project `id`.
2. Enter that `id` as the `id` path parameter.
3. Send the request.

Returns `403 Forbidden` if the project belongs to another user.</en>
     * <id>Mengembalikan daftar tugas di dalam project yang ditentukan, termasuk status, prioritas, pengguna yang ditugaskan, dan label.

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.
>
> **Selain itu:** project tersebut harus milik kamu.

**Langkah-langkah:**
1. Panggil **Daftar Project** terlebih dahulu dan salin `id` project-nya.
2. Masukkan `id` tersebut sebagai parameter path `id`.
3. Kirim request.

Mengembalikan `403 Forbidden` jika project bukan milik kamu.</id>
     */
    public function index(Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            return $this->error('Unauthorized', 403);
        }

        $tasks = $project->tasks()->with(['assignee', 'labels'])->latest()->paginate(10);
        return $this->success(
            TaskResource::collection($tasks)->response()->getData(true),
            'Tasks retrieved successfully'
        );
    }

    /**
     * <en>Create Task</en><id>Tambah Tugas Baru</id>
     *
     * <en>Create a new task inside the specified project.

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.
>
> **Additionally:** you need the project's `id`.

**Steps:**
1. Call **List Projects** to get your project IDs.
2. Enter the project `id` in the path parameter.
3. Fill in the request body. Only `title` is required; everything else is optional.

**Optional fields:**
- `status`: `todo` (default) | `in_progress` | `done`
- `priority`: `low` | `medium` (default) | `high`
- `due_date`: ISO date format, e.g. `2025-12-31`
- `assigned_to`: User ID (call **List Users** to find valid user IDs)
- `labels`: Array of label IDs (call **List Labels** to find your label IDs)</en>
     * <id>Buat tugas baru di dalam project yang ditentukan.

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.
>
> **Selain itu:** butuh `id` dari project yang sudah ada.

**Langkah-langkah:**
1. Panggil **Daftar Project** untuk mendapatkan ID project kamu.
2. Masukkan `id` project di parameter path.
3. Isi request body. Hanya `title` yang wajib diisi; sisanya opsional.

**Field opsional:**
- `status`: `todo` (default) | `in_progress` | `done`
- `priority`: `low` | `medium` (default) | `high`
- `due_date`: Format tanggal ISO, misal `2025-12-31`
- `assigned_to`: ID pengguna (panggil **Daftar Pengguna** untuk menemukan ID yang valid)
- `labels`: Array berisi ID label (panggil **Daftar Label** untuk melihat ID label kamu)</id>
     */
    public function store(Request $request, Project $project)
    {
        if ($project->user_id !== Auth::id()) {
            return $this->error('Unauthorized', 403);
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:todo,in_progress,done',
            'priority' => 'in:low,medium,high',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'labels' => 'nullable|array',
            'labels.*' => 'exists:labels,id',
        ]);

        $task = $project->tasks()->create($validated);

        if (isset($validated['labels'])) {
            $task->labels()->sync($validated['labels']);
        }

        return $this->success(
            new TaskResource($task->load(['assignee', 'labels'])),
            'Task created successfully',
            201
        );
    }

    /**
     * <en>Task Details</en><id>Detail Tugas</id>
     *
     * <en>Retrieve the full details of a single task, including its parent project, assignee, and all attached labels.

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.
>
> **Additionally:** you need the task `id`.

**Steps:**
1. Call **List Project Tasks** first to get the task IDs inside a project.
2. Copy the `id` of the task you want.
3. Enter it in the `id` path parameter and send.

Both the project owner and the assigned user can access this endpoint.</en>
     * <id>Mengambil detail lengkap dari sebuah tugas, termasuk project induknya, pengguna yang ditugaskan, dan semua label yang terpasang.

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.
>
> **Selain itu:** butuh `id` dari tugas.

**Langkah-langkah:**
1. Panggil **Daftar Tugas dalam Project** terlebih dahulu untuk mendapatkan ID tugas di dalam project.
2. Salin `id` dari tugas yang ingin kamu lihat.
3. Masukkan di parameter path `id`, lalu kirim.

Baik pemilik project maupun pengguna yang di-assign bisa mengakses endpoint ini.</id>
     */
    public function show(Task $task)
    {
        // Load project to check authorization
        $task->load('project');
        if ($task->project->user_id !== Auth::id() && $task->assigned_to !== Auth::id()) {
            return $this->error('Unauthorized', 403);
        }

        return $this->success(new TaskResource($task->load(['project', 'assignee', 'labels'])));
    }

    /**
     * <en>Update Task</en><id>Perbarui Tugas</id>
     *
     * <en>Update any field of an existing task. The most common use case is changing the `status`.

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.
>
> **Additionally:** the task's parent project must belong to you.

**Steps:**
1. Call **List Project Tasks** to get the task `id`.
2. Enter the task `id` in the path parameter.
3. In the body, include only the fields you want to update.

**Accepted status values:** `todo` | `in_progress` | `done`
**Accepted priority values:** `low` | `medium` | `high`

To change the assignee, provide `assigned_to` with a valid user ID. To change labels, provide `labels` as an array of label IDs (replaces all existing labels).</en>
     * <id>Perbarui field apa pun dari tugas yang sudah ada. Paling umum digunakan untuk mengubah `status`.

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.
>
> **Selain itu:** project induk dari tugas ini harus milik kamu.

**Langkah-langkah:**
1. Panggil **Daftar Tugas dalam Project** untuk mendapatkan `id` tugas.
2. Masukkan `id` tugas di parameter path.
3. Di body, sertakan hanya field yang ingin diperbarui.

**Nilai status yang diterima:** `todo` | `in_progress` | `done`
**Nilai prioritas yang diterima:** `low` | `medium` | `high`

Untuk mengubah assignee, sertakan `assigned_to` dengan ID pengguna yang valid. Untuk mengubah label, sertakan `labels` berupa array ID label (menggantikan semua label yang sudah ada).</id>
     */
    public function update(Request $request, Task $task)
    {
        $task->load('project');
        if ($task->project->user_id !== Auth::id() && $task->assigned_to !== Auth::id()) {
            return $this->error('Unauthorized', 403);
        }

        $validated = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'in:todo,in_progress,done',
            'priority' => 'in:low,medium,high',
            'due_date' => 'nullable|date',
            'assigned_to' => 'nullable|exists:users,id',
            'labels' => 'nullable|array',
            'labels.*' => 'exists:labels,id',
        ]);

        $task->update($validated);

        if (isset($validated['labels'])) {
            // Check if user owns these labels
            $task->labels()->sync($validated['labels']);
        }

        return $this->success(new TaskResource($task->load(['project', 'assignee', 'labels'])), 'Task updated successfully');
    }

    /**
     * <en>Delete Task</en><id>Hapus Tugas</id>
     *
     * <en>Soft delete a task. The record is flagged as deleted but not physically removed.

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.
>
> **Additionally:** the task's parent project must belong to you.

**Steps:**
1. Call **List Project Tasks** to find the task `id` you want to delete.
2. Enter that `id` in the path parameter.
3. Send the request.

The task will no longer appear in **List Project Tasks**, but the record still exists in the database for data integrity purposes.</en>
     * <id>Hapus tugas secara soft delete. Record ditandai sebagai terhapus tapi tidak dihapus secara fisik.

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.
>
> **Selain itu:** project induk dari tugas ini harus milik kamu.

**Langkah-langkah:**
1. Panggil **Daftar Tugas dalam Project** untuk menemukan `id` tugas yang ingin dihapus.
2. Masukkan `id` tersebut di parameter path.
3. Kirim request.

Tugas tidak akan lagi muncul di **Daftar Tugas**, tapi record-nya masih ada di database demi menjaga integritas data.</id>
     */
    public function destroy(Task $task)
    {
        $task->load('project');
        if ($task->project->user_id !== Auth::id()) {
            return $this->error('Unauthorized', 403);
        }

        $task->delete();

        return $this->success(null, 'Task deleted successfully');
    }
}
