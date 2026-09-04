<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Service;
use App\Services\IdHasher;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\View\View;

class CetakController extends Controller
{
    public function nota(string|int $id): View
    {
        return view('print.nota', ['id' => $id]);
    }

    public function notaPdf(string|int $id): Response
    {
        $realId = IdHasher::decode($id);
        $penjualan = Penjualan::with(['customer', 'user', 'details.barang'])
            ->where('nomor_nota', $id)
            ->orWhere('id', $realId)
            ->orWhere('id', is_numeric($id) ? (int)$id : 0)
            ->firstOrFail();

        $size = request()->query('size', '80');
        $widthPt = ($size === '58') ? 164.41 : 226.77; // 58mm = 164.41pt, 80mm = 226.77pt

        // Hitung tinggi kertas secara dinamis agar pas dengan isi dan tidak menyisakan ruang putih panjang
        $itemCount = $penjualan->details->count();
        $baseHeaderMeta = 135; // Kop toko, alamat, no nota, tanggal, kasir, customer
        $itemHeight = 28;     // Tinggi per baris nama barang + qty x harga
        $baseTotalsFooter = 135; // Subtotal, diskon, grand total, status, ucapan terima kasih
        $heightPt = max(280, $baseHeaderMeta + ($itemCount * $itemHeight) + $baseTotalsFooter);

        $pdf = Pdf::loadView('print.pdf.nota', ['penjualan' => $penjualan, 'size' => $size])
            ->setPaper([0, 0, $widthPt, $heightPt], 'portrait');

        return $pdf->download("Struk_{$penjualan->nomor_nota}.pdf");
    }

    public function faktur(string|int $id): View
    {
        return view('print.faktur', ['id' => $id]);
    }

    public function fakturPdf(string|int $id): Response
    {
        $realId = IdHasher::decode($id);
        $penjualan = Penjualan::with(['customer', 'user', 'details.barang'])
            ->where('nomor_nota', $id)
            ->orWhere('id', $realId)
            ->orWhere('id', is_numeric($id) ? (int)$id : 0)
            ->firstOrFail();

        $pdf = Pdf::loadView('print.pdf.faktur', ['penjualan' => $penjualan])
            ->setPaper('a5', 'landscape');

        return $pdf->download("Faktur_{$penjualan->nomor_nota}.pdf");
    }

    public function tandaTerimaService(string|int $id): View
    {
        return view('print.tanda-terima-service', ['id' => $id]);
    }

    public function tandaTerimaServicePdf(string|int $id): Response
    {
        $realId = IdHasher::decode($id);
        $service = Service::with(['customer', 'montir', 'details.barang'])
            ->where('nomor_dokumen', $id)
            ->orWhere('id', $realId)
            ->orWhere('id', is_numeric($id) ? (int)$id : 0)
            ->firstOrFail();

        $pdf = Pdf::loadView('print.pdf.tanda-terima-service', ['service' => $service])
            ->setPaper('a5', 'landscape');

        return $pdf->download("TandaTerimaService_{$service->nomor_dokumen}.pdf");
    }

    public function suratJalan(string|int $id): View
    {
        return view('print.surat-jalan', ['id' => $id]);
    }

    public function suratJalanPdf(string|int $id): Response
    {
        $realId = IdHasher::decode($id);
        $penjualan = Penjualan::with(['customer', 'user', 'details.barang'])
            ->where('nomor_nota', $id)
            ->orWhere('id', $realId)
            ->orWhere('id', is_numeric($id) ? (int)$id : 0)
            ->firstOrFail();

        $pdf = Pdf::loadView('print.pdf.surat-jalan', ['penjualan' => $penjualan])
            ->setPaper('a5', 'landscape');

        return $pdf->download("SuratJalan_{$penjualan->nomor_nota}.pdf");
    }
}
