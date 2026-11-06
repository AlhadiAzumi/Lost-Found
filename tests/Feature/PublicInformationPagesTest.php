<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicInformationPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_public_information_pages_are_available_to_guests(): void
    {
        $pages = [
            ['pages.about', 'Tentang layanan Lost & Found', 'Tentang layanan | Lost &amp; Found'],
            ['pages.guide', 'Panduan penggunaan', 'Panduan | Lost &amp; Found'],
            ['pages.safety', 'Keamanan saat melapor dan mengambil barang', 'Keamanan | Lost &amp; Found'],
            ['pages.privacy', 'Privasi dan data pengguna', 'Privasi | Lost &amp; Found'],
            ['pages.contact', 'Bantuan dan kontak', 'Bantuan dan kontak | Lost &amp; Found'],
            ['pages.terms', 'Ketentuan penggunaan layanan', 'Ketentuan penggunaan | Lost &amp; Found'],
        ];

        foreach ($pages as [$route, $heading, $browserTitle]) {
            $this->get(route($route))
                ->assertOk()
                ->assertSee($heading)
                ->assertSee('<title>'.$browserTitle.'</title>', false)
                ->assertSee(route('pages.contact'))
                ->assertDontSee('info-page__eyebrow', false)
                ->assertDontSee(' — ', false)
                ->assertDontSee('Politeknik Negeri Lhokseumawe')
                ->assertDontSee('Lost &amp; Found PNL', false)
                ->assertDontSee('Lost & Found PNL', false);
        }
    }

    public function test_contact_page_uses_supplied_service_details(): void
    {
        $this->get(route('pages.contact'))
            ->assertOk()
            ->assertSee('lostandfoundpnl@gmail.com')
            ->assertSee('0851-6781-1268')
            ->assertSee('08.00–17.00 WIB')
            ->assertDontSee('pnl.ac.id');
    }

    public function test_information_pages_are_linked_from_the_desktop_navigation(): void
    {
        $this->get(route('pages.contact'))
            ->assertOk()
            ->assertSee('Tentang', false)
            ->assertSee('Keamanan', false)
            ->assertSee('Bantuan', false)
            ->assertSee('Lainnya', false)
            ->assertSee(route('pages.guide'))
            ->assertSee(route('pages.privacy'))
            ->assertSee(route('pages.terms'))
            ->assertSee('images/logo-mark.jpg')
            ->assertSee('alt="" class="brand__logo-img"', false)
            ->assertDontSee('Politeknik Negeri Lhokseumawe');
    }

    public function test_privacy_page_discloses_unfinalized_policy_and_public_claim_storage(): void
    {
        $this->get(route('pages.privacy'))
            ->assertOk()
            ->assertSee('Kebijakan retensi belum ditetapkan kampus')
            ->assertSee('storage publik')
            ->assertSee('Informasi yang terlihat di katalog publik');
    }
}
