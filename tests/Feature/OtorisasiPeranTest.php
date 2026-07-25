<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OtorisasiPeranTest extends TestCase
{
    use RefreshDatabase;

    private function buatUser(string $peran, string $username): User
    {
        return User::create([
            'name' => ucfirst($username),
            'username' => $username,
            'email' => "{$username}@rajawalimotor.test",
            'password' => 'password',
            'peran' => $peran,
            'aktif' => true,
        ]);
    }

    public function test_owner_bisa_akses_semua_halaman(): void
    {
        $this->actingAs($this->buatUser('owner', 'owner'));

        $this->get('/admin/dashboard')->assertOk();
        $this->get('/admin/kasir')->assertOk();
        $this->get('/admin/barang')->assertOk();
        $this->get('/admin/pengaturan/toko')->assertOk();
        $this->get('/admin/laporan')->assertOk();
    }

    public function test_kasir_tidak_bisa_akses_pengaturan(): void
    {
        $this->actingAs($this->buatUser('kasir', 'kasir1'));

        $this->get('/admin/kasir')->assertOk();
        $this->get('/admin/pengaturan/toko')->assertForbidden();
        $this->get('/admin/barang')->assertForbidden();
    }

    public function test_gudang_tidak_bisa_akses_kasir(): void
    {
        $this->actingAs($this->buatUser('gudang', 'gudang1'));

        $this->get('/admin/barang')->assertOk();
        $this->get('/admin/stok/rekap')->assertOk();
        $this->get('/admin/kasir')->assertForbidden();
        $this->get('/admin/keuangan/kas')->assertForbidden();
    }

    public function test_montir_hanya_bisa_akses_service_dan_dashboard(): void
    {
        $this->actingAs($this->buatUser('montir', 'montir1'));

        $this->get('/admin/dashboard')->assertOk();
        $this->get('/admin/service')->assertOk();
        $this->get('/admin/kasir')->assertForbidden();
        $this->get('/admin/laporan')->assertForbidden();
        $this->get('/admin/pengaturan/user')->assertForbidden();
    }

    public function test_admin_tidak_bisa_akses_pengaturan_khusus_owner(): void
    {
        $this->actingAs($this->buatUser('admin', 'admin'));

        $this->get('/admin/laporan')->assertOk();
        $this->get('/admin/pengaturan/toko')->assertForbidden();
    }
}
