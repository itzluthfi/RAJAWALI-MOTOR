<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class SupplierController extends Controller
{
    public function index(Request $request): View
    {
        $query = Supplier::query();

        if ($cari = $request->string('cari')->trim()->value()) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                    ->orWhere('telepon', 'like', "%{$cari}%");
            });
        }

        $perPage = max(5, min(100, (int) $request->input('per_page', 25)));

        return view('supplier.index', [
            'supplier' => $query->orderBy('nama')->paginate($perPage)->withQueryString(),
            'filter' => $request->only('cari'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->simpan($request, null);
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        return $this->simpan($request, $supplier);
    }

    private function simpan(Request $request, ?Supplier $supplier): RedirectResponse
    {
        try {
            $data = $request->validate([
                'nama' => ['required', 'string', 'max:150'],
                'telepon' => ['nullable', 'string', 'max:30'],
                'alamat' => ['nullable', 'string', 'max:255'],
            ]);

            DB::transaction(function () use ($data, $supplier) {
                $supplier ? $supplier->update($data) : Supplier::create($data);
            }, attempts: 3);

            return back()->with('sukses', $supplier ? 'Supplier berhasil diperbarui.' : 'Supplier berhasil disimpan.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Gagal menyimpan supplier', ['pesan' => $e->getMessage()]);

            return back()->withInput()->withErrors(['nama' => 'Gagal menyimpan supplier. Silakan coba lagi.']);
        }
    }

    public function toggleAktif(Supplier $supplier): RedirectResponse
    {
        try {
            DB::transaction(fn () => $supplier->update(['aktif' => ! $supplier->aktif]), attempts: 3);

            return back()->with('sukses', $supplier->aktif ? 'Supplier diaktifkan kembali.' : 'Supplier dinonaktifkan.');
        } catch (Throwable $e) {
            Log::error('Gagal mengubah status supplier', ['pesan' => $e->getMessage()]);

            return back()->withErrors(['nama' => 'Gagal mengubah status supplier.']);
        }
    }
}
