<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
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
        $user = User::factory()->create([
            'name' => 'Admin Lost & Found',
            'email' => 'admin@campus.ac.id',
        ]);

        $categoryMap = [];
        $categories = [
            ['Elektronik', 'usb',       '#fce7f6'],
            ['Dompet',     'tag',       '#dbeafe'],
            ['Kunci',      'key-round', '#ecfccb'],
            ['Tas',        'backpack',  '#d1fae8'],
            ['Lainnya',    'tag',       '#fee2e2'],
            ['Alat Tulis', 'tag',       '#ede9fe'],
        ];

        foreach ($categories as [$name, $icon, $color]) {
            $cat = Category::firstOrCreate(['name' => $name], compact('icon', 'color'));
            $categoryMap[$name] = $cat->id;
        }

        Item::create([
            'user_id' => $user->id,
            'category_id' => $categoryMap['Elektronik'] ?? 1,
            'type' => 'hilang',
            'name' => 'Flashdisk Sandisk 32GB Merah',
            'description' => 'Tertinggal di meja laboratorium komputer lab 3 setelah praktikum pemrograman.',
            'location' => 'Gedung F - Lab Komputer 3',
            'occurred_on' => now()->subDays(2)->toDateString(),
            'status' => 'disetujui',
        ]);

        Item::create([
            'user_id' => $user->id,
            'category_id' => $categoryMap['Kunci'] ?? 3,
            'type' => 'ditemukan',
            'name' => 'Gantungan Kunci Motor Honda + Karakter Anime',
            'description' => 'Ditemukan tergantung di loker lantai 1 dekat kantin.',
            'location' => 'Kantin Utama',
            'occurred_on' => now()->subDay()->toDateString(),
            'status' => 'disetujui',
        ]);
    }
}

