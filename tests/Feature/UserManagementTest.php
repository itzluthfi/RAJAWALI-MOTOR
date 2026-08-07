<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function buatUserOwner(): User
    {
        return User::create([
            'name' => 'Owner Utama',
            'username' => 'owner',
            'email' => 'owner@rajawalimotor.test',
            'password' => 'password',
            'peran' => 'owner',
            'aktif' => true,
        ]);
    }

    public function test_owner_bisa_membuka_halaman_manajemen_user(): void
    {
        $owner = $this->buatUserOwner();

        $response = $this->actingAs($owner)->get('/admin/pengaturan/user');

        $response->assertOk();
        $response->assertViewHas('users');
    }

    public function test_non_owner_tidak_bisa_akses_manajemen_user(): void
    {
        $kasir = User::create([
            'name' => 'Kasir 1',
            'username' => 'kasir1',
            'email' => 'kasir1@rajawalimotor.test',
            'password' => 'password',
            'peran' => 'kasir',
            'aktif' => true,
        ]);

        $response = $this->actingAs($kasir)->get('/admin/pengaturan/user');

        $response->assertForbidden();
    }

    public function test_owner_bisa_menambah_user_baru(): void
    {
        $owner = $this->buatUserOwner();

        $response = $this->actingAs($owner)->post('/admin/pengaturan/user', [
            'name' => 'Montir Handal',
            'username' => 'montir1',
            'email' => 'montir1@rajawalimotor.test',
            'password' => 'password123',
            'peran' => 'montir',
        ]);

        $response->assertRedirect(route('pengaturan.user'));
        $this->assertDatabaseHas('users', [
            'username' => 'montir1',
            'peran' => 'montir',
            'aktif' => true,
        ]);
    }

    public function test_owner_bisa_memperbarui_user(): void
    {
        $owner = $this->buatUserOwner();
        $target = User::create([
            'name' => 'Gudang Lama',
            'username' => 'gudang1',
            'email' => 'gudang1@rajawalimotor.test',
            'password' => 'password',
            'peran' => 'gudang',
            'aktif' => true,
        ]);

        $response = $this->actingAs($owner)->put("/admin/pengaturan/user/{$target->id}", [
            'name' => 'Gudang Baru',
            'username' => 'gudang1',
            'email' => 'gudangbaru@rajawalimotor.test',
            'peran' => 'gudang',
        ]);

        $response->assertRedirect(route('pengaturan.user'));
        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'name' => 'Gudang Baru',
            'email' => 'gudangbaru@rajawalimotor.test',
        ]);
    }

    public function test_owner_bisa_menonaktifkan_user_lain(): void
    {
        $owner = $this->buatUserOwner();
        $target = User::create([
            'name' => 'Kasir Nonaktif',
            'username' => 'kasir_nonaktif',
            'email' => 'kasirnonaktif@rajawalimotor.test',
            'password' => 'password',
            'peran' => 'kasir',
            'aktif' => true,
        ]);

        $response = $this->actingAs($owner)->patch("/admin/pengaturan/user/{$target->id}/toggle-aktif");

        $response->assertRedirect(route('pengaturan.user'));
        $this->assertDatabaseHas('users', [
            'id' => $target->id,
            'aktif' => false,
        ]);
    }

    public function test_owner_tidak_bisa_menonaktifkan_akun_sendiri(): void
    {
        $owner = $this->buatUserOwner();

        $response = $this->actingAs($owner)->patch("/admin/pengaturan/user/{$owner->id}/toggle-aktif");

        $response->assertRedirect();
        $response->assertSessionHas('gagal');
        $this->assertDatabaseHas('users', [
            'id' => $owner->id,
            'aktif' => true,
        ]);
    }
}
