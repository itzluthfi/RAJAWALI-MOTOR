<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Penjualan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PiutangInstallmentTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['username' => 'admin_' . Str::random(6), 'peran' => 'admin']);
    }

    public function test_can_pay_piutang_in_installments(): void
    {
        $adminUser = $this->admin();
        $this->actingAs($adminUser);

        $customer = Customer::create(['nama' => 'Toko Maju', 'termin_hari' => 30]);

        $penjualan = Penjualan::create([
            'nomor_nota' => 'PJ-TEMPO-001',
            'customer_id' => $customer->id,
            'user_id' => $adminUser->id,
            'subtotal' => 1000000,
            'diskon' => 0,
            'pajak' => 0,
            'total_akhir' => 1000000,
            'bayar' => 0,
            'kembali' => 0,
            'uang_muka' => 200000, // Sisa piutang = 800.000
            'metode_pembayaran' => 'tempo',
            'status_bayar' => 'piutang',
        ]);

        // Cicilan pertama = Rp 300.000
        $response = $this->post("/admin/keuangan/piutang/{$penjualan->id}/pelunasan", [
            'nominal_bayar' => 300000,
        ]);

        $response->assertRedirect();

        $penjualan->refresh();
        $this->assertEquals(500000, (float) $penjualan->uang_muka);
        $this->assertEquals('piutang', $penjualan->status_bayar);

        // Cicilan kedua = Rp 500.000 (Lunas)
        $response2 = $this->post("/admin/keuangan/piutang/{$penjualan->id}/pelunasan", [
            'nominal_bayar' => 500000,
        ]);

        $response2->assertRedirect();

        $penjualan->refresh();
        $this->assertEquals(1000000, (float) $penjualan->uang_muka);
        $this->assertEquals('lunas', $penjualan->status_bayar);
    }
}
