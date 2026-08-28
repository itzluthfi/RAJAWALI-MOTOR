<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Group;
use App\Models\Satuan;
use App\Models\SubGroup;
use App\Services\StokService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class BarangController extends Controller
{
    public function index(Request $request, StokService $stokService): View
    {
        $query = Barang::query()->with(['group', 'satuan', 'barcodes']);

        if ($cari = $request->string('cari')->trim()->value()) {
            $query->where(function ($q) use ($cari) {
                $q->where('kode', 'like', "%{$cari}%")
                    ->orWhere('nama', 'like', "%{$cari}%")
                    ->orWhereHas('barcodes', fn ($b) => $b->where('barcode', 'like', "%{$cari}%"));
            });
        }

        if ($groupId = $request->integer('group_id')) {
            $query->where('group_id', $groupId);
        }

        $perPage = max(5, min(100, (int) $request->input('per_page', 25)));
        $barang = $query->orderBy('nama')->paginate($perPage)->withQueryString();

        $stok = $stokService->stokBanyakBarang($barang->pluck('id'));

        if ($request->boolean('stok_menipis')) {
            $barang->setCollection(
                $barang->getCollection()->filter(
                    fn (Barang $b) => ($stok[$b->id] ?? 0) <= (float) $b->stok_minimum
                )->values()
            );
        }

        return view('barang.index', [
            'barang' => $barang,
            'stok' => $stok,
            'groupList' => Group::orderBy('nama')->get(),
            'subGroupList' => SubGroup::orderBy('nama')->get(),
            'satuanList' => Satuan::orderBy('nama')->get(),
            'filter' => $request->only(['cari', 'group_id', 'stok_menipis']),
        ]);
    }

    public function store(Request $request, StokService $stokService): RedirectResponse
    {
        return $this->simpan($request, null, $stokService);
    }

    public function update(Request $request, Barang $barang, StokService $stokService): RedirectResponse
    {
        return $this->simpan($request, $barang, $stokService);
    }

    private function simpan(Request $request, ?Barang $barang, StokService $stokService): RedirectResponse
    {
        try {
            $data = $request->validate([
                'kode' => ['required', 'string', 'max:50', Rule::unique('barangs', 'kode')->ignore($barang)],
                'nama' => ['required', 'string', 'max:150'],
                'group_id' => ['required', 'exists:groups,id'],
                'sub_group_id' => ['nullable', 'exists:sub_groups,id'],
                'satuan_id' => ['required', 'exists:satuans,id'],
                'harga_eceran' => ['required', 'numeric', 'min:0'],
                'harga_grosir' => ['required', 'numeric', 'min:0'],
                'min_qty_grosir_1' => ['nullable', 'numeric', 'min:1'],
                'harga_grosir_1' => ['nullable', 'numeric', 'min:0'],
                'min_qty_grosir_2' => ['nullable', 'numeric', 'min:1'],
                'harga_grosir_2' => ['nullable', 'numeric', 'min:0'],
                'stok_minimum' => ['required', 'numeric', 'min:0'],
                'stok_awal' => ['nullable', 'numeric', 'min:0'],
                'stok_saat_ini' => ['nullable', 'numeric', 'min:0'],
                'lokasi_rak' => ['nullable', 'string', 'max:50'],
                'barcode' => ['nullable', 'string', 'max:50'],
            ]);

            if (in_array($request->user()->peran, ['owner', 'admin'], true)) {
                $data['hpp'] = $request->validate(['hpp' => ['required', 'numeric', 'min:0']])['hpp'];
            }

            $barcodeInput = $data['barcode'] ?? null;
            $stokAwal = (float) ($data['stok_awal'] ?? 0);
            $stokPenyesuaian = isset($data['stok_saat_ini']) ? (float) $data['stok_saat_ini'] : null;
            unset($data['barcode'], $data['stok_awal'], $data['stok_saat_ini']);

            DB::transaction(function () use ($data, $barang, $barcodeInput, $stokAwal, $stokPenyesuaian, $stokService) {
                if ($barang) {
                    $barang->update($data);
                    if ($barcodeInput) {
                        $barang->barcodes()->update(['utama' => false]);
                        $barcodeObj = $barang->barcodes()->where('barcode', $barcodeInput)->first();
                        if ($barcodeObj) {
                            $barcodeObj->update(['utama' => true]);
                        } else {
                            $barang->barcodes()->create(['barcode' => $barcodeInput, 'utama' => true]);
                        }
                    }

                    // Penyesuaian stok saat edit barang
                    if ($stokPenyesuaian !== null) {
                        $stokLama = $stokService->stokSaatIni($barang);
                        $selisih = $stokPenyesuaian - $stokLama;
                        if (abs($selisih) > 0.0001) {
                            \App\Models\StokMutasi::create([
                                'barang_id' => $barang->id,
                                'tanggal' => now()->toDateString(),
                                'jenis_mutasi' => 'koreksi',
                                'no_dokumen' => 'KOR-' . date('YmdHis'),
                                'masuk' => $selisih > 0 ? $selisih : 0,
                                'keluar' => $selisih < 0 ? abs($selisih) : 0,
                                'hpp' => $data['hpp'] ?? $barang->hpp ?? 0,
                                'keterangan' => 'Penyesuaian stok dari menu Master Barang',
                            ]);
                        }
                    }
                } else {
                    $baru = Barang::create($data + ['harga_beli_terakhir' => $data['hpp'] ?? 0]);
                    if ($barcodeInput) {
                        $baru->barcodes()->create(['barcode' => $barcodeInput, 'utama' => true]);
                    }
                    if ($stokAwal > 0) {
                        \App\Models\StokMutasi::create([
                            'barang_id' => $baru->id,
                            'tanggal' => now()->toDateString(),
                            'jenis_mutasi' => 'stok_awal',
                            'no_dokumen' => 'STOK-AWAL-' . $baru->kode,
                            'masuk' => $stokAwal,
                            'keluar' => 0,
                            'hpp' => $data['hpp'] ?? 0,
                            'keterangan' => 'Stok awal saat pendaftaran barang',
                        ]);
                    }
                }
            }, attempts: 3);

            return back()->with('sukses', $barang ? 'Barang & data stok berhasil diperbarui.' : 'Barang & stok awal berhasil disimpan.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Gagal menyimpan barang', ['pesan' => $e->getMessage()]);

            return back()->withInput()->withErrors(['kode' => 'Gagal menyimpan barang. Silakan coba lagi.']);
        }
    }

    public function toggleAktif(Barang $barang, StokService $stokService): RedirectResponse
    {
        try {
            if ($barang->aktif && $stokService->stokSaatIni($barang) > 0) {
                return back()->withErrors([
                    'kode' => "Barang {$barang->nama} masih memiliki stok dan tidak bisa dinonaktifkan.",
                ]);
            }

            DB::transaction(fn () => $barang->update(['aktif' => ! $barang->aktif]), attempts: 3);

            return back()->with('sukses', $barang->aktif ? 'Barang diaktifkan kembali.' : 'Barang dinonaktifkan.');
        } catch (Throwable $e) {
            Log::error('Gagal mengubah status barang', ['pesan' => $e->getMessage()]);

            return back()->withErrors(['kode' => 'Gagal mengubah status barang. Silakan coba lagi.']);
        }
    }

    public function tambahBarcode(Request $request, Barang $barang): RedirectResponse
    {
        try {
            $data = $request->validate([
                'barcode' => ['required', 'string', 'max:50', 'unique:barang_barcodes,barcode'],
            ]);

            DB::transaction(function () use ($data, $barang) {
                $barang->barcodes()->create(['barcode' => $data['barcode'], 'utama' => $barang->barcodes()->count() === 0]);
            }, attempts: 3);

            return back()->with('sukses', 'Barcode berhasil ditambahkan.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Gagal menambah barcode', ['pesan' => $e->getMessage()]);

            return back()->withErrors(['barcode' => 'Gagal menambah barcode. Mungkin sudah dipakai barang lain.']);
        }
    }

    public function jadikanBarcodeUtama(Barang $barang, int $barcodeId): RedirectResponse
    {
        DB::transaction(function () use ($barang, $barcodeId) {
            $barang->barcodes()->update(['utama' => false]);
            $barang->barcodes()->where('id', $barcodeId)->update(['utama' => true]);
        }, attempts: 3);

        return back()->with('sukses', 'Barcode utama berhasil diubah.');
    }

    public function cetakLabel(Request $request, Barang $barang): View
    {
        $barang->load(['barcodes', 'group', 'satuan']);
        $jumlah = max(1, min(100, (int) $request->input('jumlah', 1)));
        $tipe = $request->input('tipe', 'barcode');
        $barcodeUtama = $barang->barcodes->firstWhere('utama', true)?->barcode 
            ?? $barang->barcodes->first()?->barcode 
            ?? $barang->kode;

        return view('print.label-barcode', [
            'barang' => $barang,
            'jumlah' => $jumlah,
            'tipe' => $tipe,
            'barcodeUtama' => $barcodeUtama,
            'pengaturan' => \App\Models\PengaturanToko::current(),
        ]);
    }
}
