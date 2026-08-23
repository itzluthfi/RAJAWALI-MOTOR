<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CustomerAttributesTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['username' => 'admin_' . Str::random(6), 'peran' => 'admin']);
    }

    public function test_can_create_customer_with_rich_attributes(): void
    {
        $this->actingAs($this->admin());

        $response = $this->post('/admin/customer', [
            'nama' => 'Pak Harun (Bengkel Mitra)',
            'plat_nomor' => 'L 9988 XYZ',
            'jenis_kendaraan' => 'Honda Vario 160',
            'kategori' => 'mitra',
            'telepon' => '08123456789',
            'no_wa' => '08123456789',
            'alamat' => 'Jl. Jasem No. 10, Sidoarjo',
            'termin_hari' => 30,
        ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('customers', [
            'nama' => 'Pak Harun (Bengkel Mitra)',
            'plat_nomor' => 'L 9988 XYZ',
            'jenis_kendaraan' => 'Honda Vario 160',
            'kategori' => 'mitra',
            'no_wa' => '08123456789',
            'termin_hari' => 30,
        ]);
    }
}
