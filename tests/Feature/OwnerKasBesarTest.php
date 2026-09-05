<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\KasFlow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class OwnerKasBesarTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        return User::factory()->create(['username' => 'owner_' . Str::random(6), 'peran' => 'owner']);
    }

    private function kasir(): User
    {
        return User::factory()->create(['username' => 'kasir_' . Str::random(6), 'peran' => 'kasir']);
    }

    public function test_owner_can_access_buku_kas_utama(): void
    {
        $this->actingAs($this->owner());

        KasFlow::create([
            'tanggal' => now()->toDateString(),
            'tipe' => 'masuk',
            'sumber' => 'kas',
            'kategori' => 'penjualan',
            'nominal' => 500000,
            'keterangan' => 'Kas Masuk Penjualan',
        ]);

        $response = $this->get('/admin/keuangan/kas');
        $response->assertOk();
        $response->assertSee('Buku Kas Utama (Owner)');
        $response->assertSee('500.000');
    }

    public function test_kas_besar_redirects_to_kas_utama_for_owner(): void
    {
        $this->actingAs($this->owner());

        $response = $this->get('/admin/keuangan/kas-besar');
        $response->assertRedirect(route('keuangan.kas'));
    }

    public function test_kasir_cannot_access_buku_kas_utama_or_kas_besar(): void
    {
        $this->actingAs($this->kasir());

        $this->get('/admin/keuangan/kas')->assertStatus(403);
        $this->get('/admin/keuangan/kas-besar')->assertStatus(403);
    }
}
