# AFTERGLOW Festival

Aplikasi tiket festival tiga hari berbasis Laravel 13 dengan antarmuka Bahasa Indonesia, Blade, Tailwind CSS, dan Alpine.js. Aplikasi mendukung pemesanan tiket, pembayaran manual melalui GoPay, verifikasi admin, tiket QR bertanda tangan, unduhan PDF, laporan penjualan, check-in, dan layanan pelanggan.

## Persyaratan

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan npm
- SQLite atau MySQL

## Instalasi lokal

1. Jalankan `composer install`.
2. Salin `.env.example` menjadi `.env`, lalu jalankan `php artisan key:generate`.
3. Atur koneksi database pada `.env`. Untuk SQLite, buat `database/database.sqlite`.
4. Isi konfigurasi kontak dan pembayaran pada `.env` (contoh tersedia di bawah).
5. Jalankan `npm install`.
6. Jalankan `php artisan migrate --seed` untuk menerapkan migrasi dan mengisi data awal. Pada database yang sudah berisi data, gunakan `php artisan migrate --seed`; jangan gunakan `migrate:fresh`.
7. Jalankan `php artisan storage:link`.
8. Jalankan `npm run build`.
9. Mulai aplikasi dengan `php artisan serve`.

## Konfigurasi festival

```dotenv
APP_LOCALE=id
APP_FALLBACK_LOCALE=id
APP_FAKER_LOCALE=id_ID
APP_TIMEZONE=Asia/Jakarta
FESTIVAL_GOPAY_NUMBER=081313490079
FESTIVAL_GOPAY_NAME="afterglow - Festival"
FESTIVAL_WHATSAPP=6282128943420
FESTIVAL_SUPPORT_EMAIL=afterglow@support.com
```

Festival berlangsung di Kebun Raya Bogor, Bogor, Jawa Barat pada 29–31 Desember 2026. Gerbang dibuka pukul 14.00 WIB dan acara untuk semua usia. Harga resmi sembilan tiket disimpan di database dan dapat dikelola admin. Seeder tidak menimpa harga atau kuota yang sudah ada. Migrasi konfigurasi resmi memperbarui tanggal, deskripsi, manfaat, dan harga tanpa mengubah stok terjual, pesanan, atau total transaksi historis.

Pembayaran dilakukan manual melalui GoPay. Sistem tidak terhubung ke API pembayaran; pengunjung mengunggah bukti transfer dan admin memverifikasinya.

## Akun demo

- Admin: `admin@festival.com` / `FestivalAdmin!2026`
- Pengunjung: `test@example.com` / `password`

Ganti kata sandi demo sebelum digunakan di lingkungan publik.

## Halaman utama

- `/` — informasi acara, jadwal, harga tiket, FAQ, serta kontak langsung WhatsApp dan email
- `/dashboard` — pesanan, petunjuk pembayaran, unggah bukti, tiket QR, dan PDF
- `/admin` — ringkasan, verifikasi pembayaran, harga tiket, kuota, dan laporan
- `/admin/check-in` — validasi QR dan check-in

Pertanyaan pelanggan dilayani melalui tombol WhatsApp dan email pada situs. Rute admin memerlukan akun dengan peran admin. Bukti pembayaran disimpan pada disk privat. Pengunjung hanya dapat mengakses pesanan dan tiket miliknya. Pesanan yang belum dibayar menahan kuota selama 24 jam; scheduler melepaskan reservasi yang kedaluwarsa setiap sepuluh menit. Untuk pengembangan lokal jalankan `php artisan schedule:work`; di server, jadwalkan `php artisan schedule:run` setiap menit.

Sebelum produksi, matikan debug, aktifkan HTTPS, atur layanan email, dan pastikan direktori `storage` serta `bootstrap/cache` dapat ditulis.
