<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Service;
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
        $realId = \App\Services\IdHasher::decode($id);
        $penjualan = Penjualan::with(['customer', 'user', 'details.barang'])
            ->where('nomor_nota', $id)
            ->orWhere('id', $realId)
            ->firstOrFail();

        $pdf = Pdf::loadView('print.pdf.nota', ['penjualan' => $penjualan])
            ->setPaper([0, 0, 226.77, 600], 'portrait');

        return $pdf->download("Struk_{$penjualan->nomor_nota}.pdf");
    }

    public function faktur(string|int $id): View
    {
        return view('print.faktur', ['id' => $id]);
    }

    public function fakturPdf(string|int $id): Response
    {
        $realId = \App\Services\IdHasher::decode($id);
        $penjualan = Penjualan::with(['customer', 'user', 'details.barang'])
            ->where('nomor_nota', $id)
            ->orWhere('id', $realId)
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
        $realId = \App\Services\IdHasher::decode($id);
        $service = Service::with(['customer', 'montirUser', 'details.barang'])
            ->where('nomor_service', $id)
            ->orWhere('id', $realId)
            ->firstOrFail();

        $pdf = Pdf::loadView('print.pdf.tanda-terima-service', ['service' => $service])
            ->setPaper('a5', 'landscape');

        return $pdf->download("TandaTerimaService_{$service->nomor_service}.pdf");
    }

    public function suratJalan(string|int $id): View
    {
        return view('print.surat-jalan', ['id' => $id]);
    }

    public function suratJalanPdf(string|int $id): Response
    {
        $realId = \App\Services\IdHasher::decode($id);
        $penjualan = Penjualan::with(['customer', 'user', 'details.barang'])
            ->where('nomor_nota', $id)
            ->orWhere('id', $realId)
            ->firstOrFail();

        $pdf = Pdf::loadView('print.pdf.faktur', ['penjualan' => $penjualan])
            ->setPaper('a5', 'landscape');

        return $pdf->download("SuratJalan_{$penjualan->nomor_nota}.pdf");
    }
}
