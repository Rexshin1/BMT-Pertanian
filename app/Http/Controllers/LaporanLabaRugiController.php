<?php

namespace App\Http\Controllers;

use App\Models\Pemilik;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class LaporanLabaRugiController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'tanggal_mulai' => ['nullable', 'date_format:Y-m-d'],
            'tanggal_selesai' => ['nullable', 'date_format:Y-m-d', 'after_or_equal:tanggal_mulai'],
            'pemilik' => ['nullable', 'string', 'exists:pemiliks,kode'],
        ]);

        $tanggalMulai = $filters['tanggal_mulai'] ?? Carbon::today()->startOfMonth()->toDateString();
        $tanggalSelesai = $filters['tanggal_selesai'] ?? Carbon::today()->toDateString();
        $query = Penjualan::query()
            ->with('produk.pemilik')
            ->whereDate('tanggal', '>=', $tanggalMulai)
            ->whereDate('tanggal', '<=', $tanggalSelesai);

        if (! empty($filters['pemilik'])) {
            $query->whereHas('produk.pemilik', fn ($ownerQuery) => $ownerQuery->where('kode', $filters['pemilik']));
        }

        $ringkasan = (clone $query)
            ->selectRaw('COALESCE(SUM(total_harga_beli), 0) AS total_hb')
            ->selectRaw('COALESCE(SUM(total_harga_jual), 0) AS total_hj')
            ->selectRaw('COALESCE(SUM(laba), 0) AS total_laba')
            ->first();

        return view('keuangan.laporan.index', [
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'ringkasan' => $ringkasan,
            'pemiliks' => Pemilik::query()->orderByRaw('CAST(kode AS DECIMAL(20, 0))')->get(),
            'pemilikTerpilih' => $filters['pemilik'] ?? '',
            'pemilikTerpilihNama' => ! empty($filters['pemilik'])
                ? Pemilik::query()->where('kode', $filters['pemilik'])->value('nama')
                : null,
            'penjualans' => $query->latest('tanggal')->latest('id')->get(),
        ]);
    }
}
