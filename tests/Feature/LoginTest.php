<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function buatUser(array $atribut = []): User
    {
        return User::create(array_merge([
            'name' => 'Budi Santoso',
            'username' => 'owner',
            'email' => 'owner@rajawalimotor.test',
            'password' => 'password',
            'peran' => 'owner',
            'aktif' => true,
        ], $atribut));
    }

    public function test_halaman_login_bisa_dibuka_tamu(): void
    {
        $this->get('/admin/login')->assertOk();
    }

    public function test_pengguna_login_diarahkan_ke_login_halaman_diblokir(): void
    {
        $this->actingAs($this->buatUser());

        $this->get('/admin/login')->assertRedirect();
    }

    public function test_login_berhasil_mengarahkan_owner_ke_dashboard(): void
    {
        $this->buatUser();

        $response = $this->post('/admin/login', [
            'username' => 'owner',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticated();
    }

    public function test_login_berhasil_mengarahkan_kasir_ke_halaman_kasir(): void
    {
        $this->buatUser(['username' => 'kasir1', 'email' => 'kasir1@rajawalimotor.test', 'peran' => 'kasir']);

        $response = $this->post('/admin/login', [
            'username' => 'kasir1',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('kasir'));
    }

    public function test_login_gagal_dengan_kata_sandi_salah(): void
    {
        $this->buatUser();

        $response = $this->post('/admin/login', [
            'username' => 'owner',
            'password' => 'salah',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_akun_nonaktif_ditolak(): void
    {
        $this->buatUser(['aktif' => false]);

        $response = $this->post('/admin/login', [
            'username' => 'owner',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_lima_kali_gagal_login_mengunci_akun(): void
    {
        $this->buatUser();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', ['username' => 'owner', 'password' => 'salah']);
        }

        $response = $this->post('/admin/login', ['username' => 'owner', 'password' => 'password']);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_logout_mengeluarkan_pengguna(): void
    {
        $this->actingAs($this->buatUser());

        $response = $this->post('/admin/logout');

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    public function test_tamu_tidak_bisa_akses_dashboard(): void
    {
        $this->get('/admin/dashboard')->assertRedirect(route('login'));
    }
}
