<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Barang;
use App\Models\StokMutasi;

class StokService
{
    /**
     * Stok saat ini = agregasi stok_mutasis (masuk - keluar).
     * Dilarang membaca/menyimpan kolom stok langsung pada tabel barang.
     */
    public function stokSaatIni(Barang $barang): float
    {
        return (float) StokMutasi::query()
            ->where('barang_id', $barang->id)
            ->selectRaw('COALESCE(SUM(masuk) - SUM(keluar), 0) as saldo')
            ->value('saldo');
    }

    /**
     * Ambil stok saat ini untuk banyak barang sekaligus (hindari N+1).
     *
     * @param  iterable<int>  $barangIds
     * @return array<int, float>
     */
    public function stokBanyakBarang(iterable $barangIds): array
    {
        return StokMutasi::query()
            ->whereIn('barang_id', $barangIds)
            ->selectRaw('barang_id, COALESCE(SUM(masuk) - SUM(keluar), 0) as saldo')
            ->groupBy('barang_id')
            ->pluck('saldo', 'barang_id')
            ->map(fn ($saldo) => (float) $saldo)
            ->all();
    }
}
