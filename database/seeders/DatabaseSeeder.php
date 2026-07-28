<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Buat Pengguna
        $user1 = User::factory()->create([
            'name' => 'Sachril (Manajer)',
            'email' => 'default@example.com',
            'password' => bcrypt('default123'),
        ]);

        $user2 = User::factory()->create([
            'name' => 'Andi (Developer)',
            'email' => 'developer@example.com',
            'password' => bcrypt('default123'),
        ]);

        $user3 = User::factory()->create([
            'name' => 'Siti (Designer)',
            'email' => 'designer@example.com',
            'password' => bcrypt('default123'),
        ]);

        // 2. Buat Label untuk Budi
        $labelBug = \App\Models\Label::create(['user_id' => $user1->id, 'name' => '🐛 Bug', 'color' => '#fc8181']);
        $labelFeature = \App\Models\Label::create(['user_id' => $user1->id, 'name' => '✨ Fitur Baru', 'color' => '#63b3ed']);
        $labelUrgent = \App\Models\Label::create(['user_id' => $user1->id, 'name' => '🚨 Urgent', 'color' => '#ed8936']);

        // 3. Buat 15 Project realistis untuk Sachril (Manajer)
        $projects = [
            [
                'name' => 'Redesain Website Perusahaan',
                'description' => 'Memperbarui tampilan dan pengalaman pengguna website korporat agar lebih modern dan responsif.',
                'tasks' => [
                    ['Buat wireframe halaman utama di Figma', 'todo', $user3->id],
                    ['Implementasi desain ke HTML & CSS', 'in_progress', $user2->id],
                    ['Review dan approval desain final oleh tim', 'done', $user1->id],
                ]
            ],
            [
                'name' => 'Pengembangan Aplikasi Mobile NexTask',
                'description' => 'Membangun versi mobile dari platform manajemen tugas menggunakan React Native.',
                'tasks' => [
                    ['Setup project React Native dan konfigurasi environment', 'done', $user2->id],
                    ['Buat halaman login dan registrasi', 'done', $user2->id],
                    ['Integrasi API autentikasi Sanctum', 'in_progress', $user2->id],
                ]
            ],
            [
                'name' => 'Sistem Absensi Karyawan',
                'description' => 'Membangun sistem pencatatan kehadiran berbasis geolokasi dan selfie verification.',
                'tasks' => [
                    ['Desain database schema absensi', 'done', $user1->id],
                    ['Implementasi fitur geolokasi check-in', 'in_progress', $user2->id],
                    ['Buat laporan rekap bulanan otomatis', 'todo', $user1->id],
                ]
            ],
            [
                'name' => 'Migrasi Server ke Cloud AWS',
                'description' => 'Memindahkan seluruh infrastruktur on-premise ke layanan AWS EC2 dan RDS.',
                'tasks' => [
                    ['Audit semua layanan yang berjalan di server lama', 'done', $user1->id],
                    ['Setup VPC dan security group di AWS', 'done', $user2->id],
                    ['Uji coba performa setelah migrasi', 'in_progress', $user1->id],
                ]
            ],
            [
                'name' => 'Kampanye Marketing Q3 2025',
                'description' => 'Merencanakan dan mengeksekusi kampanye digital marketing untuk kuartal ketiga.',
                'tasks' => [
                    ['Riset target audiens dan kompetitor', 'done', $user3->id],
                    ['Buat konten media sosial untuk bulan Juli–September', 'in_progress', $user3->id],
                    ['Setup dan monitoring iklan Google Ads', 'todo', $user1->id],
                ]
            ],
            [
                'name' => 'Onboarding Karyawan Baru — Batch 3',
                'description' => 'Menyiapkan materi, jadwal, dan alat untuk proses orientasi karyawan baru gelombang ketiga.',
                'tasks' => [
                    ['Siapkan modul pelatihan digital', 'done', $user3->id],
                    ['Jadwalkan sesi perkenalan dengan semua divisi', 'done', $user1->id],
                    ['Evaluasi hasil onboarding dan kumpulkan feedback', 'todo', $user3->id],
                ]
            ],
            [
                'name' => 'Integrasi Payment Gateway Midtrans',
                'description' => 'Menghubungkan sistem pembelian produk dengan layanan pembayaran Midtrans.',
                'tasks' => [
                    ['Daftar akun dan verifikasi bisnis di Midtrans', 'done', $user1->id],
                    ['Implementasi Midtrans Snap pada halaman checkout', 'in_progress', $user2->id],
                    ['Pengujian transaksi di sandbox environment', 'todo', $user2->id],
                ]
            ],
            [
                'name' => 'Audit Keamanan Sistem',
                'description' => 'Melakukan penetration testing dan review keamanan pada seluruh layanan API publik.',
                'tasks' => [
                    ['Identifikasi endpoint yang rentan terhadap SQL injection', 'done', $user2->id],
                    ['Uji coba brute force pada endpoint autentikasi', 'in_progress', $user2->id],
                    ['Buat laporan temuan dan rekomendasi perbaikan', 'todo', $user1->id],
                ]
            ],
            [
                'name' => 'Dokumentasi API Internal',
                'description' => 'Membuat dokumentasi lengkap untuk semua endpoint API yang digunakan oleh tim internal.',
                'tasks' => [
                    ['Kumpulkan semua endpoint yang belum terdokumentasi', 'done', $user2->id],
                    ['Tulis dokumentasi menggunakan standar OpenAPI 3.1', 'in_progress', $user2->id],
                    ['Review dokumentasi bersama tim frontend', 'todo', $user3->id],
                ]
            ],
            [
                'name' => 'Dashboard Analitik Penjualan',
                'description' => 'Membangun dashboard real-time untuk memantau performa penjualan harian, mingguan, dan bulanan.',
                'tasks' => [
                    ['Tentukan KPI dan metrik utama yang ditampilkan', 'done', $user1->id],
                    ['Desain tampilan dashboard dengan Chart.js', 'in_progress', $user3->id],
                    ['Sambungkan data ke API backend dan uji akurasi', 'todo', $user2->id],
                ]
            ],
            [
                'name' => 'Peningkatan Performa Aplikasi',
                'description' => 'Mengoptimalkan waktu muat halaman dan respons API untuk meningkatkan pengalaman pengguna.',
                'tasks' => [
                    ['Profiling database query yang lambat menggunakan Laravel Telescope', 'done', $user2->id],
                    ['Implementasi caching Redis pada endpoint yang sering diakses', 'in_progress', $user2->id],
                    ['Optimasi aset frontend (lazy loading, image compression)', 'todo', $user3->id],
                ]
            ],
            [
                'name' => 'Fitur Notifikasi Real-Time',
                'description' => 'Menambahkan sistem notifikasi push dan in-app menggunakan WebSocket dan Laravel Echo.',
                'tasks' => [
                    ['Setup Pusher dan konfigurasi Laravel Echo', 'done', $user2->id],
                    ['Implementasi notifikasi saat task di-assign ke pengguna', 'in_progress', $user2->id],
                    ['Uji coba notifikasi pada berbagai browser dan perangkat', 'todo', $user1->id],
                ]
            ],
            [
                'name' => 'Program Loyalitas Pelanggan',
                'description' => 'Merancang dan meluncurkan program poin reward untuk meningkatkan retensi pelanggan.',
                'tasks' => [
                    ['Riset model program loyalitas kompetitor', 'done', $user3->id],
                    ['Desain mekanisme penukaran poin', 'in_progress', $user3->id],
                    ['Implementasi sistem poin di backend', 'todo', $user2->id],
                ]
            ],
            [
                'name' => 'Refactoring Modul Laporan',
                'description' => 'Memperbaiki dan menyederhanakan kode pada modul pembuatan laporan yang sudah usang.',
                'tasks' => [
                    ['Identifikasi bagian kode yang perlu diubah', 'done', $user2->id],
                    ['Tulis unit test sebelum melakukan perubahan', 'in_progress', $user2->id],
                    ['Lakukan refactor dan pastikan semua test lolos', 'todo', $user2->id],
                ]
            ],
            [
                'name' => 'Event Tahunan Tech Summit',
                'description' => 'Koordinasi logistik, pembicara, dan materi untuk acara konferensi teknologi tahunan perusahaan.',
                'tasks' => [
                    ['Konfirmasi dan hubungi pembicara tamu', 'done', $user1->id],
                    ['Siapkan materi presentasi dan rundown acara', 'in_progress', $user3->id],
                    ['Koordinasi dengan tim venue dan katering', 'todo', $user1->id],
                ]
            ],
        ];

        $allLabels = [$labelBug->id, $labelFeature->id, $labelUrgent->id];

        foreach ($projects as $projectData) {
            $project = \App\Models\Project::create([
                'user_id' => $user1->id,
                'name' => $projectData['name'],
                'description' => $projectData['description'],
            ]);

            foreach ($projectData['tasks'] as $taskData) {
                $task = \App\Models\Task::create([
                    'project_id' => $project->id,
                    'title' => $taskData[0],
                    'status' => $taskData[1],
                    'assigned_to' => $taskData[2],
                ]);

                if (rand(0, 1)) {
                    $task->labels()->attach(
                        collect($allLabels)->random(rand(1, 2))->toArray()
                    );
                }
            }
        }
    }
}
