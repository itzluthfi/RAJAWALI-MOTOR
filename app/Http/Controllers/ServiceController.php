<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\Customer;
use App\Models\KasFlow;
use App\Models\Service;
use App\Models\ServiceDetail;
use App\Models\ServiceJasa;
use App\Models\StokMutasi;
use App\Models\Supplier;
use App\Models\Sales;
use App\Models\User;
use App\Services\StokService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class ServiceController extends Controller
{
    public function index(Request $request): View
    {
        $query = Service::query()->with(['customer', 'montir']);

        // Filter tanggal
        $dariTanggal = $request->input('dari_tanggal', now()->startOfMonth()->toDateString());
        $sampaiTanggal = $request->input('sampai_tanggal', now()->toDateString());
        $query->whereBetween('tanggal_masuk', [$dariTanggal, $sampaiTanggal]);

        // Filter status
        if ($request->filled('status') && $request->status !== 'semua') {
            $query->where('status', $request->status);
        }

        // Search
        if ($search = $request->string('cari')->trim()->value()) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_dokumen', 'LIKE', "%{$search}%")
                  ->orWhereHas('customer', fn ($c) => $c->where('nama', 'LIKE', "%{$search}%"));
            });
        }

        $services = $query->latest('id')->paginate(15)->withQueryString();

        return view('service.index', [
            'services' => $services,
            'filter' => [
                'dari_tanggal' => $dariTanggal,
                'sampai_tanggal' => $sampaiTanggal,
                'status' => $request->input('status', 'semua'),
                'cari' => $request->input('cari', ''),
            ]
        ]);
    }

    public function create(StokService $stokService): View
    {
        $customers = Customer::where('aktif', true)->orderBy('nama')->get();
        $montirs = User::where('aktif', true)->whereIn('peran', ['montir', 'admin', 'owner'])->orderBy('name')->get();
        $suppliers = Supplier::where('aktif', true)->orderBy('nama')->get();
        $sales = Sales::where('aktif', true)->orderBy('nama')->get();

        $barang = Barang::where('aktif', true)->orderBy('nama')->get();
        $stok = $stokService->stokBanyakBarang($barang->pluck('id'));

        $daftarBarang = $barang->map(fn (Barang $b) => [
            'id' => $b->id,
            'kode' => $b->kode,
            'nama' => $b->nama,
            'harga' => (float) $b->harga_eceran,
            'hpp' => (float) $b->hpp,
            'stok' => $stok[$b->id] ?? 0.0,
        ])->values();

        $daftarCustomerJson = $customers->map(fn (Customer $c) => [
            'id' => $c->id,
            'nama' => $c->nama,
            'telepon' => $c->telepon ?? $c->no_wa ?? '',
            'plat' => $c->plat_nomor ?? '',
            'motor' => $c->jenis_kendaraan ?? '',
        ])->values();

        return view('service.form', [
            'customers' => $customers,
            'montirs' => $montirs,
            'suppliers' => $suppliers,
            'sales' => $sales,
            'daftarCustomerJson' => $daftarCustomerJson,
            'daftarBarangJson' => $daftarBarang,
            'nomorDokumenBaru' => Service::buatNomorDokumen(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        try {
            $validated = $request->validate([
                'tanggal_masuk' => ['required', 'date'],
                'customer_id' => ['required', 'exists:customers,id'],
                'telepon' => ['nullable', 'string', 'max:25'],
                'montir_id' => ['nullable', 'exists:users,id'],
                'sales_id' => ['nullable', 'exists:sales,id'],
                'merk_type' => ['nullable', 'string', 'max:100'],
                'no_rangka' => ['nullable', 'string', 'max:100'],
                'no_mesin' => ['nullable', 'string', 'max:100'],
                'kelengkapan' => ['nullable', 'string', 'max:255'],
                'keluhan' => ['nullable', 'string'],
                'catatan' => ['nullable', 'string'],
                'repaired_by' => ['required', 'in:intern,extern'],
                'supplier_id' => ['nullable', 'required_if:repaired_by,extern', 'exists:suppliers,id'],
                'tanggal_kirim' => ['nullable', 'date'],
                'tanggal_kembali' => ['nullable', 'date'],
                
                // Spareparts
                'items' => ['nullable', 'array'],
                'items.*.barang_id' => ['required', 'exists:barangs,id'],
                'items.*.qty' => ['required', 'numeric', 'min:0.001'],
                'items.*.harga' => ['required', 'numeric', 'min:0'],
                
                // Jasas
                'jasas' => ['nullable', 'array'],
                'jasas.*.nama_jasa' => ['required', 'string', 'max:150'],
                'jasas.*.harga_supplier' => ['nullable', 'numeric', 'min:0'],
                'jasas.*.harga_nett' => ['required', 'numeric', 'min:0'],
            ]);

            // Update nomor WA/telepon customer jika diisi
            if (!empty($validated['telepon'])) {
                $cust = Customer::find($validated['customer_id']);
                if ($cust && (empty($cust->telepon) || $cust->telepon !== $validated['telepon'])) {
                    $cust->update([
                        'telepon' => $validated['telepon'],
                        'no_wa' => $validated['telepon'],
                    ]);
                }
            }

            DB::transaction(function () use ($validated) {
                $nomorDokumen = Service::buatNomorDokumen();
                
                $service = Service::create([
                    'nomor_dokumen' => $nomorDokumen,
                    'tanggal_masuk' => $validated['tanggal_masuk'],
                    'customer_id' => $validated['customer_id'],
                    'montir_id' => $validated['montir_id'] ?? null,
                    'sales_id' => $validated['sales_id'] ?? null,
                    'merk_type' => $validated['merk_type'] ?? null,
                    'no_rangka' => $validated['no_rangka'] ?? null,
                    'no_mesin' => $validated['no_mesin'] ?? null,
                    'kelengkapan' => $validated['kelengkapan'] ?? null,
                    'keluhan' => $validated['keluhan'] ?? null,
                    'catatan' => $validated['catatan'] ?? null,
                    'repaired_by' => $validated['repaired_by'],
                    'supplier_id' => $validated['repaired_by'] === 'extern' ? ($validated['supplier_id'] ?? null) : null,
                    'tanggal_kirim' => $validated['repaired_by'] === 'extern' ? ($validated['tanggal_kirim'] ?? null) : null,
                    'tanggal_kembali' => $validated['repaired_by'] === 'extern' ? ($validated['tanggal_kembali'] ?? null) : null,
                    'status' => 'masuk',
                    'status_lunas' => false,
                ]);

                $totalSupplier = 0;
                $totalNett = 0;

                // Save spareparts
                if (!empty($validated['items'])) {
                    foreach ($validated['items'] as $item) {
                        $barang = Barang::findOrFail($item['barang_id']);
                        $qty = (float) $item['qty'];
                        $hargaJual = (float) $item['harga'];
                        $subtotal = $qty * $hargaJual;

                        ServiceDetail::create([
                            'service_id' => $service->id,
                            'barang_id' => $barang->id,
                            'qty' => $qty,
                            'harga_jual' => $hargaJual,
                            'hpp' => (float) $barang->hpp,
                            'subtotal' => $subtotal,
                        ]);

                        $totalNett += $subtotal;
                    }
                }

                // Save jasas
                if (!empty($validated['jasas'])) {
                    foreach ($validated['jasas'] as $jasa) {
                        $hargaSupp = (float) $jasa['harga_supplier'];
                        $hargaNett = (float) $jasa['harga_nett'];

                        ServiceJasa::create([
                            'service_id' => $service->id,
                            'nama_jasa' => $jasa['nama_jasa'],
                            'harga_supplier' => $hargaSupp,
                            'harga_nett' => $hargaNett,
                        ]);

                        $totalSupplier += $hargaSupp;
                        $totalNett += $hargaNett;
                    }
                }

                $service->update([
                    'grand_total_supplier' => $totalSupplier,
                    'grand_total_nett' => $totalNett,
                ]);

                // Sync Stok (masuk status is 'masuk', so no stock deduction yet)
                $this->syncStokMutasi($service);
            });

            return redirect()->route('service.index')->with('sukses', 'Tanda terima service berhasil disimpan.');
        } catch (ValidationException $e) {
            throw $e;
        } catch (Throwable $e) {
            Log::error('Gagal menyimpan transaksi service', ['pesan' => $e->getMessage()]);
            return back()->withInput()->withErrors(['error' => 'Gagal menyimpan transaksi service: ' . $e->getMessage()]);
        }
    }

    public function show($id): View
    {
        $service = Service::with(['customer', 'montir', 'sales', 'supplier', 'details.barang', 'jasas'])
            ->where('nomor_dokumen', $id)
            ->orWhere('id', $id)
            ->firstOrFail();

        return view('service.show', [
            'service' => $service,
            'id' => $service->nomor_dokumen,
        ]);
    }

    public function updateStatus(Service $service): RedirectResponse
    {
        try {
            DB::transaction(function () use ($service) {
                $statusMap = [
                    'masuk' => 'dikerjakan',
                    'dikerjakan' => 'selesai',
                    'selesai' => 'diambil',
                    'diambil' => 'lunas',
                ];

                $statusLama = $service->status;
                $statusBaru = $statusMap[$statusLama] ?? $statusLama;

                $updateData = ['status' => $statusBaru];

                if ($statusBaru === 'lunas') {
                    $updateData['status_lunas'] = true;
                }

                $service->update($updateData);

                // Sinkronisasi Stok Mutasi
                $this->syncStokMutasi($service);

                // Pembayaran / Kas Flow otomatis saat lunas
                if ($statusBaru === 'lunas' && $statusLama !== 'lunas') {
                    // Penerimaan Uang dari Customer
                    KasFlow::create([
                        'tanggal' => now()->toDateString(),
                        'tipe' => 'masuk',
                        'sumber' => 'kas',
                        'kategori' => 'piutang',
                        'no_referensi' => $service->nomor_dokumen,
                        'nominal' => $service->grand_total_nett,
                        'keterangan' => "Penerimaan Service Bengkel {$service->nomor_dokumen}",
                    ]);

                    // Pengeluaran Uang ke Supplier (jasa luar / extern)
                    if ($service->repaired_by === 'extern' && $service->grand_total_supplier > 0) {
                        KasFlow::create([
                            'tanggal' => now()->toDateString(),
                            'tipe' => 'keluar',
                            'sumber' => 'kas',
                            'kategori' => 'hutang',
                            'no_referensi' => $service->nomor_dokumen,
                            'nominal' => $service->grand_total_supplier,
                            'keterangan' => "Pembayaran Outsourcing Service {$service->nomor_dokumen}",
                        ]);
                    }
                }
            });

            return back()->with('sukses', 'Status pengerjaan service berhasil diperbarui.');
        } catch (Throwable $e) {
            Log::error('Gagal memperbarui status service', ['pesan' => $e->getMessage()]);
            return back()->withErrors(['error' => 'Gagal memperbarui status service: ' . $e->getMessage()]);
        }
    }

    private function syncStokMutasi(Service $service): void
    {
        // Bersihkan mutasi stok lama yang berkaitan dengan dokumen ini
        StokMutasi::where('no_dokumen', $service->nomor_dokumen)->delete();

        // Stok dikurangi jika status sudah mulai dikerjakan ke atas
        if (in_array($service->status, ['dikerjakan', 'selesai', 'diambil', 'lunas'], true)) {
            foreach ($service->details as $detail) {
                StokMutasi::create([
                    'barang_id' => $detail->barang_id,
                    'tanggal' => $service->tanggal_masuk->toDateString(),
                    'jenis_mutasi' => 'service',
                    'no_dokumen' => $service->nomor_dokumen,
                    'masuk' => 0,
                    'keluar' => $detail->qty,
                    'hpp' => $detail->hpp,
                    'keterangan' => "Penggunaan Sparepart Service {$service->nomor_dokumen}",
                ]);
            }
        }
    }
}
