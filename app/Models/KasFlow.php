<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KasFlow extends Model
{
    protected $table = 'kas_flows';

    protected $fillable = [
        'tanggal',
        'tipe',
        'sumber',
        'kategori',
        'no_referensi',
        'nominal',
        'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'nominal' => 'decimal:2',
        ];
    }
}
