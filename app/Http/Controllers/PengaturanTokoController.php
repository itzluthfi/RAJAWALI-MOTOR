<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\PengaturanToko;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
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
                'slogan' => ['nullable', 'string', 'max:255'],
                'alamat' => ['nullable', 'string', 'max:255'],
                'telepon' => ['nullable', 'string', 'max:30'],
                'footer_struk' => ['nullable', 'string', 'max:500'],
                'format_nota' => ['required', 'string', 'max:50'],
                'batas_diskon_kasir_persen' => ['required', 'numeric', 'min:0', 'max:100'],
                'izinkan_stok_minus' => ['nullable', 'boolean'],
                'printer_struk_aktif' => ['nullable', 'boolean'],
                'printer_faktur_aktif' => ['nullable', 'boolean'],
                'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,svg', 'max:2048'],
                'hapus_logo' => ['nullable', 'boolean'],
            ]);

            $pengaturan = PengaturanToko::current();

            // Handle hapus logo
            if ($request->boolean('hapus_logo') && $pengaturan->logo_path) {
                Storage::disk('public')->delete($pengaturan->logo_path);
                $data['logo_path'] = null;
            }

            // Handle upload logo baru
            if ($request->hasFile('logo') && $request->file('logo')->isValid()) {
                if ($pengaturan->logo_path) {
                    Storage::disk('public')->delete($pengaturan->logo_path);
                }
                $path = $request->file('logo')->store('logo', 'public');
                $data['logo_path'] = $path;
            }

            unset($data['logo'], $data['hapus_logo']);

            $data['izinkan_stok_minus'] = $request->boolean('izinkan_stok_minus');
            $data['printer_struk_aktif'] = $request->boolean('printer_struk_aktif');
            $data['printer_faktur_aktif'] = $request->boolean('printer_faktur_aktif');

            DB::transaction(function () use ($pengaturan, $data) {
                $pengaturan->update($data);
            }, attempts: 3);

            AuditLog::catat(
                'Ubah Pengaturan Toko',
                'Pengaturan',
                (string) $pengaturan->id,
                "Mengubah identitas toko, logo, dan konfigurasi POS"
            );

            return redirect()->route('pengaturan.toko')->with('sukses', 'Pengaturan web & profil toko berhasil disimpan.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Gagal menyimpan pengaturan toko', ['pesan' => $e->getMessage()]);

            return back()->withInput()->withErrors([
                'error' => 'Gagal menyimpan pengaturan: ' . $e->getMessage(),
            ]);
        }
    }
}
