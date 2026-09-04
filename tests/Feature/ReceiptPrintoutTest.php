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
use Illuminate\Support\Str;
use Tests\TestCase;

class ReceiptPrintoutTest extends TestCase
{
    use RefreshDatabase;

    private function kasir(): User
    {
        return User::factory()->create(['username' => 'kasir_' . Str::random(6), 'peran' => 'kasir']);
    }

    public function test_receipt_does_not_contain_kembali(): void
    {
        $kasirUser = $this->kasir();
        $this->actingAs($kasirUser);

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
            'user_id' => $kasirUser->id,
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

    public function test_nota_supports_80mm_and_58mm_preview_and_pdf(): void
    {
        $kasirUser = $this->kasir();
        $this->actingAs($kasirUser);

        $customer = Customer::create(['nama' => 'Agus']);
        $group = Group::create(['nama' => 'Oli']);
        $satuan = Satuan::create(['nama' => 'BOTOL']);
        $barang = Barang::create([
            'kode' => 'OLI-MPX',
            'nama' => 'Oli MPX2 0.8L',
            'group_id' => $group->id,
            'satuan_id' => $satuan->id,
            'hpp' => 45000,
            'harga_eceran' => 55000,
            'stok_minimum' => 5,
        ]);

        $penjualan = Penjualan::create([
            'nomor_nota' => 'PJ-THERMAL-01',
            'customer_id' => $customer->id,
            'user_id' => $kasirUser->id,
            'subtotal' => 55000,
            'diskon' => 0,
            'pajak' => 0,
            'total_akhir' => 55000,
            'bayar' => 55000,
            'kembali' => 0,
            'metode_pembayaran' => 'tunai',
            'status_bayar' => 'lunas',
        ]);

        PenjualanDetail::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang->id,
            'qty' => 1,
            'harga_satuan' => 55000,
            'diskon' => 0,
            'hpp' => 45000,
            'subtotal' => 55000,
        ]);

        // Preview default (80mm) & 58mm
        $res80 = $this->get('/admin/cetak/nota/' . $penjualan->nomor_nota);
        $res80->assertOk()->assertSee('Thermal 80mm')->assertSee('Thermal 58mm');

        $res58 = $this->get('/admin/cetak/nota/' . $penjualan->nomor_nota . '?size=58');
        $res58->assertOk()->assertSee('Oli MPX2 0.8L');

        // PDF 80mm & 58mm
        $pdf80 = $this->get('/admin/cetak/nota/' . $penjualan->nomor_nota . '/pdf');
        $pdf80->assertOk();
        $this->assertEquals('application/pdf', $pdf80->headers->get('content-type'));

        $pdf58 = $this->get('/admin/cetak/nota/' . $penjualan->nomor_nota . '/pdf?size=58');
        $pdf58->assertOk();
        $this->assertEquals('application/pdf', $pdf58->headers->get('content-type'));
    }

    public function test_faktur_and_surat_jalan_preview_and_pdf(): void
    {
        $kasirUser = $this->kasir();
        $this->actingAs($kasirUser);

        $customer = Customer::create(['nama' => 'Bengkel Mandiri']);
        $group = Group::create(['nama' => 'Ban']);
        $satuan = Satuan::create(['nama' => 'PCS']);
        $barang = Barang::create([
            'kode' => 'BAN-IRC',
            'nama' => 'Ban Luar IRC 80/90-14',
            'group_id' => $group->id,
            'satuan_id' => $satuan->id,
            'hpp' => 150000,
            'harga_eceran' => 185000,
            'stok_minimum' => 2,
        ]);

        $penjualan = Penjualan::create([
            'nomor_nota' => 'PJ-FAKTUR-01',
            'customer_id' => $customer->id,
            'user_id' => $kasirUser->id,
            'subtotal' => 185000,
            'diskon' => 0,
            'pajak' => 0,
            'total_akhir' => 185000,
            'bayar' => 185000,
            'kembali' => 0,
            'metode_pembayaran' => 'tunai',
            'status_bayar' => 'lunas',
        ]);

        PenjualanDetail::create([
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang->id,
            'qty' => 1,
            'harga_satuan' => 185000,
            'diskon' => 0,
            'hpp' => 150000,
            'subtotal' => 185000,
        ]);

        // Faktur HTML & PDF
        $fakturHtml = $this->get('/admin/cetak/faktur/' . $penjualan->nomor_nota);
        $fakturHtml->assertOk()->assertSee('FAKTUR PENJUALAN');

        $fakturPdf = $this->get('/admin/cetak/faktur/' . $penjualan->nomor_nota . '/pdf');
        $fakturPdf->assertOk();
        $this->assertEquals('application/pdf', $fakturPdf->headers->get('content-type'));

        // Surat Jalan HTML & PDF
        $sjHtml = $this->get('/admin/cetak/surat-jalan/' . $penjualan->nomor_nota);
        $sjHtml->assertOk()->assertSee('SURAT JALAN PENGIRIMAN');

        $sjPdf = $this->get('/admin/cetak/surat-jalan/' . $penjualan->nomor_nota . '/pdf');
        $sjPdf->assertOk();
        $this->assertEquals('application/pdf', $sjPdf->headers->get('content-type'));
    }
}
