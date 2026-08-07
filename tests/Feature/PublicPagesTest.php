<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class PublicPagesTest extends TestCase
{
    public function test_halaman_utama_bisa_dibuka(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('Rajawali Motor');
        $response->assertSee('Kalkulator Estimasi Biaya');
    }

    public function test_halaman_detail_layanan_ganti_oli(): void
    {
        $this->get('/layanan/ganti-oli')->assertOk()->assertSee('Ganti Oli');
    }

    public function test_halaman_detail_layanan_tune_up(): void
    {
        $this->get('/layanan/tune-up')->assertOk()->assertSee('Tune Up');
    }

    public function test_halaman_detail_layanan_ban_spooring(): void
    {
        $this->get('/layanan/ban-spooring')->assertOk()->assertSee('Spooring');
    }

    public function test_halaman_detail_layanan_kelistrikan(): void
    {
        $this->get('/layanan/kelistrikan')->assertOk()->assertSee('Kelistrikan');
    }

    public function test_halaman_detail_layanan_injeksi(): void
    {
        $this->get('/layanan/injeksi')->assertOk()->assertSee('Injeksi');
    }

    public function test_halaman_detail_layanan_ac_mobil(): void
    {
        $this->get('/layanan/ac-mobil')->assertOk()->assertSee('AC Mobil');
    }

    public function test_halaman_detail_layanan_body_repair(): void
    {
        $this->get('/layanan/body-repair')->assertOk()->assertSee('Body Repair');
    }

    public function test_sitemap_xml_dan_robots_txt(): void
    {
        $this->get('/sitemap.xml')->assertOk()->assertHeader('Content-Type', 'application/xml');
        $this->get('/robots.txt')->assertOk()->assertSee('Sitemap:');
    }
}
