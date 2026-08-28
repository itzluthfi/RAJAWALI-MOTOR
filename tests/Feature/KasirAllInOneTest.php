<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\Group;
use App\Models\Penjualan;
use App\Models\Satuan;
use App\Models\Service;
use App\Models\StokMutasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KasirAllInOneTest extends TestCase
{
    use RefreshDatabase;

    private User $kasirUser;
    private Barang $barangOli;
    private Barang $barangJasa;
    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->kasirUser = User::create([
            'name' => 'Kasir Utama',
            'username' => 'kasir_pos',
            'email' => 'kasir@rajawali.com',
            'password' => bcrypt('password'),
            'peran' => 'kasir',
            'aktif' => true,
        ]);

        $groupPart = Group::create(['nama' => 'Oli & Pelumas', 'aktif' => true]);
        $groupJasa = Group::create(['nama' => 'Jasa Bengkel', 'aktif' => true]);
        $satuan = Satuan::create(['nama' => 'Botol', 'aktif' => true]);

        $this->barangOli = Barang::create([
            'kode' => 'OLI-001',
            'nama' => 'Oli Shell Advance AX7 0.8L',
            'group_id' => $groupPart->id,
            'satuan_id' => $satuan->id,
            'harga_eceran' => 55000,
            'hpp' => 45000,
            'stok_minimum' => 5,
            'aktif' => true,
        ]);

        // Beri stok awal 20
        StokMutasi::create([
            'barang_id' => $this->barangOli->id,
            'tanggal' => now()->toDateString(),
            'jenis_mutasi' => 'penyesuaian',
            'no_dokumen' => 'INIT01',
            'masuk' => 20,
            'keluar' => 0,
            'hpp' => 45000,
            'keterangan' => 'Stok awal',
        ]);

        $this->barangJasa = Barang::create([
            'kode' => 'JSA-001',
            'nama' => 'Jasa Servis Ringan & Tune Up',
            'group_id' => $groupJasa->id,
            'satuan_id' => $satuan->id,
            'harga_eceran' => 35000,
            'hpp' => 0,
            'stok_minimum' => 0,
            'aktif' => true,
        ]);

        $this->customer = Customer::create([
            'nama' => 'Budi Santoso',
            'telepon' => '081234567890',
            'plat_nomor' => 'L 4567 ABC',
            'jenis_kendaraan' => 'Honda Vario 125',
            'kategori' => 'umum',
            'aktif' => true,
        ]);
    }

    public function test_kasir_can_quick_add_customer(): void
    {
        $this->actingAs($this->kasirUser);

        $response = $this->postJson(route('kasir.customer-cepat'), [
            'nama' => 'Pak Haji Slamet',
            'telepon' => '081987654321',
            'plat_nomor' => 'W 8888 ZZ',
            'jenis_kendaraan' => 'Yamaha NMAX',
            'kategori' => 'grosir',
        ]);

        $response->assertOk();
        $response->assertJson(['sukses' => true]);

        $this->assertDatabaseHas('customers', [
            'nama' => 'Pak Haji Slamet',
            'plat_nomor' => 'W 8888 ZZ',
        ]);
    }

    public function test_kasir_can_create_service_spk_work_order(): void
    {
        $this->actingAs($this->kasirUser);

        $response = $this->postJson(route('kasir.store'), [
            'tipe_transaksi' => 'service_spk',
            'customer_id' => $this->customer->id,
            'plat_nomor' => 'L 4567 ABC',
            'merk_type' => 'Honda Vario 125',
            'montir_id' => $this->kasirUser->id,
            'keluhan' => 'Motor brebet tarikan berat',
            'items' => [
                ['kode' => 'OLI-001', 'qty' => 1, 'harga' => 55000, 'diskon' => 0],
                ['kode' => 'JSA-001', 'qty' => 1, 'harga' => 35000, 'diskon' => 0],
            ],
            'metode_pembayaran' => 'tunai',
        ]);

        $response->assertOk();
        $response->assertJson(['sukses' => true, 'tipe' => 'service_spk']);

        $this->assertDatabaseHas('services', [
            'customer_id' => $this->customer->id,
            'status' => 'dikerjakan',
            'status_lunas' => false,
        ]);
    }

    public function test_kasir_can_process_regular_sale_and_direct_service(): void
    {
        $this->actingAs($this->kasirUser);

        // 1. Penjualan Part Biasa
        $response1 = $this->postJson(route('kasir.store'), [
            'tipe_transaksi' => 'penjualan',
            'customer_id' => $this->customer->id,
            'items' => [
                ['kode' => 'OLI-001', 'qty' => 2, 'harga' => 55000, 'diskon' => 0],
            ],
            'bayar' => 110000,
            'metode_pembayaran' => 'tunai',
        ]);

        $response1->assertOk();
        $response1->assertJson(['sukses' => true, 'tipe' => 'penjualan']);

        // 2. Servis Langsung Lunas (Suku Cadang + Jasa)
        $response2 = $this->postJson(route('kasir.store'), [
            'tipe_transaksi' => 'service_langsung',
            'customer_id' => $this->customer->id,
            'plat_nomor' => 'L 4567 ABC',
            'merk_type' => 'Honda Vario 125',
            'items' => [
                ['kode' => 'OLI-001', 'qty' => 1, 'harga' => 55000, 'diskon' => 0],
                ['kode' => 'JSA-001', 'qty' => 1, 'harga' => 35000, 'diskon' => 0],
            ],
            'bayar' => 90000,
            'metode_pembayaran' => 'tunai',
        ]);

        $response2->assertOk();
        $response2->assertJson(['sukses' => true]);

        // Cek stok oli terpotong 2 + 1 = 3 (sisa 17)
        $stokService = app(\App\Services\StokService::class);
        $this->assertEquals(17, $stokService->stokSaatIni($this->barangOli));
    }
}
