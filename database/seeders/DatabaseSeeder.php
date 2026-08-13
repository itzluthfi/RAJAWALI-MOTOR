<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            PengaturanTokoSeeder::class,
            SatuanSeeder::class,
            GroupSeeder::class,
            SubGroupSeeder::class,
            CustomerSeeder::class,
            SupplierSeeder::class,
            SalesSeeder::class,
            BarangSeeder::class,
            PembelianSeeder::class,
            ReturSeeder::class,
            AuditLogSeeder::class,
        ]);
    }
}
