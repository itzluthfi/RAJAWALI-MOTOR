<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('home', [
            'toko' => [
                'nama' => 'Rajawali Motor',
                'tagline' => 'Bengkel & Sparepart Terpercaya di Surabaya',
                'kota' => 'Surabaya',
                'alamat' => 'Jl. Siwalankerto Timur No. 231 A, Surabaya, Jawa Timur, Indonesia',
                'telepon' => '0318477400',
                'teleponTampil' => '031 847 7400',
                'whatsapp' => '628184774000',
                'email' => 'halo@rajawalimotor.id',
                'mapsUrl' => 'https://maps.google.com/?q=Jl.+Siwalankerto+Timur+No.+231+A,+Surabaya',
                'mapsEmbed' => 'https://www.google.com/maps?q=Jl.+Siwalankerto+Timur+No.+231+A,+Surabaya&output=embed',
                'jamBuka' => [
                    ['hari' => 'Minggu', 'jam' => '07:30 - 17:00'],
                    ['hari' => 'Senin', 'jam' => '07:30 - 17:00'],
                    ['hari' => 'Selasa', 'jam' => '07:30 - 17:00'],
                    ['hari' => 'Rabu', 'jam' => '07:30 - 17:00'],
                    ['hari' => 'Kamis', 'jam' => '07:30 - 17:00'],
                    ['hari' => 'Jumat', 'jam' => '07:30 - 17:00'],
                    ['hari' => 'Sabtu', 'jam' => '07:30 - 17:00'],
                ],
            ],
        ]);
    }
}
