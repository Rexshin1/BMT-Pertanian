<?php

namespace App\Http\Controllers;

use App\Models\Produk;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProdukController extends Controller
{
    public function index(): View
    {
        return view('keuangan.produk.index', [
            'produks' => Produk::query()->orderBy('nama')->paginate(15),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150', 'unique:produks,nama'],
            'satuan' => ['required', 'string', 'max:30'],
            'harga_jual' => ['nullable', 'regex:/^\d{1,7}(?:\.\d{1,2})?$/'],
        ]);

        Produk::query()->create([
            'nama' => $data['nama'],
            'satuan' => $data['satuan'],
            'harga_jual' => $data['harga_jual'] ?? '0',
        ]);

        return redirect()->route('produk.index')
            ->with('success', 'Barang berhasil ditambahkan. Catat pembelian untuk menambah stok.');
    }
}
