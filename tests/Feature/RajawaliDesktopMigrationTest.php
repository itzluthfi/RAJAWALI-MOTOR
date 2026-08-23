<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\Group;
use App\Models\KasFlow;
use App\Models\Penjualan;
use App\Models\Sales;
use App\Models\Satuan;
use App\Models\Service;
use App\Models\StokMutasi;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RajawaliDesktopMigrationTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        return User::create([
            'name' => 'Owner', 'username' => 'owner', 'email' => 'owner@test.local',
            'password' => 'password', 'peran' => 'owner', 'aktif' => true,
        ]);
    }

    public function test_can_create_sales_with_new_fields(): void
    {
        $this->actingAs($this->owner());

        $response = $this->post('/admin/sales', [
            'kode_sales' => 'DKN',
            'nama' => 'Dedi Kurniawan',
            'alamat' => 'Jl. Gubeng Kertajaya V',
            'kota' => 'Surabaya',
            'telepon' => '0812-1111-2222',
            'persentase_komisi' => 2.0,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('sales', [
            'kode_sales' => 'DKN',
            'nama' => 'Dedi Kurniawan',
            'alamat' => 'Jl. Gubeng Kertajaya V',
            'kota' => 'Surabaya',
        ]);
    }

    public function test_service_intake_workflow_and_stock_mutasi_sync(): void
    {
        $this->actingAs($this->owner());

        $customer = Customer::create(['nama' => 'Andi', 'termin_hari' => 0]);
        $group = Group::create(['nama' => 'Oli']);
        $satuan = Satuan::create(['nama' => 'Botol']);
        
        $barang = Barang::create([
            'kode' => 'OLIFED1L', 'nama' => 'OLI FEDERAL MATIC 1L', 'group_id' => $group->id, 'satuan_id' => $satuan->id,
            'hpp' => 35000, 'harga_eceran' => 45000, 'harga_grosir' => 42000, 'stok_minimum' => 5,
        ]);

        // Berikan stok awal
        StokMutasi::create([
            'barang_id' => $barang->id, 'tanggal' => now()->toDateString(), 'jenis_mutasi' => 'penyesuaian',
            'no_dokumen' => 'STOK-AWAL', 'masuk' => 10, 'keluar' => 0, 'hpp' => 35000,
        ]);

        // Simpan service baru (status 'masuk')
        $response = $this->post('/admin/service', [
            'tanggal_masuk' => now()->toDateString(),
            'customer_id' => $customer->id,
            'merk_type' => 'Honda Vario 125',
            'repaired_by' => 'intern',
            'items' => [
                ['barang_id' => $barang->id, 'qty' => 1, 'harga' => 45000]
            ],
            'jasas' => [
                ['nama_jasa' => 'Servis CVT', 'harga_supplier' => 0, 'harga_nett' => 50000]
            ]
        ]);

        $response->assertRedirect();
        
        $service = Service::first();
        $this->assertNotNull($service);
        $this->assertEquals('masuk', $service->status);
        $this->assertEquals(95000, (float) $service->grand_total_nett);

        // Status 'masuk' -> tidak boleh mengurangi stok di database
        $this->assertDatabaseMissing('stok_mutasis', [
            'no_dokumen' => $service->nomor_dokumen,
            'jenis_mutasi' => 'service',
        ]);

        // Lanjutkan status pengerjaan -> menjadi 'dikerjakan'
        $responseUpdate = $this->patch("/admin/service/{$service->id}/status");
        $responseUpdate->assertSessionHasNoErrors();
        $responseUpdate->assertRedirect();
        
        $service->refresh();
        $this->assertEquals('dikerjakan', $service->status);

        // Status 'dikerjakan' -> harus mensinkronkan stok mutasi (mengurangi stok)
        $this->assertDatabaseHas('stok_mutasis', [
            'barang_id' => $barang->id,
            'no_dokumen' => $service->nomor_dokumen,
            'jenis_mutasi' => 'service',
            'keluar' => 1.000,
        ]);
    }

    public function test_extern_service_lunas_generates_kas_flows(): void
    {
        $this->actingAs($this->owner());

        $customer = Customer::create(['nama' => 'Sinta', 'termin_hari' => 0]);
        $supplier = Supplier::create(['nama' => 'Bengkel Rekanan A']);

        $service = Service::create([
            'nomor_dokumen' => 'SV2026080001',
            'tanggal_masuk' => now()->toDateString(),
            'customer_id' => $customer->id,
            'merk_type' => 'Yamaha NMax',
            'repaired_by' => 'extern',
            'supplier_id' => $supplier->id,
            'status' => 'diambil',
            'grand_total_supplier' => 100000,
            'grand_total_nett' => 150000,
            'status_lunas' => false,
        ]);

        // Bayar lunas (Lanjutkan Status dari diambil -> lunas)
        $response = $this->patch("/admin/service/{$service->id}/status");
        $response->assertRedirect();

        $service->refresh();
        $this->assertEquals('lunas', $service->status);
        $this->assertTrue($service->status_lunas);

        // Harus tercatat KasFlow masuk untuk pembayaran service oleh customer (Rp 150.000)
        $this->assertDatabaseHas('kas_flows', [
            'tipe' => 'masuk',
            'sumber' => 'kas',
            'no_referensi' => $service->nomor_dokumen,
            'nominal' => 150000,
        ]);

        // Harus tercatat KasFlow keluar untuk pembayaran outsourcing ke supplier rekanan (Rp 100.000)
        $this->assertDatabaseHas('kas_flows', [
            'tipe' => 'keluar',
            'sumber' => 'kas',
            'no_referensi' => $service->nomor_dokumen,
            'nominal' => 100000,
        ]);
    }

    public function test_pos_cashier_handles_discounts_and_down_payments(): void
    {
        $this->actingAs($this->owner());

        $customer = Customer::create(['nama' => 'Toko B', 'termin_hari' => 30]);
        $group = Group::create(['nama' => 'Ban']);
        $satuan = Satuan::create(['nama' => 'PCS']);
        $barang = Barang::create([
            'kode' => 'BANIRC', 'nama' => 'BAN IRC TUBELESS', 'group_id' => $group->id, 'satuan_id' => $satuan->id,
            'hpp' => 150000, 'harga_eceran' => 200000, 'harga_grosir' => 180000, 'stok_minimum' => 2,
        ]);
        \App\Models\StokMutasi::create([
            'barang_id' => $barang->id,
            'tanggal' => now()->toDateString(),
            'jenis_mutasi' => 'penyesuaian',
            'no_dokumen' => 'INIT-TEST',
            'masuk' => 10,
            'keluar' => 0,
            'hpp' => 150000,
            'keterangan' => 'Stok awal test',
        ]);

        // Coba checkout tempo dengan row discount dan down payment (DP)
        $response = $this->postJson('/admin/kasir', [
            'customer_id' => $customer->id,
            'items' => [
                ['kode' => 'BANIRC', 'qty' => 2, 'harga' => 200000, 'diskon' => 10000] // Subtotal = (2 * 200k) - 10k = 390k
            ],
            'diskon' => 0,
            'pajak' => 0,
            'bayar' => 0,
            'uang_muka' => 100000, // DP Rp 100.000
            'metode_pembayaran' => 'tempo',
        ]);

        $response->assertJsonPath('sukses', true);

        $penjualan = Penjualan::first();
        $this->assertNotNull($penjualan);
        $this->assertEquals(390000, (float) $penjualan->total_akhir);
        $this->assertEquals('piutang', $penjualan->status_bayar);
        $this->assertEquals(100000, (float) $penjualan->uang_muka);

        // Verifikasi detail penjualan menyimpan diskon baris
        $this->assertDatabaseHas('penjualan_details', [
            'penjualan_id' => $penjualan->id,
            'barang_id' => $barang->id,
            'diskon' => 10000,
            'subtotal' => 390000,
        ]);

        // Verifikasi KasFlow masuk mencatat pembayaran Down Payment (DP) saja sebesar Rp 100.000
        $this->assertDatabaseHas('kas_flows', [
            'tipe' => 'masuk',
            'sumber' => 'kas',
            'no_referensi' => $penjualan->nomor_nota,
            'nominal' => 100000,
        ]);
    }

    public function test_pos_last_price_endpoint(): void
    {
        $owner = $this->owner();
        $this->actingAs($owner);

        $customer = Customer::create(['nama' => 'Pelanggan Lama', 'termin_hari' => 0]);
        $group = Group::create(['nama' => 'Busi']);
        $satuan = Satuan::create(['nama' => 'PCS']);
        $barang = Barang::create([
            'kode' => 'BUSINGK', 'nama' => 'BUSI NGK SPARK', 'group_id' => $group->id, 'satuan_id' => $satuan->id,
            'hpp' => 10000, 'harga_eceran' => 15000, 'harga_grosir' => 13000, 'stok_minimum' => 2,
        ]);

        // Catat penjualan sebelumnya ke customer tersebut dengan harga khusus Rp 14.000
        $penjualan = Penjualan::create([
            'nomor_nota' => 'PJ2026080001', 'customer_id' => $customer->id, 'user_id' => $owner->id,
            'subtotal' => 14000, 'diskon' => 0, 'pajak' => 0, 'total_akhir' => 14000, 'bayar' => 14000,
            'kembali' => 0, 'metode_pembayaran' => 'tunai', 'status_bayar' => 'lunas'
        ]);
        \App\Models\PenjualanDetail::create([
            'penjualan_id' => $penjualan->id, 'barang_id' => $barang->id, 'qty' => 1,
            'harga_satuan' => 14000, 'diskon' => 0, 'hpp' => 10000, 'subtotal' => 14000
        ]);

        // Akses endpoint harga terakhir
        $response = $this->getJson("/admin/kasir/harga-terakhir?customer_id={$customer->id}&barang_id={$barang->id}");
        
        $response->assertJson([
            'harga' => 14000,
            'nota' => 'PJ2026080001',
        ]);
    }
}
