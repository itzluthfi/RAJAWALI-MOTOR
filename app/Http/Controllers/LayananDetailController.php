<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class LayananDetailController extends Controller
{
    private array $toko = [
        'nama' => 'Rajawali Motor',
        'tagline' => 'Bengkel & Sparepart Terpercaya di Sidoarjo',
        'kota' => 'Sidoarjo',
        'alamat' => 'Jl. Samanhudi No.102, Jasem, Bulusidokare, Kec. Sidoarjo, Kabupaten Sidoarjo, Jawa Timur 61212',
        'telepon' => '0318477400',
        'teleponTampil' => '031 847 7400',
        'whatsapp' => '628184774000',
        'email' => 'halo@rajawalimotor.id',
        'mapsUrl' => 'https://maps.google.com/?q=Jl.+Samanhudi+No.102,+Jasem,+Bulusidokare,+Sidoarjo',
        'mapsEmbed' => 'https://www.google.com/maps?q=Jl.+Samanhudi+No.102,+Jasem,+Bulusidokare,+Sidoarjo&output=embed',
        'jamBuka' => [
            ['hari' => 'Minggu', 'jam' => '07:30 - 17:00'],
            ['hari' => 'Senin', 'jam' => '07:30 - 17:00'],
            ['hari' => 'Selasa', 'jam' => '07:30 - 17:00'],
            ['hari' => 'Rabu', 'jam' => '07:30 - 17:00'],
            ['hari' => 'Kamis', 'jam' => '07:30 - 17:00'],
            ['hari' => 'Jumat', 'jam' => '07:30 - 17:00'],
            ['hari' => 'Sabtu', 'jam' => '07:30 - 17:00'],
        ],
    ];

    public function gantiOli(): View
    {
        return view('layanan.ganti-oli', ['toko' => $this->toko]);
    }

    public function tuneUp(): View
    {
        return view('layanan.tune-up', ['toko' => $this->toko]);
    }

    public function banSpooring(): View
    {
        return view('layanan.ban-spooring', ['toko' => $this->toko]);
    }

    public function kelistrikan(): View
    {
        return view('layanan.kelistrikan', ['toko' => $this->toko]);
    }

    public function injeksi(): View
    {
        return view('layanan.injeksi', ['toko' => $this->toko]);
    }

    public function acMobil(): View
    {
        return view('layanan.ac-mobil', ['toko' => $this->toko]);
    }

    public function bodyRepair(): View
    {
        return view('layanan.body-repair', ['toko' => $this->toko]);
    }
}
