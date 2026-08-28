<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Group;
use App\Models\Pembelian;
use App\Models\PembelianDetail;
use App\Models\Satuan;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PembelianCicilanTest extends TestCase
{
    use RefreshDatabase;

    public function test_pembelian_tempo_can_be_paid_in_installments_and_tracks_history(): void
    {
        $user = User::create([
            'name' => 'Admin Toko',
            'username' => 'admin_test',
            'email' => 'admin@rajawali.com',
            'password' => bcrypt('password'),
            'peran' => 'admin',
            'aktif' => true,
        ]);

        $supplier = Supplier::create([
            'nama' => 'CV Sinar Motor Jaya',
            'telepon' => '08123456789',
        ]);

        $pembelian = Pembelian::create([
            'nomor_pembelian' => 'PB2026080002',
            'supplier_id' => $supplier->id,
            'user_id' => $user->id,
            'tanggal' => now()->subDays(10)->toDateString(),
            'total' => 1200000,
            'terbayar' => 0,
            'status_bayar' => 'tempo',
            'jatuh_tempo' => now()->subDays(10)->addDays(30)->toDateString(),
        ]);

        $this->actingAs($user);

        // 1. Cicilan Pertama: Rp 500.000
        $response1 = $this->post(route('pembelian.pelunasan', $pembelian), [
            'nominal_bayar' => 500000,
            'tanggal_bayar' => now()->format('Y-m-d H:i:s'),
            'sumber' => 'kas',
            'keterangan' => 'Cicilan pertama DP',
        ]);

        $response1->assertRedirect();
        $pembelian->refresh();

        $this->assertEquals(500000, $pembelian->terbayar);
        $this->assertEquals(700000, $pembelian->sisa_hutang);
        $this->assertEquals('tempo', $pembelian->status_bayar);
        $this->assertNull($pembelian->tanggal_lunas);
        $this->assertCount(1, $pembelian->pembayarans);

        // 2. Cicilan Kedua (Pelunasan Penuh Sisa Rp 700.000)
        $response2 = $this->post(route('pembelian.pelunasan', $pembelian), [
            'nominal_bayar' => 700000,
            'tanggal_bayar' => now()->format('Y-m-d H:i:s'),
            'sumber' => 'bank',
            'keterangan' => 'Pelunasan sisa transfer BCA',
        ]);

        $response2->assertRedirect();
        $pembelian->refresh();

        $this->assertEquals(1200000, $pembelian->terbayar);
        $this->assertEquals(0, $pembelian->sisa_hutang);
        $this->assertEquals('lunas', $pembelian->status_bayar);
        $this->assertNotNull($pembelian->tanggal_lunas);
        $this->assertCount(2, $pembelian->pembayarans);
    }
}
