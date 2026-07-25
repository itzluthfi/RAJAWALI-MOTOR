<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\Group;
use App\Models\Satuan;
use App\Models\StokMutasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MasterDataCrudTest extends TestCase
{
    use RefreshDatabase;

    private function owner(): User
    {
        return User::create([
            'name' => 'Owner', 'username' => 'owner', 'email' => 'owner@test.local',
            'password' => 'password', 'peran' => 'owner', 'aktif' => true,
        ]);
    }

    public function test_owner_bisa_menambah_barang_baru(): void
    {
        $this->actingAs($this->owner());

        $group = Group::create(['nama' => 'Sparepart']);
        $satuan = Satuan::create(['nama' => 'PCS']);

        $response = $this->post('/admin/barang', [
            'kode' => 'TESTX01',
            'nama' => 'Barang Uji',
            'group_id' => $group->id,
            'satuan_id' => $satuan->id,
            'hpp' => 1000,
            'harga_eceran' => 1500,
            'harga_grosir' => 1300,
            'stok_minimum' => 5,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('barangs', ['kode' => 'TESTX01', 'nama' => 'Barang Uji']);
    }

    public function test_barang_dengan_stok_tidak_bisa_dinonaktifkan(): void
    {
        $this->actingAs($this->owner());

        $group = Group::create(['nama' => 'Sparepart']);
        $satuan = Satuan::create(['nama' => 'PCS']);
        $barang = Barang::create([
            'kode' => 'TESTX02', 'nama' => 'Barang Ada Stok', 'group_id' => $group->id, 'satuan_id' => $satuan->id,
            'hpp' => 1000, 'harga_eceran' => 1500, 'harga_grosir' => 1300, 'stok_minimum' => 5, 'aktif' => true,
        ]);
        StokMutasi::create([
            'barang_id' => $barang->id, 'tanggal' => now()->toDateString(), 'jenis_mutasi' => 'penyesuaian',
            'no_dokumen' => 'TEST', 'masuk' => 10, 'keluar' => 0, 'hpp' => 1000,
        ]);

        $response = $this->patch("/admin/barang/{$barang->id}/toggle-aktif");

        $response->assertSessionHasErrors();
        $this->assertDatabaseHas('barangs', ['id' => $barang->id, 'aktif' => true]);
    }

    public function test_barang_tanpa_stok_bisa_dinonaktifkan(): void
    {
        $this->actingAs($this->owner());

        $group = Group::create(['nama' => 'Sparepart']);
        $satuan = Satuan::create(['nama' => 'PCS']);
        $barang = Barang::create([
            'kode' => 'TESTX03', 'nama' => 'Barang Kosong', 'group_id' => $group->id, 'satuan_id' => $satuan->id,
            'hpp' => 1000, 'harga_eceran' => 1500, 'harga_grosir' => 1300, 'stok_minimum' => 5, 'aktif' => true,
        ]);

        $response = $this->patch("/admin/barang/{$barang->id}/toggle-aktif");

        $response->assertRedirect();
        $this->assertDatabaseHas('barangs', ['id' => $barang->id, 'aktif' => false]);
    }

    public function test_owner_bisa_ubah_dan_nonaktifkan_customer(): void
    {
        $this->actingAs($this->owner());

        $customer = Customer::create(['nama' => 'Customer Uji', 'termin_hari' => 0]);

        $update = $this->put("/admin/customer/{$customer->id}", [
            'nama' => 'Customer Uji Diubah',
            'termin_hari' => 30,
        ]);
        $update->assertRedirect();
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'nama' => 'Customer Uji Diubah', 'termin_hari' => 30]);

        $toggle = $this->patch("/admin/customer/{$customer->id}/toggle-aktif");
        $toggle->assertRedirect();
        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'aktif' => false]);
    }

    public function test_kode_barang_tidak_boleh_dobel(): void
    {
        $this->actingAs($this->owner());

        $group = Group::create(['nama' => 'Sparepart']);
        $satuan = Satuan::create(['nama' => 'PCS']);
        Barang::create([
            'kode' => 'DOBEL01', 'nama' => 'Pertama', 'group_id' => $group->id, 'satuan_id' => $satuan->id,
            'hpp' => 1000, 'harga_eceran' => 1500, 'harga_grosir' => 1300, 'stok_minimum' => 5,
        ]);

        $response = $this->post('/admin/barang', [
            'kode' => 'DOBEL01',
            'nama' => 'Kedua',
            'group_id' => $group->id,
            'satuan_id' => $satuan->id,
            'hpp' => 1000,
            'harga_eceran' => 1500,
            'harga_grosir' => 1300,
            'stok_minimum' => 5,
        ]);

        $response->assertSessionHasErrors('kode');
    }
}
