# 🦅 Rajawali Motor Surabaya — Sistem Informasi & POS ERP Bengkel Terintegrasi

![Rajawali Motor](https://lh3.googleusercontent.com/aida-public/AB6AXuAsYEm9KYYbuD248b0jN_sheEfynwQ6j7teJdvKA8edK8NYF0ndmkXVXlqw9SKIhago4iUYt5RmUV5kgkIuq0AjjoDKToRqxiuEM17EOurrulLi0qsUlk36AxIH4JObdUrym7rxUnRAwC9aLkxP4pUlSgGe9qLiTLXOV0I1-pYXxewRVi_zU2DtKVLzY0W20Ve5lzZD-FdFadE3YvJ_ozDGIJmgDt6aLfSKhBNi1YFqbLL-76iue9ykhTo7OsirOQuyfFH_HfkN0Dc)

Sistem Aplikasi Web Terintegrasi Pengganti Aplikasi Desktop untuk Toko Sparepart & Bengkel Motor **Rajawali Motor Surabaya**. Dibuat menggunakan stack modern **Laravel 13, Livewire, Alpine.js, Tailwind CSS, Vite, & MySQL 8**.

---

## 📌 Daftar Isi
- [Teknologi Utama](#-teknologi-utama)
- [Fitur Unggulan](#-fitur-unggulan)
- [Daftar Akun Demo & Hak Akses Peran (Roles)](#-daftar-akun-demo--hak-akses-peran-roles)
- [Matriks Hak Akses Peran](#-matriks-hak-akses-peran)
- [Penjelasan Modul & Menu Sistem](#-penjelasan-modul--menu-sistem)
- [Panduan Instalasi Lokal (Development)](#-panduan-instalasi-lokal-development)
- [Panduan Deploy Ke Server Produksi (Production Deployment)](#-panduan-deploy-ke-server-produksi-production-deployment)
- [Pemeliharaan & Troubleshooting](#-pemeliharaan--troubleshooting)

---

## 🛠️ Teknologi Utama

- **Framework Backend**: Laravel 13 (PHP 8.3+)
- **Frontend Reactive**: Livewire & Alpine.js
- **Styling & Design Token**: Tailwind CSS & Material Symbols / Lucide Icons
- **Asset Bundler**: Vite
- **Database**: MySQL 8.0+ / MariaDB 10.4+
- **SweetAlert2 & Toast**: Dialog modal konfirmasi & notifikasi interaktif

---

## ⭐ Fitur Unggulan

1. **Website Landing Page Pelanggan (Frontend Public)**:
   - Tampilan presisi 100% dari desain Stitch.
   - Status Toko Buka/Tutup otomatis mengikuti Waktu Indonesia Barat (WIB).
   - Profil Layanan Lengkap (Ganti Oli, Tune Up, Ban & Spooring, Kelistrikan, AC Mobil, Injeksi, & Body Repair).
   - Widget Kalkulator Estimasi Biaya Servis Interaktif & Booking 1-Click via WhatsApp.
   - Tombol melayang Floating WhatsApp FAB & tombol navbar WhatsApp Official Green (`#25D366`).
   - OpenGraph & Twitter Card Meta Tags untuk preview media sosial.

2. **Sistem Admin & POS Bengkel (Backend ERP)**:
   - Halaman Login Split-Screen White & Red Rajawali Theme (`#B0181C`) dengan tombol 1-Click Auto Fill Demo.
   - Layout Responsif Mobile Drawer & Topbar Hamburger Toggle.
   - Tampilan **Mobile Card List View** khusus layar HP (< 768px).
   - Pencarian Cepat Modul dengan Shortcut `Ctrl + K`.
   - Tooltip Interaktif (`[data-tooltip]`) di seluruh tombol aksi tabel.
   - Format Cetak Nota Kasir Thermal (58mm/80mm), Faktur A4, dan Tanda Terima Service lengkap dengan Logo Stitch.

---

## 🔑 Daftar Akun Demo & Hak Akses Peran (Roles)

Sistem ini mendukung **5 Peran Pengguna (Multi-Role Authorization)**. Seluruh akun demo berikut siap digunakan dengan kata sandi bawaan: **`password`**

| Peran (Role) | Username | Password | Deskripsi Tugas & Wewenang |
| :--- | :--- | :--- | :--- |
| **Owner** | `owner` | `password` | Pemilik toko, akses 100% penuh ke seluruh modul, keuangan, dan pengaturan toko. |
| **Admin** | `admin` | `password` | Administrator operasional, mengelola barang, transaksi, customer, supplier, dan stok. |
| **Kasir** | `kasir1` | `password` | Petugas kasir, melayani transaksi POS penjualan langsung, cetak nota, & customer. |
| **Gudang** | `gudang1` | `password` | Petugas gudang, mengelola pembelian supplier, penerimaan barang, dan opname stok. |
| **Montir** | `montir1` | `password` | Teknisi bengkel, melihat dan mengupdate status pengerjaan Work Order Service. |

---

## 📊 Matriks Hak Akses Peran

| Modul / Fitur | Owner | Admin | Kasir | Gudang | Montir |
| :--- | :---: | :---: | :---: | :---: | :---: |
| **Dashboard Utama** | ✅ | ✅ | ✅ | ✅ | ✅ |
| **Kasir POS Express** | ✅ | ✅ | ✅ | ❌ | ❌ |
| **Nota Penjualan & Cetak** | ✅ | ✅ | ✅ | ❌ | ❌ |
| **Pembelian (Supplier)** | ✅ | ✅ | ❌ | ✅ | ❌ |
| **Retur Barang** | ✅ | ✅ | ✅ | ✅ | ❌ |
| **Service & Work Order** | ✅ | ✅ | ✅ | ❌ | ✅ |
| **Master Barang & Barcode** | ✅ | ✅ | ❌ | ✅ | ❌ |
| **Master Customer** | ✅ | ✅ | ✅ | ❌ | ❌ |
| **Master Supplier** | ✅ | ✅ | ❌ | ✅ | ❌ |
| **Master Sales** | ✅ | ✅ | ❌ | ❌ | ❌ |
| **Stok (Kartu, Rekap, Opname)** | ✅ | ✅ | ❌ | ✅ | ❌ |
| **Keuangan (Piutang, Kas, Bank)** | ✅ | ✅ | ❌ | ❌ | ❌ |
| **Laporan Laba Rugi Realtime** | ✅ | ✅ | ❌ | ❌ | ❌ |
| **Pengaturan Toko & User** | ✅ | ❌ | ❌ | ❌ | ❌ |
| **Audit Log Activity** | ✅ | ❌ | ❌ | ❌ | ❌ |

---

## 📄 Penjelasan Modul & Menu Sistem

### 1. Modul Transaksi POS & Penjualan (`/admin/kasir` & `/admin/penjualan`)
- **Kasir POS**: Scan barcode barang / cari nama sparepart, pilih jenis transaksi (Tunai / Tempo), hitung kembalian otomatis, cetak nota thermal 58mm/80mm.
- **Nota Penjualan**: Daftar seluruh riwayat transaksi penjualan, cetak ulang nota, dan fitur pembatalan nota dengan input alasan.

### 2. Modul Work Order Service Bengkel (`/admin/service`)
- Terima kendaraan baru, input keluhan pelanggan & mekanik penanggung jawab, update status pengerjaan (*Masuk -> Dikerjakan -> Selesai -> Lunas*), dan cetak lembar Tanda Terima Service.

### 3. Modul Master Data (`/admin/barang`, `/admin/customer`, `/admin/supplier`, `/admin/sales`)
- **Master Barang**: Kelola kode barang, nama sparepart, group/sub-group, satuan, HPP, harga eceran, harga grosir, lokasi rak, stok minimum, dan multi-barcode.
- **Master Customer**: Kelola data pelanggan, nomor telepon, alamat, dan batas termin piutang (hari).
- **Master Supplier**: Kelola data pemasok barang dan kontak person.
- **Master Sales**: Kelola data tim sales beserta persentase komisi.

### 4. Modul Stok & Pergudangan (`/admin/stok/*`)
- **Kartu Stok**: Laporan riwayat mutasi masuk/keluar setiap barang.
- **Rekap Stok**: Ringkasan total kuantitas & nilai nominal stok tersimpan.
- **Stok Opname**: Penyesuaian fisik barang gudang dengan sistem.
- **Stok Menipis**: Peringatan otomatis untuk barang yang sudah mencapai batas minimum.

### 5. Modul Keuangan & Laporan (`/admin/keuangan/*` & `/admin/laporan`)
- **Piutang Customer & Hutang Supplier**: Catatan tagihan tempo belum lunas.
- **Kas & Bank**: Catatan arus kas masuk/keluar operasional bengkel.
- **Laporan Laba Rugi**: Perhitungan omzet kotor, HPP, biaya operasional, dan laba bersih secara realtime.

---

## 💻 Panduan Instalasi Lokal (Development)

### Persyaratan Sistem (Prerequisites)
- PHP 8.3 atau lebih baru (Extension: OpenSSL, PDO, Mbstring, Tokenizer, XML, Ctype, JSON, BCMath)
- Composer 2.x
- Node.js v20+ & npm
- Laragon / XAMPP (MySQL 8.0 / MariaDB 10.4)

### Langkah-Langkah Instalasi:

1. **Clone / Buka Direktori Proyek**:
   ```bash
   cd c:\laragon\www\RAJAWALI-MOTOR
   ```

2. **Install Dependensi Composer & NPM**:
   ```bash
   composer install
   npm install
   ```

3. **Konfigurasi Environment `.env`**:
   Salin file `.env.example` menjadi `.env` dan sesuaikan kredensial database Laragon Anda:
   ```ini
   APP_NAME="Rajawali Motor"
   APP_ENV=local
   APP_KEY=base64:...
   APP_DEBUG=true
   APP_URL=http://localhost:8000

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=rajawali_motor
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Buat Database, Jalankan Migrasi & Seeder**:
   ```bash
   mysql -u root -e "CREATE DATABASE IF NOT EXISTS rajawali_motor CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
   php artisan migrate:fresh --seed
   ```

5. **Jalankan Development Server**:
   Jalankan dua perintah berikut di dua terminal terpisah:
   - Terminal 1 (Laravel Artisan):
     ```bash
     php artisan serve
     ```
   - Terminal 2 (Vite Compiler):
     ```bash
     npm run dev
     ```

6. **Akses Aplikasi**:
   - Website Utama: `http://127.0.0.1:8000/`
   - Portal Login Admin: `http://127.0.0.1:8000/admin/login`

---

## 🚀 Panduan Deploy Ke Server Produksi (Production Deployment)

Rekomendasi Nama Domain Utama: **`rajawalimotor.com`**

### 1. Persiapan Server Produksi (Ubuntu 22.04 LTS / VPS)
Pastikan server VPS / Hosting Anda sudah terinstall Nginx / Apache, PHP 8.3, MySQL 8.0, Certbot (SSL), Composer, & Node.js.

### 2. Upload Code & Setup Environment Produksi
Clone atau upload proyek ke server (misal ke `/var/www/rajawalimotor.com`):
```bash
cd /var/www/rajawalimotor.com
composer install --no-dev --optimize-autoloader
npm install
npm run build
```

Edit file `.env` produksi:
```ini
APP_NAME="Rajawali Motor"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://rajawalimotor.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=rajawali_motor_prod
DB_USERNAME=rajawali_user
DB_PASSWORD=PasswordKuat123!
```

### 3. Eksekusi Migrasi & Optimasi Cache Laravel
```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
```

Set hak akses direktori storage & bootstrap cache:
```bash
chown -R www-data:www-data /var/www/rajawalimotor.com
chmod -R 775 /var/www/rajawalimotor.com/storage
chmod -R 775 /var/www/rajawalimotor.com/bootstrap/cache
```

### 4. Konfigurasi Nginx Server Block (`/etc/nginx/sites-available/rajawalimotor.com`)
```nginx
server {
    listen 80;
    server_name rajawalimotor.com www.rajawalimotor.com;
    root /var/www/rajawalimotor.com/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 5. Pasang Sertifikat SSL Gratis (Certbot Let's Encrypt)
```bash
sudo certbot --nginx -d rajawalimotor.com -d www.rajawalimotor.com
```

---

## ⚙️ Pemeliharaan & Troubleshooting

### Reset Data & Re-Seed Pengguna Demo
Jika ingin mengembalikan data ke posisi awal demo:
```bash
php artisan migrate:fresh --seed
```

### Clear Cache (Jika Ada Perubahan View / CSS)
```bash
php artisan view:clear
php artisan config:clear
php artisan cache:clear
npm run build
```

---

© {{ date('Y') }} **Rajawali Motor Surabaya**. All rights reserved.
