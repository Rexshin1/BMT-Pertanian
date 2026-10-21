<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Pemilik;
use App\Models\Penjualan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PemilikController extends Controller
{
    public function index(): View
    {
        return view('keuangan.pemilik.index', [
            'pemiliks' => Pemilik::query()
                ->withCount('produks')
                ->orderByRaw('CAST(kode AS DECIMAL(20, 0))')
                ->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'alamat' => ['nullable', 'string', 'max:255'],
        ]);

        $pemilik = DB::transaction(function () use ($data): Pemilik {
            $sequenceLock = DB::table('pemilik_sequence_locks')->where('id', 1)->lockForUpdate()->first();

            if (! $sequenceLock) {
                throw new \RuntimeException('Owner ID sequence lock is missing. Run the pending database migrations.');
            }

            $kodeBerikutnya = (int) Pemilik::query()
                ->selectRaw('MAX(CAST(kode AS DECIMAL(20, 0))) AS kode_terakhir')
                ->value('kode_terakhir') + 1;

            return Pemilik::query()->create([
                'kode' => str_pad((string) $kodeBerikutnya, 3, '0', STR_PAD_LEFT),
                'nama' => trim($data['nama']),
                'alamat' => ! empty($data['alamat']) ? trim($data['alamat']) : null,
            ]);
        });

        return redirect()->route('pemilik.index')
            ->with('success', 'Nama berhasil disimpan dengan ID '.$pemilik->id_tampilan.'.');
    }

    public function edit(Pemilik $pemilik): View
    {
        return view('keuangan.pemilik.edit', compact('pemilik'));
    }

    public function update(Request $request, Pemilik $pemilik): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'alamat' => ['nullable', 'string', 'max:255'],
        ]);

        $pemilik->update([
            'nama' => trim($data['nama']),
            'alamat' => ! empty($data['alamat']) ? trim($data['alamat']) : null,
        ]);

        return redirect()->route('pemilik.index')->with('success', 'Nama pemilik berhasil diperbarui.');
    }

    public function destroy(Pemilik $pemilik): RedirectResponse
    {
        DB::transaction(function () use ($pemilik): void {
            $produkIds = $pemilik->produks()->pluck('id');

            Penjualan::query()->whereIn('produk_id', $produkIds)->delete();
            Pembelian::query()->whereIn('produk_id', $produkIds)->delete();
            $pemilik->produks()->delete();
            $pemilik->delete();
        });

        return redirect()->route('pemilik.index')
            ->with('success', 'Pemilik, barang, dan seluruh transaksi terkait berhasil dihapus.');
    }
}
