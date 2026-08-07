<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\Group;
use App\Models\PengaturanToko;
use App\Models\Penjualan;
use App\Models\Satuan;
use App\Models\StokMutasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PenjualanPosTest extends TestCase
{
    use RefreshDatabase;

    private function buatUserKasir(): User
    {
        return User::create([
            'name' => 'Kasir Utama',
            'username' => 'kasir1',
            'email' => 'kasir1@test.com',
            'password' => 'password',
            'peran' => 'kasir',
            'aktif' => true,
        ]);
    }

    private function buatBarang(string $kode = 'BRG001', float $stokAwal = 50.0): Barang
    {
        $group = Group::create(['nama' => 'Oli & Pelumas']);
        $satuan = Satuan::create(['nama' => 'Botol']);

        $barang = Barang::create([
            'kode' => $kode,
            'nama' => 'Oli Shell Helix 1L',
            'group_id' => $group->id,
            'satuan_id' => $satuan->id,
            'hpp' => 45000,
            'harga_eceran' => 60000,
            'harga_grosir' => 55000,
            'stok_minimum' => 5,
            'aktif' => true,
        ]);

        StokMutasi::create([
            'barang_id' => $barang->id,
            'tanggal' => now()->toDateString(),
            'jenis_mutasi' => 'penyesuaian',
            'no_dokumen' => 'INIT-001',
            'masuk' => $stokAwal,
            'keluar' => 0,
            'hpp' => 45000,
            'keterangan' => 'Stok Awal Test',
        ]);

        return $barang;
    }

    public function test_kasir_bisa_membuka_halaman_pos(): void
    {
        $kasir = $this->buatUserKasir();

        $response = $this->actingAs($kasir)->get('/admin/kasir');

        $response->assertOk();
        $response->assertViewHas('daftarBarangJson');
    }

    public function test_kasir_bisa_memproses_transaksi_pos_dan_mengurangi_stok(): void
    {
        $kasir = $this->buatUserKasir();
        $barang = $this->buatBarang('BRG001', 50.0);
        $customer = Customer::create([
            'nama' => 'Customer Umum',
            'aktif' => true,
        ]);

        PengaturanToko::current();

        $response = $this->actingAs($kasir)->postJson('/admin/kasir', [
            'customer_id' => $customer->id,
            'items' => [
                [
                    'kode' => 'BRG001',
                    'qty' => 2,
                    'harga' => 60000,
                ],
            ],
            'diskon' => 0,
            'bayar' => 150000,
            'metode_pembayaran' => 'tunai',
        ]);

        $response->assertOk();
        $response->assertJson([
            'sukses' => true,
        ]);

        $this->assertDatabaseHas('penjualans', [
            'customer_id' => $customer->id,
            'user_id' => $kasir->id,
            'subtotal' => 120000,
            'total_akhir' => 120000,
            'bayar' => 150000,
            'kembali' => 30000,
            'status_bayar' => 'lunas',
        ]);

        $penjualan = Penjualan::first();

        $this->assertDatabaseHas('penjualan_details', [
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang->id,
            'qty' => 2,
            'harga_satuan' => 60000,
            'subtotal' => 120000,
        ]);

        $this->assertDatabaseHas('stok_mutasis', [
            'barang_id' => $barang->id,
            'jenis_mutasi' => 'penjualan',
            'no_dokumen' => $penjualan->nomor_nota,
            'keluar' => 2,
        ]);
    }
}
