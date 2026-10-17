<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Produk;
use App\Support\FinancialAmount;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PenjualanController extends Controller
{
    public function index(Request $request): View
    {
        $query = Penjualan::query()->with('produk');

        return view('keuangan.penjualan.master-data', [
            'penjualans' => $query
                ->latest('tanggal')
                ->latest('id')
                ->get(),
        ]);
    }

    public function create(): View
    {
        return view('keuangan.penjualan.create', [
            'nomorBerikutnya' => Penjualan::query()->count() + 1,
            'produks' => Produk::query()
                ->where('stok', '>', 0)
                ->orderBy('nama')
                ->get(['id', 'nama', 'satuan', 'stok']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'produk_id' => ['required', 'integer', 'exists:produks,id'],
            'tanggal' => ['required', 'date_format:Y-m-d'],
            'satuan' => ['required', 'string', 'max:30'],
            'jumlah' => ['required', 'numeric', 'regex:/^\d{1,6}(?:\.\d{1,3})?$/', 'gt:0'],
            'harga_jual' => ['required', 'numeric', 'regex:/^\d{1,7}(?:\.\d{1,2})?$/', 'gt:0'],
        ]);

        DB::transaction(function () use ($data): void {
            $produk = Produk::query()
                ->whereKey($data['produk_id'])
                ->lockForUpdate()
                ->first();

            if (! $produk) {
                throw ValidationException::withMessages([
                    'produk_id' => 'Barang tidak ditemukan. Pilih barang yang tersedia dari daftar.',
                ]);
            }

            $jumlah = (string) $data['jumlah'];

            if (FinancialAmount::quantityInThousandths($jumlah) > FinancialAmount::quantityInThousandths((string) $produk->stok)) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Stok tidak cukup. Stok tersedia: '.$produk->stok.' '.$produk->satuan.'.',
                ]);
            }

            $hargaBeli = (string) $produk->harga_beli;
            $hargaJual = (string) $data['harga_jual'];
            $satuan = trim($data['satuan']);
            $totalHargaBeli = FinancialAmount::multiply($jumlah, $hargaBeli);
            $totalHargaJual = FinancialAmount::multiply($jumlah, $hargaJual);

            Penjualan::query()->create([
                'produk_id' => $produk->id,
                'tanggal' => $data['tanggal'],
                'satuan' => $satuan,
                'jumlah' => $jumlah,
                'harga_beli_satuan' => $hargaBeli,
                'harga_jual' => $hargaJual,
                'total_harga_beli' => $totalHargaBeli,
                'total_harga_jual' => $totalHargaJual,
                'laba' => FinancialAmount::profit($jumlah, $hargaJual, $hargaBeli),
            ]);

            $produk->decrement('stok', $jumlah);
            $produk->harga_jual = $hargaJual;
            $produk->save();
        });

        return redirect()->route('penjualan.create')->with('success', 'Penjualan dicatat dan stok barang berkurang.');
    }
}
