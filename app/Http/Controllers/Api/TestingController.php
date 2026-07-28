<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

class TestingController extends Controller
{
    use ApiResponse;

    /**
     * <en>Reset Database (Local Only)</en><id>Reset Database (Khusus Lokal)</id>
     *
     * <en>Wipe the entire database and re-seed it with fresh dummy data. Useful when test data has become too messy to work with.

> **No authentication required.** Just send the request directly.

> ⚠️ **This completely deletes all data** — all projects, tasks, labels, and users will be wiped and replaced with fresh seed data. There is no undo.

**After the reset, the default test account is:**
- Email: `default@example.com`
- Password: `default123`

This endpoint is **completely disabled in production** — if `APP_ENV` is set to `production`, the request will return `403 Forbidden` and will not appear in the public documentation.</en>
     * <id>Hapus seluruh data database dan isi ulang dengan data dummy yang segar. Berguna kalau data uji coba sudah terlalu berantakan.

> **Tidak perlu autentikasi.** Langsung kirim requestnya saja.

> ⚠️ **Ini menghapus semua data** — semua project, tugas, label, dan pengguna akan terhapus dan digantikan dengan data bawaan yang baru. Tidak ada cara untuk membatalkan.

**Setelah reset, akun pengujian berikut tersedia:**
- Email: `default@example.com`
- Password: `default123`

Endpoint ini **sepenuhnya dinonaktifkan di production** — jika `APP_ENV` diset ke `production`, request akan mendapat `403 Forbidden` dan endpoint ini tidak akan muncul di dokumentasi publik.</id>
     */
    public function resetDatabase(Request $request)
    {
        if (app()->environment('production')) {
            return $this->error('Action not allowed in production environment.', 403);
        }

        Artisan::call('migrate:fresh', ['--seed' => true]);

        return $this->success(null, 'Database has been successfully wiped and re-seeded. You can now login with default@example.com (password: default123)');
    }
}
