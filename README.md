# BMT Cinde Barokah — Aplikasi Penjualan

Aplikasi pencatatan pembelian, penjualan, stok, dan laporan laba rugi berbasis Laravel 10. Antarmuka menggunakan template Modernize dengan identitas BMT Cinde Barokah.

## Fitur

- Dashboard dengan ringkasan transaksi dan grafik Penjualan (hijau) serta Pembelian (merah).
- Pencatatan Pembelian yang otomatis mendaftarkan barang baru dan menambah stok.
- Master nama dengan ID otomatis dan persediaan yang dipisahkan per pemilik.
- Form Penjualan dengan pilihan barang yang masih memiliki stok.
- Validasi stok, pencatatan unit per transaksi, dan perhitungan total otomatis.
- Tabel transaksi dengan filter tanggal, pencarian, pagination, dan ekspor CSV, Excel, atau PDF.
- Laporan laba rugi berdasarkan harga beli dan harga jual.
- Login, pengelolaan akun pribadi, dan ekspor laporan dengan nomor urut sesuai hasil filter.

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

Alternatifnya, jalankan server Laravel secara langsung. Gunakan `0.0.0.0` agar HP yang berada di Wi-Fi yang sama dapat mengakses aplikasi:

```powershell
php artisan serve --host=0.0.0.0 --port=8108
```

Buka aplikasi di <http://127.0.0.1:8108> dari laptop. Dari HP, buka `http://IP-LAPTOP:8108` (ganti `IP-LAPTOP` dengan alamat IPv4 laptop). Izinkan port 8108 hanya pada profil jaringan Private di Windows Firewall jika diminta; jangan matikan firewall.

Untuk penggunaan harian yang lebih responsif, jalankan aplikasi melalui Apache Laragon. `php artisan serve` adalah server pengembangan PHP dan tidak dirancang untuk melayani banyak permintaan secara bersamaan.

Pendaftaran publik dinonaktifkan. Buat akun administrator pertama dari terminal server dengan `php artisan app:create-admin`, lalu masuk melalui `/login`. Detail akun dan password dapat diperbarui dari menu **Edit Account**.

## Alur penggunaan

1. Buka **Master Data → Master ID Nama** dan daftarkan setiap pemilik satu kali. Aplikasi memberi ID otomatis, seperti `001`.
2. Buka **Master Data → Master ID Nama** untuk mengelola nama pemilik. Tambah, edit, atau hapus master barang melalui tombol **Kelola Barang** di form Pembelian atau Penjualan; daftar barang tidak ditampilkan di sidebar dan tidak memuat kolom stok.
3. Buka **Create Pembelian**, masukkan ID pemilik, lalu isi nama barang, tanggal, unit, harga beli per unit, dan jumlah. Barang bernama sama untuk pemilik berbeda dicatat sebagai persediaan terpisah.
4. Buka **Create Penjualan**, masukkan ID pemilik lalu pilih barang dari stok milik orang tersebut. Isi tanggal, unit, harga jual per unit, dan jumlah. Unit terisi dari data barang tetapi tetap bisa diubah; harga jual dimasukkan manual.
5. Setelah transaksi disimpan, aplikasi kembali ke form yang sama agar input berikutnya bisa langsung dilakukan. Stok pemilik terkait diperbarui otomatis.
6. Di tabel **Penjualan** atau **Pembelian**, pilih ID/nama untuk melihat serta mengekspor data pemilik tersebut saja. Kosongkan filter untuk melihat semua transaksi.
7. Dashboard merangkum transaksi dan stok. Grafik membedakan Penjualan dengan warna hijau dan Pembelian dengan warna merah.

Data barang dan transaksi lama yang sebelumnya masuk ke ID `000` kini dimiliki oleh **Bambang** dengan ID `001`. Perubahan ini mempertahankan semua barang dan transaksi; ID pemilik baru dimulai dari `002`.

Menghapus barang akan menghapus permanen transaksi pembelian dan penjualan barang tersebut. Menghapus pemilik juga menghapus seluruh barang serta transaksi yang terkait; aplikasi meminta konfirmasi sebelum penghapusan.

## Pengujian

Atur kredensial MySQL tes pada bagian `<php>` di `phpunit.xml` jika konfigurasi lokal berbeda. Pastikan tes diarahkan ke database `aplikasi_penjualan_testing`, bukan database utama.

```powershell
php artisan test
```

## Catatan keamanan

Laravel 10 sudah melewati masa dukungan keamanan. Tinjau risiko sebelum menggunakan aplikasi di lingkungan produksi dan rencanakan peningkatan ke versi Laravel yang masih didukung.
