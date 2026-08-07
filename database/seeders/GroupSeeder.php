<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Group;
use Illuminate\Database\Seeder;

class GroupSeeder extends Seeder
{
    public function run(): void
    {
        foreach (['Sparepart', 'Oli', 'Aksesoris', 'Jasa & Service'] as $nama) {
            Group::updateOrCreate(['nama' => $nama]);
        }
    }
}
