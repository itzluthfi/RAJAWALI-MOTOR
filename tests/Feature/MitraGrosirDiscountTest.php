<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\Group;
use App\Models\Satuan;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class MitraGrosirDiscountTest extends TestCase
{
    use RefreshDatabase;

    private function kasir(): User
    {
        return User::factory()->create(['username' => 'kasir_' . Str::random(6), 'peran' => 'kasir']);
    }

    public function test_pos_includes_harga_grosir_and_customer_kategori(): void
    {
        $this->actingAs($this->kasir());

        $mitra = Customer::create([
            'nama' => 'Mitra Bengkel',
            'kategori' => 'mitra',
            'termin_hari' => 30,
        ]);

        $group = Group::create(['nama' => 'Oli']);
        $satuan = Satuan::create(['nama' => 'BOTOL']);

        $barang = Barang::create([
            'kode' => 'OLIMPX',
            'nama' => 'OLI MPX2',
            'group_id' => $group->id,
            'satuan_id' => $satuan->id,
            'hpp' => 45000,
            'harga_eceran' => 60000,
            'harga_grosir' => 52000,
            'stok_minimum' => 5,
        ]);

        $response = $this->get('/admin/kasir');
        $response->assertOk();
        $response->assertSee('harga_grosir');
        $response->assertSee('mitra');
    }
}
