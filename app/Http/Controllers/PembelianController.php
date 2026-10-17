<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
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
        $query = Pembelian::query()->with('produk');

        return view('keuangan.pembelian.master-data', [
            'pembelians' => $query
                ->latest('tanggal')
                ->latest('id')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('keuangan.pembelian.create', [
            'nomorBerikutnya' => Pembelian::query()->count() + 1,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama_barang' => ['required', 'string', 'max:150'],
            'tanggal' => ['required', 'date_format:Y-m-d'],
            'satuan' => ['required', 'string', 'max:30'],
            'jumlah' => ['required', 'numeric', 'regex:/^\d{1,6}(?:\.\d{1,3})?$/', 'gt:0'],
            'harga_beli' => ['required', 'numeric', 'regex:/^\d{1,7}(?:\.\d{1,2})?$/', 'gt:0'],
        ]);

        DB::transaction(function () use ($data): void {
            $namaBarang = trim($data['nama_barang']);
            $satuan = trim($data['satuan']);
            $produk = Produk::query()
                ->whereRaw('LOWER(nama) = ?', [mb_strtolower($namaBarang)])
                ->lockForUpdate()
                ->first();

            if (! $produk) {
                $produk = new Produk([
                    'nama' => $namaBarang,
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

        return redirect()->route('pembelian.create')->with('success', 'Pembelian dicatat dan stok barang bertambah.');
    }
}
