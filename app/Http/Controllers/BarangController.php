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

        $barang = $query->orderBy('nama')->paginate(15)->withQueryString();

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

    public function store(Request $request): RedirectResponse
    {
        return $this->simpan($request, null);
    }

    public function update(Request $request, Barang $barang): RedirectResponse
    {
        return $this->simpan($request, $barang);
    }

    private function simpan(Request $request, ?Barang $barang): RedirectResponse
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
                'stok_minimum' => ['required', 'numeric', 'min:0'],
                'lokasi_rak' => ['nullable', 'string', 'max:50'],
            ]);

            if ($request->user()->peran === 'owner') {
                $data['hpp'] = $request->validate(['hpp' => ['required', 'numeric', 'min:0']])['hpp'];
            }

            DB::transaction(function () use ($data, $barang) {
                if ($barang) {
                    $barang->update($data);
                } else {
                    Barang::create($data + ['harga_beli_terakhir' => $data['hpp'] ?? 0]);
                }
            }, attempts: 3);

            return back()->with('sukses', $barang ? 'Barang berhasil diperbarui.' : 'Barang berhasil disimpan.');
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
}
