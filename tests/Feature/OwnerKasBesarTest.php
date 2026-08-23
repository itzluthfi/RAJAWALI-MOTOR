<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\KasFlow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OwnerKasBesarTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        return User::factory()->create(['peran' => 'owner']);
    }

    private function kasir(): User
    {
        return User::factory()->create(['peran' => 'kasir']);
    }

    public function test_owner_can_access_kas_besar(): void
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

        $response = $this->get('/admin/keuangan/kas-besar');
        $response->assertOk();
        $response->assertSee('Saldo Kas Besar');
        $response->assertSee('500.000');
    }

    public function test_kasir_cannot_access_kas_besar(): void
    {
        $this->actingAs($this->kasir());

        $response = $this->get('/admin/keuangan/kas-besar');
        $response->assertStatus(403);
    }
}
