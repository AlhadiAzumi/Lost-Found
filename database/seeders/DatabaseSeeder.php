<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        // 1. Buat Akun Admin Kampus
        User::create([
            'name' => 'Administrator Kampus',
            'email' => 'admin@campus.ac.id',
            'nim_nip' => 'ADM001',
            'password' => Hash::make('password123'),
            'role' => 'admin',
        ]);

        // 2. Buat Akun Satpam Pos
        User::create([
            'name' => 'Pak Satpam Pos Utama',
            'email' => 'satpam@campus.ac.id',
            'nim_nip' => 'STP001',
            'password' => Hash::make('password123'),
            'role' => 'satpam',
        ]);

        // 3. Buat Akun Mahasiswa
        User::create([
            'name' => 'Mahasiswa Pelapor',
            'email' => 'mahasiswa@student.ac.id',
            'nim_nip' => '23456789',
            'password' => Hash::make('password123'),
            'role' => 'mahasiswa',
        ]);

        // 4. Masukkan Data Kategori Barang
        $categories = [
            ['name' => 'Elektronik (Laptop, HP, Charger)', 'slug' => 'elektronik', 'icon' => 'laptop'],
            ['name' => 'Dokumen Penting (KTM, KTP, SIM)', 'slug' => 'dokumen-penting', 'icon' => 'id-card'],
            ['name' => 'Dompet & Tas', 'slug' => 'dompet-tas', 'icon' => 'briefcase'],
            ['name' => 'Aksesoris & Perhiasan', 'slug' => 'aksesoris', 'icon' => 'watch'],
            ['name' => 'Kunci Kendaraan', 'slug' => 'kunci-kendaraan', 'icon' => 'key'],
            ['name' => 'Lainnya', 'slug' => 'lainnya', 'icon' => 'box'],
        ];

        foreach ($categories as $cat) {
            Category::create($cat);
        }
    }
}