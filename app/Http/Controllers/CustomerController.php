<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $query = Customer::query();

        if ($cari = $request->string('cari')->trim()->value()) {
            $query->where(function ($q) use ($cari) {
                $q->where('nama', 'like', "%{$cari}%")
                    ->orWhere('plat_nomor', 'like', "%{$cari}%")
                    ->orWhere('jenis_kendaraan', 'like', "%{$cari}%")
                    ->orWhere('no_wa', 'like', "%{$cari}%")
                    ->orWhere('telepon', 'like', "%{$cari}%");
            });
        }

        $perPage = max(5, min(100, (int) $request->input('per_page', 25)));

        return view('customer.index', [
            'customer' => $query->orderBy('nama')->paginate($perPage)->withQueryString(),
            'filter' => $request->only('cari'),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        return $this->simpan($request, null);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        return $this->simpan($request, $customer);
    }

    private function simpan(Request $request, ?Customer $customer): RedirectResponse
    {
        try {
            $data = $request->validate([
                'nama' => ['required', 'string', 'max:150'],
                'plat_nomor' => ['nullable', 'string', 'max:30'],
                'jenis_kendaraan' => ['nullable', 'string', 'max:100'],
                'kategori' => ['nullable', 'string', 'in:umum,mitra,grosir'],
                'telepon' => ['nullable', 'string', 'max:30'],
                'no_wa' => ['nullable', 'string', 'max:30'],
                'alamat' => ['nullable', 'string', 'max:255'],
                'termin_hari' => ['required', 'integer', 'min:0', 'max:365'],
            ]);

            $data['kategori'] = $data['kategori'] ?? 'umum';

            DB::transaction(function () use ($data, $customer) {
                $customer ? $customer->update($data) : Customer::create($data);
            }, attempts: 3);

            return back()->with('sukses', $customer ? 'Customer berhasil diperbarui.' : 'Customer berhasil disimpan.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Gagal menyimpan customer', ['pesan' => $e->getMessage()]);

            return back()->withInput()->withErrors(['nama' => 'Gagal menyimpan customer. Silakan coba lagi.']);
        }
    }

    public function toggleAktif(Customer $customer): RedirectResponse
    {
        try {
            DB::transaction(fn () => $customer->update(['aktif' => ! $customer->aktif]), attempts: 3);

            return back()->with('sukses', $customer->aktif ? 'Customer diaktifkan kembali.' : 'Customer dinonaktifkan.');
        } catch (Throwable $e) {
            Log::error('Gagal mengubah status customer', ['pesan' => $e->getMessage()]);

            return back()->withErrors(['nama' => 'Gagal mengubah status customer.']);
        }
    }
}
