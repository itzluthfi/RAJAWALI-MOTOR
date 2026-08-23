<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\Group;
use App\Models\Penjualan;
use App\Models\PenjualanDetail;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReceiptPrintoutTest extends TestCase
{
    use RefreshDatabase;

    private function kasir(): User
    {
        return User::factory()->create(['peran' => 'kasir']);
    }

    public function test_receipt_does_not_contain_kembali(): void
    {
        $this->actingAs($this->kasir());

        $customer = Customer::create(['nama' => 'Budi']);
        $group = Group::create(['nama' => 'Sparepart']);
        $satuan = Satuan::create(['nama' => 'PCS']);
        $barang = Barang::create([
            'kode' => 'BUSI',
            'nama' => 'BUSI NGK',
            'group_id' => $group->id,
            'satuan_id' => $satuan->id,
            'hpp' => 15000,
            'harga_eceran' => 25000,
            'stok_minimum' => 5,
        ]);

        $penjualan = Penjualan::create([
            'nomor_nota' => 'PJ-TEST-001',
            'customer_id' => $customer->id,
            'user_id' => $this->kasir()->id,
            'subtotal' => 25000,
            'diskon' => 0,
            'pajak' => 0,
            'total_akhir' => 25000,
            'bayar' => 25000,
            'kembali' => 0,
            'metode_pembayaran' => 'tunai',
            'status_bayar' => 'lunas',
        ]);

        PenjualanDetail::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang->id,
            'qty' => 1,
            'harga_satuan' => 25000,
            'diskon' => 0,
            'hpp' => 15000,
            'subtotal' => 25000,
        ]);

        $response = $this->get('/admin/cetak/nota/' . $penjualan->id);
        $response->assertOk();
        $response->assertDontSee('Kembali:');
        $response->assertDontSee('Bayar:');
    }
}
