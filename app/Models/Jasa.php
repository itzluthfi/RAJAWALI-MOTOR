<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jasa extends Model
{
    protected $table = 'jasas';

    protected $fillable = [
        'kode',
        'nama',
        'kategori',
        'tarif',
        'komisi_montir',
        'keterangan',
        'aktif',
    ];

    protected function casts(): array
    {
        return [
            'tarif' => 'decimal:2',
            'komisi_montir' => 'decimal:2',
            'aktif' => 'boolean',
        ];
    }
}
