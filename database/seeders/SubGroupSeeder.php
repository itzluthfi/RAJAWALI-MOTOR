<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Group;
use App\Models\SubGroup;
use Illuminate\Database\Seeder;

class SubGroupSeeder extends Seeder
{
    public function run(): void
    {
        $sparepart = Group::where('nama', 'Sparepart')->firstOrFail();

        foreach (['Rem & CVT', 'Kelistrikan', 'Kaki-Kaki'] as $nama) {
            SubGroup::updateOrCreate(['group_id' => $sparepart->id, 'nama' => $nama]);
        }
    }
}
