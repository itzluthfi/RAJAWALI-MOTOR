<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceJasa extends Model
{
    protected $table = 'service_jasas';

    protected $fillable = [
        'service_id',
        'nama_jasa',
        'harga_supplier',
        'harga_nett',
    ];

    protected function casts(): array
    {
        return [
            'harga_supplier' => 'decimal:2',
            'harga_nett' => 'decimal:2',
        ];
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class, 'service_id');
    }
}
