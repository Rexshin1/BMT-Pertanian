# BMT Cinde Barokah — Aplikasi Penjualan

Aplikasi pencatatan pembelian, penjualan, stok, dan laporan laba rugi berbasis Laravel 10. Antarmuka menggunakan template Modernize dengan identitas BMT Cinde Barokah.

## Fitur

- Dashboard dengan ringkasan transaksi dan grafik Penjualan (hijau) serta Pembelian (merah).
- Pencatatan Pembelian yang otomatis mendaftarkan barang baru dan menambah stok.
- Form Penjualan dengan pilihan barang yang masih memiliki stok.
- Validasi stok, pencatatan unit per transaksi, dan perhitungan total otomatis.
- Tabel transaksi dengan filter tanggal, pencarian, pagination, dan ekspor CSV, Excel, atau PDF.
- Laporan laba rugi berdasarkan harga beli dan harga jual.

## Teknologi

- PHP 8.1.10
- Laravel 10
- MySQL 5.7+ atau MariaDB yang kompatibel
- Composer

## Persiapan

Pastikan PHP 8.1.10 dan MySQL aktif. Buat database aplikasi, misalnya melalui HeidiSQL atau phpMyAdmin:

```sql
CREATE DATABASE aplikasi_penjualan
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

Tes menggunakan database terpisah bernama `aplikasi_penjualan_testing` agar data aplikasi tidak terhapus. Buat juga database tersebut sebelum menjalankan tes:

```sql
CREATE DATABASE aplikasi_penjualan_testing
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
```

## Instalasi lokal

Jalankan perintah berikut dari folder proyek:

```powershell
Copy-Item .env.example .env
composer install
```

Atur `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, dan `DB_PASSWORD` pada `.env`, lalu siapkan Laravel dan skema database:

```powershell
php artisan key:generate
php artisan migrate
```

Pilih PHP 8.1.10 di Laragon dan pastikan versinya:

```powershell
php -v
```

Untuk menjalankan aplikasi di Laragon, buka `start-laravel.bat`. Secara bawaan file tersebut menggunakan PHP Laragon di `D:\laragon\bin\php\php-8.1.10-Win32-vs16-x64\php.exe` dan menjalankan server pada port `8108`. Jika lokasi PHP Laragon berbeda, sesuaikan nilai `PHP_EXE` di file tersebut.

Alternatifnya, jalankan server Laravel secara langsung:

```powershell
php artisan serve --host=127.0.0.1 --port=8108
```

Buka aplikasi di <http://127.0.0.1:8108>.

## Alur penggunaan

1. Buka **Create Pembelian** untuk mendaftarkan barang. Isi nama barang, tanggal, unit, harga beli per unit, dan jumlah. Barang baru otomatis ditambahkan dan stok bertambah.
2. Buka **Create Penjualan**. Pilih barang dari dropdown barang yang stoknya tersedia, lalu isi tanggal, unit, harga jual per unit, dan jumlah. Unit terisi dari data barang tetapi tetap bisa diubah; harga jual dimasukkan manual.
3. Setelah transaksi disimpan, aplikasi kembali ke form yang sama agar input berikutnya bisa langsung dilakukan. Stok diperbarui otomatis.
4. Buka menu **Penjualan**, **Pembelian**, atau **Laba Rugi** untuk melihat dan memfilter catatan transaksi.
5. Dashboard merangkum transaksi dan stok. Grafik membedakan Penjualan dengan warna hijau dan Pembelian dengan warna merah.

## Pengujian

Atur kredensial MySQL tes pada bagian `<php>` di `phpunit.xml` jika konfigurasi lokal berbeda. Pastikan tes diarahkan ke database `aplikasi_penjualan_testing`, bukan database utama.

```powershell
php artisan test
```

## Catatan keamanan

Laravel 10 sudah melewati masa dukungan keamanan. Tinjau risiko sebelum menggunakan aplikasi di lingkungan produksi dan rencanakan peningkatan ke versi Laravel yang masih didukung.
