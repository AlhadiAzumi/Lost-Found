<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@campus.ac.id'],
            [
                'name' => 'Admin Lost & Found',
                'password' => bcrypt(Str::random(40)),
                'role' => 'admin',
            ]
        );
        if ($user->role !== 'admin') {
            $user->update(['role' => 'admin']);
        }

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
            $cat = Category::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name), 'icon' => $icon]
            );
            $categoryMap[$name] = $cat->id;
        }

        Item::create([
            'user_id' => $user->id,
            'category_id' => $categoryMap['Elektronik'] ?? 1,
            'type' => 'lost',
            'title' => 'Flashdisk Sandisk 32GB Merah',
            'description' => 'Tertinggal di meja laboratorium komputer lab 3 setelah praktikum pemrograman.',
            'location' => 'Gedung F - Lab Komputer 3',
            'date_event' => now()->subDays(2)->toDateString(),
            'status' => 'approved',
        ]);

        Item::create([
            'user_id' => $user->id,
            'category_id' => $categoryMap['Kunci'] ?? 3,
            'type' => 'found',
            'title' => 'Gantungan Kunci Motor Honda',
            'description' => 'Ditemukan tergantung di loker lantai 1 dekat kantin.',
            'location' => 'Kantin Utama',
            'date_event' => now()->subDay()->toDateString(),
            'security_question' => 'Apa karakter pada gantungan kunci?',
            'security_answer' => 'Anime',
            'status' => 'approved',
        ]);
    }
}
