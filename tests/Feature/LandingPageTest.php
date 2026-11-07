<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LandingPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_is_a_landing_page_and_catalog_remains_available_separately(): void
    {
        $category = Category::create([
            'name' => 'Elektronik (Laptop, HP, Charger)',
            'slug' => 'elektronik',
            'icon' => 'usb',
        ]);
        $user = User::factory()->create();

        Item::create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'type' => 'found',
            'title' => 'Earbuds Hitam',
            'description' => 'Ditemukan di perpustakaan.',
            'location' => 'Perpustakaan',
            'date_event' => now()->toDateString(),
            'security_question' => 'Apa warna kotaknya?',
            'security_answer' => 'Putih',
            'status' => 'approved',
        ]);

        $this->get(route('home'))
            ->assertOk()
            ->assertSee('Cari barang hilang')
            ->assertSee('<title>Lost &amp; Found | Barang hilang dan temuan</title>', false)
            ->assertDontSee('Kenali alurnya')
            ->assertSee('Earbuds Hitam')
            ->assertSee(route('catalog.index'));

        $this->get(route('catalog.index'))
            ->assertOk()
            ->assertSee('class="catalog-page"', false)
            ->assertSee('area kampus')
            ->assertDontSee('Portal resmi kampus')
            ->assertSee('item__category-label')
            ->assertSee('Elektronik (Laptop, HP, Charger)')
            ->assertSee('Filter Barang');
    }
}
