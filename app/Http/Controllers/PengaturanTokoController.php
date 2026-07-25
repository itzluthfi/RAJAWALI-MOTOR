<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\PengaturanToko;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class PengaturanTokoController extends Controller
{
    public function edit(): View
    {
        return view('pengaturan.toko', [
            'pengaturan' => PengaturanToko::current(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        try {
            $data = $request->validate([
                'nama_toko' => ['required', 'string', 'max:150'],
                'alamat' => ['nullable', 'string', 'max:255'],
                'telepon' => ['nullable', 'string', 'max:30'],
                'format_nota' => ['required', 'string', 'max:50'],
                'batas_diskon_kasir_persen' => ['required', 'numeric', 'min:0', 'max:100'],
                'izinkan_stok_minus' => ['nullable', 'boolean'],
            ]);

            $data['izinkan_stok_minus'] = $request->boolean('izinkan_stok_minus');

            DB::transaction(function () use ($data) {
                PengaturanToko::current()->update($data);
            }, attempts: 3);

            return redirect()->route('pengaturan.toko')->with('sukses', 'Pengaturan toko berhasil disimpan.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Gagal menyimpan pengaturan toko', ['pesan' => $e->getMessage()]);

            return back()->withInput()->withErrors([
                'nama_toko' => 'Gagal menyimpan pengaturan. Silakan coba lagi.',
            ]);
        }
    }
}
