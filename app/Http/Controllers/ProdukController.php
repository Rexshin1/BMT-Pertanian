<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Pemilik;
use App\Models\Penjualan;
use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProdukController extends Controller
{
    public function index(): View
    {
        return view('keuangan.produk.index', [
            'produks' => Produk::query()->with('pemilik')->orderBy('nama')->get(),
        ]);
    }

    public function create(): View
    {
        return view('keuangan.produk.form', [
            'produk' => null,
            'pemiliks' => Pemilik::query()->orderByRaw('CAST(kode AS DECIMAL(20, 0))')->get(),
        ]);
    }

    public function edit(Produk $produk): View
    {
        return view('keuangan.produk.form', [
            'produk' => $produk->load('pemilik'),
            'pemiliks' => Pemilik::query()->orderByRaw('CAST(kode AS DECIMAL(20, 0))')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'pemilik_kode' => ['required', 'string', 'regex:/^\d{3,10}$/', 'exists:pemiliks,kode'],
            'satuan' => ['required', 'string', 'max:30'],
            'nama' => ['required', 'string', 'max:150'],
            'harga_jual' => ['nullable', 'regex:/^\d{1,7}(?:\.\d{1,2})?$/'],
        ]);

        $pemilik = Pemilik::query()->where('kode', $data['pemilik_kode'])->firstOrFail();
        $request->validate([
            'nama' => [Rule::unique('produks', 'nama')->where(fn ($query) => $query->where('pemilik_id', $pemilik->id))],
        ]);

        Produk::query()->create([
            'pemilik_id' => $pemilik->id,
            'nama' => $data['nama'],
            'satuan' => $data['satuan'],
            'harga_jual' => $data['harga_jual'] ?? '0',
        ]);

        return redirect()->route('produk.index')
            ->with('success', 'Barang berhasil ditambahkan. Catat pembelian untuk menambah stok.');
    }

    public function update(Request $request, Produk $produk): RedirectResponse
    {
        $data = $request->validate([
            'pemilik_kode' => ['required', 'string', 'regex:/^\d{3,10}$/', 'exists:pemiliks,kode'],
            'satuan' => ['required', 'string', 'max:30'],
            'nama' => ['required', 'string', 'max:150'],
            'harga_jual' => ['nullable', 'regex:/^\d{1,7}(?:\.\d{1,2})?$/'],
        ]);

        $pemilik = Pemilik::query()->where('kode', $data['pemilik_kode'])->firstOrFail();
        $request->validate([
            'nama' => [
                Rule::unique('produks', 'nama')
                    ->where(fn ($query) => $query->where('pemilik_id', $pemilik->id))
                    ->ignore($produk->id),
            ],
        ]);

        $produk->update([
            'pemilik_id' => $pemilik->id,
            'nama' => trim($data['nama']),
            'satuan' => trim($data['satuan']),
            'harga_jual' => $data['harga_jual'] ?? '0',
        ]);

        return redirect()->route('produk.index')->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroy(Produk $produk): RedirectResponse
    {
        DB::transaction(function () use ($produk): void {
            Penjualan::query()->where('produk_id', $produk->id)->delete();
            Pembelian::query()->where('produk_id', $produk->id)->delete();
            $produk->delete();
        });

        return redirect()->route('produk.index')
            ->with('success', 'Barang dan seluruh transaksi terkait berhasil dihapus.');
    }
}
