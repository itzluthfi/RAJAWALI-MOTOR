<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Group;
use App\Models\Satuan;
use App\Models\StokMutasi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class PusatStokTest extends TestCase
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

    private function createBarang(array $attributes = []): Barang
    {
        $group = Group::firstOrCreate(['nama' => 'Sparepart']);
        $satuan = Satuan::firstOrCreate(['nama' => 'PCS']);

        return Barang::create(array_merge([
            'kode' => 'BRG-' . Str::upper(Str::random(5)),
            'nama' => 'Barang Uji',
            'group_id' => $group->id,
            'satuan_id' => $satuan->id,
            'hpp' => 10000,
            'harga_eceran' => 15000,
            'harga_grosir' => 13000,
            'stok_minimum' => 5,
            'aktif' => true,
        ], $attributes));
    }

    public function test_owner_can_access_pusat_stok_index(): void
    {
        $this->actingAs($this->owner());

        $barang = $this->createBarang([
            'kode' => 'BRG001',
            'nama' => 'Oli Mesin MPX 0.8L',
            'hpp' => 45000,
            'harga_eceran' => 55000,
            'stok_minimum' => 5,
            'aktif' => true,
        ]);

        StokMutasi::create([
            'barang_id' => $barang->id,
            'tanggal' => now()->toDateString(),
            'jenis_mutasi' => 'penyesuaian',
            'no_dokumen' => 'INIT-001',
            'masuk' => 10,
            'keluar' => 0,
            'hpp' => 45000,
            'keterangan' => 'Saldo awal testing',
        ]);

        $response = $this->get('/admin/stok');
        $response->assertOk();
        $response->assertSee('Pusat Stok Terpadu');
        $response->assertSee('Rekap &amp; Nilai Stok', false);
        $response->assertSee('Oli Mesin MPX 0.8L');
        $response->assertSee('Total Valuasi Persediaan');
    }

    public function test_pusat_stok_tabs(): void
    {
        $this->actingAs($this->owner());

        $barang = $this->createBarang([
            'kode' => 'BRG-MENIPIS',
            'nama' => 'Kampas Rem Vario',
            'hpp' => 25000,
            'harga_eceran' => 35000,
            'stok_minimum' => 10,
            'aktif' => true,
        ]);

        StokMutasi::create([
            'barang_id' => $barang->id,
            'tanggal' => now()->toDateString(),
            'jenis_mutasi' => 'penyesuaian',
            'no_dokumen' => 'INIT-002',
            'masuk' => 2,
            'keluar' => 0,
            'hpp' => 25000,
            'keterangan' => 'Stok sisa 2 (di bawah minimum 10)',
        ]);

        // Tab Menipis
        $resMenipis = $this->get('/admin/stok?tab=menipis');
        $resMenipis->assertOk();
        $resMenipis->assertSee('Peringatan Stok Di Bawah Minimum');
        $resMenipis->assertSee('Kampas Rem Vario');

        // Tab Kartu
        $resKartu = $this->get('/admin/stok?tab=kartu&barang_id=' . $barang->id);
        $resKartu->assertOk();
        $resKartu->assertSee('Kartu Mutasi Riwayat Stok');
        $resKartu->assertSee('INIT-002');

        // Tab Opname
        $resOpname = $this->get('/admin/stok?tab=opname');
        $resOpname->assertOk();
        $resOpname->assertSee('Penyesuaian Stok Opname Fisik');
    }

    public function test_legacy_stok_routes_redirect_to_unified_tabs(): void
    {
        $this->actingAs($this->owner());

        $this->get('/admin/stok/rekap')->assertRedirect(route('stok.index', ['tab' => 'rekap']));
        $this->get('/admin/stok/menipis')->assertRedirect(route('stok.index', ['tab' => 'menipis']));
        $this->get('/admin/stok/kartu')->assertRedirect(route('stok.index', ['tab' => 'kartu']));
        $this->get('/admin/stok/opname')->assertRedirect(route('stok.index', ['tab' => 'opname']));
    }

    public function test_kasir_cannot_access_pusat_stok(): void
    {
        $this->actingAs($this->kasir());

        $this->get('/admin/stok')->assertStatus(403);
    }

    public function test_owner_can_submit_stok_opname(): void
    {
        $this->actingAs($this->owner());

        $barang = $this->createBarang([
            'kode' => 'BRG-OPN',
            'nama' => 'Busi Denso',
            'hpp' => 15000,
            'harga_eceran' => 20000,
            'stok_minimum' => 5,
            'aktif' => true,
        ]);

        StokMutasi::create([
            'barang_id' => $barang->id,
            'tanggal' => now()->toDateString(),
            'jenis_mutasi' => 'penyesuaian',
            'no_dokumen' => 'INIT-003',
            'masuk' => 10,
            'keluar' => 0,
            'hpp' => 15000,
            'keterangan' => 'Saldo awal 10',
        ]);

        $res = $this->post('/admin/stok/opname', [
            'barang_id' => $barang->id,
            'stok_fisik' => 12, // Fisik 12 vs sistem 10 => selisih +2
            'alasan' => 'Ditemukan 2 pcs di kardus belakang',
        ]);

        $res->assertRedirect(route('stok.index', ['tab' => 'opname']));
        $res->assertSessionHas('sukses');

        $this->assertDatabaseHas('stok_mutasis', [
            'barang_id' => $barang->id,
            'jenis_mutasi' => 'penyesuaian',
            'masuk' => 2,
            'keluar' => 0,
        ]);
    }
}
