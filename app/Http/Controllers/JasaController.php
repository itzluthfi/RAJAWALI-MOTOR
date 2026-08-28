<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Jasa;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class JasaController extends Controller
{
    public function index(Request $request): View
    {
        $query = Jasa::query();

        if ($cari = $request->string('cari')->trim()->value()) {
            $query->where(function ($q) use ($cari) {
                $q->where('kode', 'like', "%{$cari}%")
                    ->orWhere('nama', 'like', "%{$cari}%")
                    ->orWhere('kategori', 'like', "%{$cari}%");
            });
        }

        if ($kategori = $request->string('kategori')->trim()->value()) {
            if ($kategori !== 'semua') {
                $query->where('kategori', $kategori);
            }
        }

        $perPage = max(5, min(100, (int) $request->input('per_page', 25)));
        $jasas = $query->orderBy('nama')->paginate($perPage)->withQueryString();

        $kategoriList = Jasa::select('kategori')->distinct()->whereNotNull('kategori')->pluck('kategori');

        return view('jasa.index', [
            'jasas' => $jasas,
            'kategoriList' => $kategoriList,
            'filter' => [
                'cari' => $request->input('cari', ''),
                'kategori' => $request->input('kategori', 'semua'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->simpan($request, null);
    }

    public function update(Request $request, Jasa $jasa): RedirectResponse
    {
        return $this->simpan($request, $jasa);
    }

    private function simpan(Request $request, ?Jasa $jasa): RedirectResponse
    {
        try {
            $data = $request->validate([
                'kode' => [
                    'required',
                    'string',
                    'max:50',
                    Rule::unique('jasas', 'kode')->ignore($jasa?->id),
                ],
                'nama' => ['required', 'string', 'max:150'],
                'kategori' => ['nullable', 'string', 'max:100'],
                'tarif' => ['required', 'numeric', 'min:0'],
                'komisi_montir' => ['nullable', 'numeric', 'min:0'],
                'keterangan' => ['nullable', 'string', 'max:255'],
            ]);

            $data['komisi_montir'] = $data['komisi_montir'] ?? 0;

            DB::transaction(function () use ($data, $jasa) {
                if ($jasa) {
                    $jasa->update($data);
                    AuditLog::catat('Ubah Jasa Servis', 'Master Jasa', $jasa->kode, "Memperbarui tarif jasa: {$jasa->nama}");
                } else {
                    $data['aktif'] = true;
                    $baru = Jasa::create($data);
                    AuditLog::catat('Tambah Jasa Servis', 'Master Jasa', $baru->kode, "Menambahkan jasa baru: {$baru->nama}");
                }
            });

            return redirect()->route('jasa.index')->with('sukses', $jasa ? 'Tarif jasa servis berhasil diperbarui.' : 'Jasa servis baru berhasil ditambahkan.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Gagal menyimpan jasa servis', ['pesan' => $e->getMessage()]);

            return back()->withInput()->withErrors([
                'error' => 'Gagal menyimpan tarif jasa: ' . $e->getMessage(),
            ]);
        }
    }

    public function toggleAktif(Jasa $jasa): RedirectResponse
    {
        $jasa->update(['aktif' => ! $jasa->aktif]);
        $status = $jasa->aktif ? 'diaktifkan' : 'dinonaktifkan';

        AuditLog::catat(
            'Toggle Status Jasa',
            'Master Jasa',
            $jasa->kode,
            "Jasa {$jasa->nama} {$status}"
        );

        return back()->with('sukses', "Jasa {$jasa->nama} berhasil {$status}.");
    }

    public function destroy(Jasa $jasa): RedirectResponse
    {
        try {
            $nama = $jasa->nama;
            $jasa->delete();

            AuditLog::catat('Hapus Jasa Servis', 'Master Jasa', $jasa->kode, "Menghapus jasa: {$nama}");

            return back()->with('sukses', "Jasa {$nama} berhasil dihapus.");
        } catch (Throwable $e) {
            return back()->withErrors(['error' => 'Gagal menghapus jasa: ' . $e->getMessage()]);
        }
    }
}
