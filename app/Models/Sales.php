<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sales extends Model
{
    protected $table = 'sales';

    protected $fillable = ['kode_sales', 'nama', 'alamat', 'kota', 'telepon', 'persentase_komisi', 'aktif'];

    protected function casts(): array
    {
        return [
            'aktif' => 'boolean',
            'persentase_komisi' => 'decimal:2',
        ];
    }
}
