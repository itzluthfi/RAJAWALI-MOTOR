<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Sales;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class SalesController extends Controller
{
    public function index(Request $request): View
    {
        $query = Sales::query();

        if ($cari = $request->string('cari')->trim()->value()) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                    ->orWhere('telepon', 'like', "%{$cari}%");
            });
        }

        return view('sales.index', [
            'sales' => $query->orderBy('nama')->paginate(15)->withQueryString(),
            'filter' => $request->only('cari'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->simpan($request, null);
    }

    public function update(Request $request, Sales $sales): RedirectResponse
    {
        return $this->simpan($request, $sales);
    }

    private function simpan(Request $request, ?Sales $sales): RedirectResponse
    {
        try {
            $data = $request->validate([
                'nama' => ['required', 'string', 'max:150'],
                'telepon' => ['nullable', 'string', 'max:30'],
                'persentase_komisi' => ['required', 'numeric', 'min:0', 'max:100'],
            ]);

            DB::transaction(function () use ($data, $sales) {
                $sales ? $sales->update($data) : Sales::create($data);
            }, attempts: 3);

            return back()->with('sukses', $sales ? 'Sales berhasil diperbarui.' : 'Sales berhasil disimpan.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Gagal menyimpan sales', ['pesan' => $e->getMessage()]);

            return back()->withInput()->withErrors(['nama' => 'Gagal menyimpan sales. Silakan coba lagi.']);
        }
    }

    public function toggleAktif(Sales $sales): RedirectResponse
    {
        try {
            DB::transaction(fn () => $sales->update(['aktif' => ! $sales->aktif]), attempts: 3);

            return back()->with('sukses', $sales->aktif ? 'Sales diaktifkan kembali.' : 'Sales dinonaktifkan.');
        } catch (Throwable $e) {
            Log::error('Gagal mengubah status sales', ['pesan' => $e->getMessage()]);

            return back()->withErrors(['nama' => 'Gagal mengubah status sales.']);
        }
    }
}
