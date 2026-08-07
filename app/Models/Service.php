<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $table = 'services';

    protected $fillable = [
        'nomor_dokumen',
        'tanggal_masuk',
        'customer_id',
        'montir_id',
        'sales_id',
        'merk_type',
        'no_rangka',
        'no_mesin',
        'kelengkapan',
        'keluhan',
        'catatan',
        'status',
        'repaired_by',
        'supplier_id',
        'tanggal_kirim',
        'tanggal_kembali',
        'grand_total_supplier',
        'grand_total_nett',
        'status_lunas',
    ];

    protected function casts(): array
    {
        return [
            'tanggal_masuk' => 'date',
            'tanggal_kirim' => 'date',
            'tanggal_kembali' => 'date',
            'grand_total_supplier' => 'decimal:2',
            'grand_total_nett' => 'decimal:2',
            'status_lunas' => 'boolean',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function montir(): BelongsTo
    {
        return $this->belongsTo(User::class, 'montir_id');
    }

    public function sales(): BelongsTo
    {
        return $this->belongsTo(Sales::class, 'sales_id');
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class, 'supplier_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(ServiceDetail::class, 'service_id');
    }

    public function jasas(): HasMany
    {
        return $this->hasMany(ServiceJasa::class, 'service_id');
    }

    /**
     * Generator nomor dokumen otomatis (contoh: SV2026080001)
     */
    public static function buatNomorDokumen(): string
    {
        $prefix = 'SV' . date('Ym');
        $last = static::query()->where('nomor_dokumen', 'LIKE', "{$prefix}%")->orderByDesc('id')->first();

        if ($last) {
            $lastNumber = (int) substr($last->nomor_dokumen, -4);
            $newNumber = str_pad((string) ($lastNumber + 1), 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return "{$prefix}{$newNumber}";
    }
}
