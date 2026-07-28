<?php

use Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess;

return [
    /*
     * Which routes to document. String or array form; use Scramble::routes() for custom selection.
     *
     * 'api_path' => [
     *     'include' => 'api',
     *     'exclude' => ['api/internal'],
     * ],
     *
     * Without *, patterns match path segments (api matches api and api/users, not apiary).
     * With *, Str::is is used (e.g. api/v*).
     *
     * One static include → default server is /{include} and paths are stripped (/users).
     * Multiple includes or wildcards → server defaults to / and paths stay full (/api/users).
     * Override with `servers`, or use Scramble::registerApi() for separate bases.
     */
    'api_path' => 'api',

    /*
     * Your API domain. By default, app domain is used. This is also a part of the default API routes
     * matcher, so when implementing your own, make sure you use this config if needed.
     */
    'api_domain' => null,

    /*
     * The path where your OpenAPI specification will be exported.
     */
    'export_path' => 'api.json',

    /*
     * Cache configuration for the generated OpenAPI document.
     *
     * Use `scramble:cache` to warm the cache and `scramble:clear` to invalidate it.
     */
    'cache' => [
        'key' => 'scramble.openapi',
        'store' => 'file',
    ],

    'info' => [
        /*
         * API version.
         */
        'version' => env('API_VERSION', '1.0.0'),

        /*
         * Description rendered on the home page of the API documentation (`/docs/api`).
         */
        'description' => '<en>
Welcome to the interactive documentation for the **Task Management REST API** — a fully functional API built with **Laravel 13**, **PHP 8.4**, **SQLite**, and **Laravel Sanctum** for token-based authentication.

---

### 📖 About This Application
This API allows users to manage their work through three core resources:
- **Projects** — containers for a group of related tasks
- **Tasks** — individual work items inside a project, which can be assigned to another user and labeled
- **Labels** — custom color-coded tags for organizing tasks

Every resource is **privately owned** — each user only sees and manages their own data. There is no shared workspace between users, unless a task is explicitly assigned to another user.

---

### 🔐 How Authentication Works
This API uses **Laravel Sanctum token-based authentication**. When you login, the server returns a **Bearer token** in the response body.

### 🌍 Environments & Routing
This application behaves differently depending on your active environment (`APP_ENV`):
- **Production (`production`)**: The API documentation is disabled. Accessing the root URL (`/`) automatically redirects users to the **Main Standalone Dashboard** at `/dashboard`.
- **Non-Production (`local`, etc)**: Accessing the root URL (`/`) automatically redirects to this API documentation page.

### 🚀 How to Test This API
You can test and interact with this API in the following ways:

1. **Visually via the Main Dashboard (Standalone)**
   You can access the full standalone dashboard by manually typing the `/dashboard` route into your browser address bar after your domain name (e.g., `http://localhost:8000/dashboard`) when in non-production.
2. **Visually via the Popup Panel (Here)**
   Click the **🗂️ Open Dashboard** button in the bottom-right corner of this page to open an interactive panel overlaid directly on the documentation.
3. **Via "Try It" Cards (Stoplight UI)**
   Click on any endpoint (like `POST /api/auth/login`) in the sidebar, fill in the request body, and click the **Send** button. 
   *(Note: Once you log in, this page automatically captures your token and injects it into all subsequent requests. No manual copy-pasting required.)*
4. **Via Command Line (cURL) or HTTP Clients**
   You can copy the generated cURL commands or use tools like Postman. Remember to manually pass the `Authorization: Bearer <token>` header for protected routes.

> The session status widget at the bottom-right of this page shows whether you are currently logged in.

**Default Test Credentials:**
- Email: `default@example.com`
- Password: `default123`

---

### 🔒 Ownership & Access Rules
The following rules are enforced strictly by the API:
- **Projects**: Only visible, editable, and deletable by their creator.
- **Tasks**: Nested under projects — you can only access tasks in your own projects. The `assigned_to` field lets you delegate a task to another user.
- **Labels**: Belong to the creator only. Other users cannot see or use your labels.
- **User profiles**: Any logged-in user can see the public profile (name only, email is hidden) of others. However, updating or deleting a user account is only allowed for the account owner.

---

### ♻️ Reset Database (Development Only)
A special endpoint `POST /api/testing/reset-database` is available in local/development environments to wipe the database and reload the default seed data.

**After a reset, the following test account is available:**
- Email: `default@example.com`
- Password: `default123`

> This endpoint is permanently disabled in production environments and returns `403 Forbidden` if called.

---

### 🏗️ Architecture & Structure
This project strictly follows the **MVC (Model-View-Controller)** pattern combined with **RESTful API** design principles.

**Core Directory Structure:**
- `app/Http/Controllers/Api/` — Contains all API logic. Controllers are kept slim using FormRequests for validation and Eloquent for data access.
- `app/Models/` — Eloquent models defining relationships (`User`, `Project`, `Task`, `Label`).
- `routes/api.php` — API endpoints routing (grouped by `auth:sanctum` middleware).
- `database/migrations/` — Database schema definitions.
- `database/seeders/` — Fake data generation for testing.
- `tests/Feature/` — Automated API endpoint testing ensuring functionality.

---

### 📚 About This Documentation
This documentation is rendered using **Stoplight Elements**, powered by **Scramble** (an automatic OpenAPI 3.1 spec generator for Laravel).

**Important notes about this documentation page:**
- It is only accessible in **local and staging environments**.
- In **production**, this documentation page is completely disabled and returns `404 Not Found`. The API itself continues to work normally.
- The "Try It" feature (Send button on each endpoint) sends real HTTP requests to the running server. Make sure the server is running when testing.
- Language can be toggled between **Bahasa Indonesia** and **English** using the button at the top-right corner.

---

### 💻 Local Installation
```bash
git clone https://github.com/yourusername/task-management-api.git
cd task-management-api
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```
</en>
<id>
Selamat datang di dokumentasi interaktif **Task Management REST API** — sebuah API fungsional penuh yang dibangun dengan **Laravel 13**, **PHP 8.4**, **SQLite**, dan **Laravel Sanctum** untuk autentikasi berbasis token.

---

### 📖 Tentang Aplikasi Ini
API ini memungkinkan pengguna mengelola pekerjaan mereka melalui tiga sumber daya utama:
- **Projects** — wadah untuk sekumpulan tugas yang berkaitan
- **Tasks** — item pekerjaan individual di dalam sebuah project, yang bisa didelegasikan ke pengguna lain dan diberi label
- **Labels** — tag berwarna kustom untuk mengorganisasi tugas

Setiap sumber daya **dimiliki secara privat** — setiap pengguna hanya bisa melihat dan mengelola datanya sendiri. Tidak ada ruang kerja bersama antar pengguna, kecuali tugas yang secara eksplisit didelegasikan ke pengguna lain.

---

### 🔐 Cara Kerja Autentikasi
API ini menggunakan **autentikasi berbasis token Laravel Sanctum**. Saat login, server mengembalikan sebuah **Bearer token** di dalam body respons.

### 🌍 Lingkungan (Environment) & Akses Dashboard
Aplikasi ini menyesuaikan tampilannya berdasarkan lingkungan aktif (`APP_ENV`):
- **Production (`production`)**: Dokumentasi API ini dinonaktifkan. Mengakses URL utama (`/`) akan otomatis mengarahkan pengguna ke **Dashboard Utama (Standalone)** di rute `/dashboard`.
- **Non-Production (`local`, dll)**: Mengakses URL utama (`/`) akan otomatis mengarahkan ke halaman dokumentasi API ini.

### 🚀 Cara Mencoba API Ini
Kamu dapat berinteraksi dengan API ini melalui cara-cara berikut:

1. **Secara Visual via Dashboard Utama (Standalone)**
   Jika kamu sedang berada di mode non-production, kamu tetap bisa mengakses dashboard utama secara manual dengan mengetikkan `/dashboard` langsung di *address bar* browsermu (contoh: `http://localhost:8000/dashboard`).
2. **Secara Visual via Panel Popup (Di Halaman Ini)**
   Klik tombol **🗂️ Buka Dashboard** di pojok kanan bawah halaman dokumentasi ini untuk membuka panel interaktif melayang.
3. **Via Kartu "Try It" (UI Stoplight)**
   Klik pada endpoint apa pun di sidebar, isi request, lalu klik tombol **Send**. 
   *(Catatan: Setelah login, halaman ini otomatis menyisipkan token ke semua request. Tidak perlu copy-paste token manual.)*
4. **Via Command Line (cURL) atau Klien HTTP**
   Gunakan perintah cURL yang disediakan atau klien seperti Postman (jangan lupa sertakan header `Authorization: Bearer <token>`).

> Widget status sesi di pojok kanan bawah halaman ini menampilkan apakah kamu sedang dalam keadaan login atau tidak.

**Kredensial Bawaan (Pengujian):**
- Email: `default@example.com`
- Password: `default123`

---

### 🔒 Aturan Kepemilikan & Akses
Aturan-aturan berikut diberlakukan secara ketat oleh API:
- **Projects**: Hanya bisa dilihat, diedit, dan dihapus oleh pembuatnya.
- **Tasks**: Bersarang di dalam project — kamu hanya bisa mengakses tugas di dalam project milikmu. Field `assigned_to` memungkinkan kamu mendelegasikan tugas ke pengguna lain.
- **Labels**: Milik pembuatnya. Pengguna lain tidak bisa melihat atau menggunakan label milikmu.
- **Profil pengguna**: Semua pengguna yang sudah login bisa melihat profil publik (hanya nama, email disembunyikan) milik pengguna lain. Namun, memperbarui atau menghapus akun hanya diizinkan untuk pemilik akun itu sendiri.

---

### ♻️ Reset Database (Khusus Pengembangan)
Endpoint khusus `POST /api/testing/reset-database` tersedia di lingkungan lokal/pengembangan untuk menghapus seluruh database dan memuat ulang data bawaan (seed).

**Setelah reset, akun pengujian berikut tersedia:**
- Email: `default@example.com`
- Password: `default123`

> Endpoint ini dinonaktifkan secara permanen di lingkungan production dan akan mengembalikan `403 Forbidden` jika dipanggil.

---

### 🏗️ Arsitektur & Susunan Folder
Proyek ini menerapkan pola **MVC (Model-View-Controller)** standar Laravel yang dipadukan dengan prinsip desain **RESTful API**.

**Struktur Direktori Utama:**
- `app/Http/Controllers/Api/` — Berisi seluruh logika HTTP API. Controller dibuat ramping (slim) dengan mendelegasikan validasi ke `FormRequest` dan akses data ke Eloquent.
- `app/Models/` — Model Eloquent yang mendefinisikan relasi database (`User`, `Project`, `Task`, `Label`).
- `routes/api.php` — Berisi routing semua endpoint API (dilindungi oleh middleware `auth:sanctum`).
- `database/migrations/` — Definisi skema pembuatan tabel database.
- `database/seeders/` — Pembuat data palsu (*dummy data*) otomatis untuk kemudahan pengujian.
- `tests/Feature/` — Tempat skrip pengujian otomatis (*automated testing*) untuk memastikan semua endpoint berfungsi 100%.

---

### 📚 Tentang Halaman Dokumentasi Ini
Dokumentasi ini dirender menggunakan **Stoplight Elements**, yang didukung oleh **Scramble** (generator OpenAPI 3.1 otomatis untuk Laravel).

**Catatan penting tentang halaman dokumentasi ini:**
- Hanya bisa diakses di lingkungan **lokal dan staging**.
- Di lingkungan **production**, halaman dokumentasi ini sepenuhnya dinonaktifkan dan mengembalikan `404 Not Found`. API itu sendiri tetap berjalan normal.
- Fitur "Try It" (tombol Send di setiap endpoint) mengirimkan request HTTP nyata ke server yang berjalan. Pastikan server sudah aktif saat mengujicoba.
- Bahasa dapat diganti antara **Bahasa Indonesia** dan **English** menggunakan tombol di pojok kanan atas.

---

### 💻 Instalasi Lokal
```bash
git clone https://github.com/yourusername/task-management-api.git
cd task-management-api
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate --seed
php artisan serve
```
</id>',
    ],

    'ui' => [
        'title' => 'NexTask API',
        'theme' => 'dark',
        'hide_try_it' => false,
        'logo' => '',
    ],

    'renderer' => 'elements',

    'renderers' => [
        /*
         * Stoplight Elements config options: https://docs.stoplight.io/docs/elements/b074dc47b2826-elements-configuration-options
         */
        'elements' => [
            'view' => 'scramble::docs',
            'theme' => 'dark',
            'hideTryIt' => false,
            'hideSchemas' => false,
            'logo' => '',
            'tryItCredentialsPolicy' => 'include',
            'layout' => 'responsive',
            'router' => 'hash',
        ],
        /*
         * Scalar API reference config options: https://scalar.com/products/api-references/configuration
         */
        'scalar' => [
            'view' => 'scramble::scalar',
            'cdn' => 'https://cdn.jsdelivr.net/npm/@scalar/api-reference',
            'theme' => 'laravel',
            'proxyUrl' => 'https://proxy.scalar.com',
            'darkMode' => false,
            'showDeveloperTools' => 'never',
            'agent' => ['disabled' => true],
            'credentials' => 'include',
        ],
    ],

    /*
     * The list of servers of the API. By default, when `null`, server URL will be created from
     * `scramble.api_path` and `scramble.api_domain` config variables. When providing an array, you
     * will need to specify the local server URL manually (if needed).
     */
    'servers' => null,

    'enum_cases_description_strategy' => 'description',
    'enum_cases_names_strategy' => false,
    'flatten_deep_query_parameters' => true,

    'middleware' => [
        'web',
        \Dedoc\Scramble\Http\Middleware\RestrictedDocsAccess::class,
        \App\Http\Middleware\BilingualApiDocsMiddleware::class,
    ],

    'extensions' => [],
    'security_strategy' => null,
];
