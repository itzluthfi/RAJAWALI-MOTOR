<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\PengaturanToko;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PengaturanTokoTest extends TestCase
{
    use RefreshDatabase;

    private function buatUserOwner(): User
    {
        return User::create([
            'name' => 'Owner Utama',
            'username' => 'owner',
            'email' => 'owner@test.com',
            'password' => 'password',
            'peran' => 'owner',
            'aktif' => true,
        ]);
    }

    public function test_owner_bisa_membuka_halaman_pengaturan_toko(): void
    {
        $owner = $this->buatUserOwner();

        $response = $this->actingAs($owner)->get('/admin/pengaturan/toko');

        $response->assertOk();
        $response->assertViewHas('pengaturan');
    }

    public function test_owner_bisa_mengubah_pengaturan_toko(): void
    {
        $owner = $this->buatUserOwner();

        $response = $this->actingAs($owner)->post('/admin/pengaturan/toko', [
            'nama_toko' => 'Rajawali Motor Surabaya Cabang 1',
            'alamat' => 'Jl. Siwalankerto Timur No. 231 A, Surabaya',
            'telepon' => '081234567890',
            'format_nota' => 'RM{tahun}{urutan}',
            'batas_diskon_kasir_persen' => 10,
            'izinkan_stok_minus' => 1,
        ]);

        $response->assertRedirect(route('pengaturan.toko'));
        $this->assertDatabaseHas('pengaturan_tokos', [
            'nama_toko' => 'Rajawali Motor Surabaya Cabang 1',
            'batas_diskon_kasir_persen' => 10,
            'izinkan_stok_minus' => true,
        ]);
    }

    public function test_validasi_nama_toko_wajib_diisi(): void
    {
        $owner = $this->buatUserOwner();

        $response = $this->actingAs($owner)->post('/admin/pengaturan/toko', [
            'nama_toko' => '',
            'format_nota' => 'PJ{tahun}{urutan}',
            'batas_diskon_kasir_persen' => 5,
        ]);

        $response->assertSessionHasErrors('nama_toko');
    }
}
