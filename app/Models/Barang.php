<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Barang extends Model
{
    protected $fillable = [
        'kode', 'nama', 'group_id', 'sub_group_id', 'satuan_id',
        'harga_beli_terakhir', 'hpp', 'harga_eceran', 'harga_grosir',
        'min_qty_grosir_1', 'harga_grosir_1', 'min_qty_grosir_2', 'harga_grosir_2',
        'stok_minimum', 'lokasi_rak', 'aktif',
    ];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
            'harga_beli_terakhir' => 'decimal:2',
            'hpp' => 'decimal:2',
            'harga_eceran' => 'decimal:2',
            'harga_grosir' => 'decimal:2',
            'min_qty_grosir_1' => 'decimal:3',
            'harga_grosir_1' => 'decimal:2',
            'min_qty_grosir_2' => 'decimal:3',
            'harga_grosir_2' => 'decimal:2',
            'stok_minimum' => 'decimal:3',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }

    public function subGroup(): BelongsTo
    {
        return $this->belongsTo(SubGroup::class);
    }

    public function satuan(): BelongsTo
    {
        return $this->belongsTo(Satuan::class);
    }

    public function barcodes(): HasMany
    {
        return $this->hasMany(BarangBarcode::class);
    }

    public function stokMutasi(): HasMany
    {
        return $this->hasMany(StokMutasi::class);
    }
}
