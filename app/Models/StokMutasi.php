<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StokMutasi extends Model
{
    protected $fillable = [
        'barang_id', 'tanggal', 'jenis_mutasi', 'no_dokumen', 'masuk', 'keluar', 'hpp', 'keterangan',
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
            'masuk' => 'decimal:3',
            'keluar' => 'decimal:3',
            'hpp' => 'decimal:2',
        ];
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
