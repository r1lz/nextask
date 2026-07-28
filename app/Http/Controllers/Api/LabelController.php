<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Label;
use App\Traits\ApiResponse;
use App\Http\Resources\LabelResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LabelController extends Controller
{
    use ApiResponse;

    /**
     * <en>List Labels</en><id>Daftar Label</id>
     *
     * <en>Retrieve all custom labels you have created. Labels can be attached to tasks to categorize them (e.g., "Bug", "Feature", "Urgent"). Each label has a `name` and a hex `color` code.

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.

You need the label `id` values from this list when assigning labels to tasks via **Create Task** or **Update Task**.</en>
     * <id>Mengambil semua label kustom yang kamu buat. Label dapat ditempelkan ke tugas untuk mengkategorikannya (misal: "Bug", "Fitur", "Urgent"). Setiap label memiliki `name` dan kode warna hex `color`.

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.

Kamu butuh nilai `id` dari label-label ini ketika menambahkan label ke tugas melalui **Tambah Tugas Baru** atau **Perbarui Tugas**.</id>
     */
    public function index()
    {
        $labels = Auth::user()->labels()->latest()->get();
        return $this->success(
            LabelResource::collection($labels),
            'Labels retrieved successfully'
        );
    }

    /**
     * <en>Create Label</en><id>Buat Label Baru</id>
     *
     * <en>Create a new custom label.

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.

**Request body:**
- `name` (required): Label name, e.g. `"Critical"` or `"Needs Review"`
- `color` (required): Hex color code, e.g. `"#FF4040"` or `"#2ECC71"`

After creating a label, copy its `id` from the response. You'll need it when assigning labels to tasks.</en>
     * <id>Buat label kustom baru.

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.

**Request body:**
- `name` (wajib): Nama label, misal `"Kritis"` atau `"Perlu Review"`
- `color` (wajib): Kode warna hex, misal `"#FF4040"` atau `"#2ECC71"`

Setelah membuat label, salin `id`-nya dari respons. Kamu butuh itu saat menambahkan label ke tugas.</id>
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'color' => 'nullable|string|max:7',
        ]);

        $label = Auth::user()->labels()->create($validated);

        return $this->success(
            new LabelResource($label),
            'Label created successfully',
            201
        );
    }

    /**
     * <en>Delete Label</en><id>Hapus Label</id>
     *
     * <en>Permanently delete a label by its ID. When deleted, it is automatically detached from all tasks that were using it.

> **Authentication required.**
> To try this endpoint here, make sure you have successfully logged in via the **`POST /api/auth/login`** endpoint first. Once logged in, the system automatically remembers your session via cookies, so you can test this endpoint directly without needing to copy or paste any tokens.
>
> **Additionally:** the label must belong to you.

**Steps:**
1. Call **List Labels** to see your labels and their IDs.
2. Copy the `id` of the label you want to delete.
3. Enter it in the path parameter and send.

⚠️ This action cannot be undone.</en>
     * <id>Hapus label secara permanen berdasarkan ID-nya. Ketika dihapus, label otomatis dilepas dari semua tugas yang menggunakannya.

> **Butuh autentikasi.**
> Untuk mencoba endpoint ini, pastikan kamu sudah berhasil login melalui endpoint **`POST /api/auth/login`** terlebih dahulu. Setelah login berhasil, sistem akan otomatis mengingat sesimu lewat cookie, sehingga kamu bisa langsung mencoba endpoint ini tanpa perlu repot memasukkan token apa pun.
>
> **Selain itu:** label tersebut harus milik kamu.

**Langkah-langkah:**
1. Panggil **Daftar Label** untuk melihat label-label kamu dan ID-nya.
2. Salin `id` dari label yang ingin dihapus.
3. Masukkan di parameter path, lalu kirim.

⚠️ Aksi ini tidak bisa dibatalkan.</id>
     */
    public function destroy(Label $label)
    {
        if ($label->user_id !== Auth::id()) {
            return $this->error('Unauthorized', 403);
        }

        $label->delete();

        return $this->success(null, 'Label deleted successfully');
    }
}
