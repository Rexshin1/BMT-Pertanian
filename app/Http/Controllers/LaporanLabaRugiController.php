<?php

namespace App\Http\Controllers;

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
        ]);

        $tanggalMulai = $filters['tanggal_mulai'] ?? Carbon::today()->startOfMonth()->toDateString();
        $tanggalSelesai = $filters['tanggal_selesai'] ?? Carbon::today()->toDateString();
        $query = Penjualan::query()
            ->with('produk')
            ->whereDate('tanggal', '>=', $tanggalMulai)
            ->whereDate('tanggal', '<=', $tanggalSelesai);

        $ringkasan = (clone $query)
            ->selectRaw('COALESCE(SUM(total_harga_beli), 0) AS total_hb')
            ->selectRaw('COALESCE(SUM(total_harga_jual), 0) AS total_hj')
            ->selectRaw('COALESCE(SUM(laba), 0) AS total_laba')
            ->first();

        return view('keuangan.laporan.index', [
            'tanggalMulai' => $tanggalMulai,
            'tanggalSelesai' => $tanggalSelesai,
            'ringkasan' => $ringkasan,
            'penjualans' => $query->latest('tanggal')->latest('id')->get(),
        ]);
    }
}
