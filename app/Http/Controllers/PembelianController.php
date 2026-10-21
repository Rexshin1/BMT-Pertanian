<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Pemilik;
use App\Models\Produk;
use App\Support\FinancialAmount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PembelianController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'tanggal_mulai' => ['nullable', 'date_format:Y-m-d'],
            'tanggal_selesai' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulai'],
            'pemilik' => ['nullable', 'string', 'exists:pemiliks,kode'],
        ]);
        $query = Pembelian::query()->with('produk.pemilik');

        if (! empty($filters['pemilik'])) {
            $query->whereHas('produk.pemilik', fn ($ownerQuery) => $ownerQuery->where('kode', $filters['pemilik']));
        }

        return view('keuangan.pembelian.master-data', [
            'pembelians' => $query
                ->latest('tanggal')
                ->latest('id')
                ->get(),
            'pemiliks' => Pemilik::query()->orderByRaw('CAST(kode AS DECIMAL(20, 0))')->get(),
            'pemilikTerpilih' => $filters['pemilik'] ?? '',
            'tanggalMulai' => $filters['tanggal_mulai'] ?? '',
            'tanggalSelesai' => $filters['tanggal_selesai'] ?? '',
            'pemilikTerpilihNama' => ! empty($filters['pemilik'])
                ? Pemilik::query()->where('kode', $filters['pemilik'])->value('nama')
                : null,
        ]);
    }

    public function create(): View
    {
        $pemiliks = Pemilik::query()->orderByRaw('CAST(kode AS DECIMAL(20, 0))')->get();
        $lastKode = session('last_pemilik_kode');

        return view('keuangan.pembelian.create', [
            'nomorBerikutnya' => Pembelian::query()->count() + 1,
            'pemiliks' => $pemiliks,
            'lastPemilikKode' => $pemiliks->contains('kode', $lastKode) ? $lastKode : null,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'pemilik_kode' => ['required', 'string', 'regex:/^\d{3,10}$/', 'exists:pemiliks,kode'],
            'nama_barang' => ['required', 'string', 'max:150'],
            'tanggal' => ['required', 'date_format:Y-m-d'],
            'satuan' => ['required', 'string', 'max:30'],
            'jumlah' => ['required', 'numeric', 'regex:/^\d{1,6}(?:\.\d{1,3})?$/', 'gt:0'],
            'harga_beli' => ['required', 'numeric', 'regex:/^\d{1,7}(?:\.\d{1,2})?$/', 'gt:0'],
        ]);

        DB::transaction(function () use ($data): void {
            $namaBarang = trim($data['nama_barang']);
            $satuan = trim($data['satuan']);
            $pemilik = Pemilik::query()
                ->where('kode', $data['pemilik_kode'])
                ->lockForUpdate()
                ->firstOrFail();
            $produk = Produk::query()
                ->where('pemilik_id', $pemilik->id)
                ->whereRaw('LOWER(nama) = ?', [mb_strtolower($namaBarang)])
                ->lockForUpdate()
                ->first();

            if (! $produk) {
                $produk = new Produk([
                    'nama' => $namaBarang,
                    'pemilik_id' => $pemilik->id,
                    'satuan' => $satuan,
                ]);
                $produk->save();
            }

            $jumlah = (string) $data['jumlah'];
            $hargaBeli = (string) $data['harga_beli'];

            Pembelian::query()->create([
                'produk_id' => $produk->id,
                'tanggal' => $data['tanggal'],
                'satuan' => $satuan,
                'jumlah' => $jumlah,
                'harga_beli' => $hargaBeli,
                'total_harga' => FinancialAmount::multiply($jumlah, $hargaBeli),
            ]);

            $produk->increment('stok', $jumlah);
            $produk->harga_beli = $hargaBeli;
            $produk->save();
        });

        session(['last_pemilik_kode' => $data['pemilik_kode']]);

        return redirect()->route('pembelian.create')->with('success', 'Pembelian dicatat dan stok barang bertambah.');
    }
}
