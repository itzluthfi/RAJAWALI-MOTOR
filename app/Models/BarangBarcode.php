<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BarangBarcode extends Model
{
    protected $fillable = ['barang_id', 'barcode', 'utama'];

    protected function casts(): array
    {
        return [
            'utama' => 'boolean',
        ];
    }

    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class);
    }
}
