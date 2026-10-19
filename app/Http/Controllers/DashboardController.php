<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Penjualan;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $today = now()->startOfDay();
        $monthStart = $today->copy()->startOfMonth();
        $yearStart = $today->copy()->startOfYear();

        $salesByDate = Penjualan::query()
            ->whereBetween('tanggal', [$yearStart->toDateString(), $today->toDateString()])
            ->selectRaw('tanggal, SUM(total_harga_jual) AS total_sales, SUM(total_harga_beli) AS total_cost, SUM(laba) AS total_profit')
            ->groupBy('tanggal')
            ->get()
            ->keyBy(fn (Penjualan $transaction) => $transaction->tanggal->toDateString());
        $dailyPurchases = Pembelian::query()
            ->whereBetween('tanggal', [$monthStart->toDateString(), $today->toDateString()])
            ->selectRaw('tanggal, SUM(total_harga) AS total')
            ->groupBy('tanggal')
            ->pluck('total', 'tanggal');

        $chartDates = collect();
        $chartSales = [];
        $chartPurchases = [];
        $chartProfit = [];

        for ($date = $monthStart->copy(); $date->lte($today); $date->addDay()) {
            $key = $date->toDateString();
            $chartDates->push($date->format('d/m'));
            $chartSales[] = (float) ($salesByDate[$key]->total_sales ?? 0);
            $chartPurchases[] = (float) ($dailyPurchases[$key] ?? 0);
            $chartProfit[] = (float) ($salesByDate[$key]->total_profit ?? 0);
        }

        $yearSales = (float) $salesByDate->sum('total_sales');
        $yearCost = (float) $salesByDate->sum('total_cost');
        $yearProfit = (float) $salesByDate->sum('total_profit');

        $sales = Penjualan::query()
            ->with('produk')
            ->latest('tanggal')
            ->latest('id')
            ->limit(8)
            ->get()
            ->map(fn (Penjualan $transaction) => [
                'id' => $transaction->id,
                'date' => $transaction->tanggal,
                'created_at' => $transaction->created_at,
                'name' => $transaction->produk->nama,
                'unit' => $transaction->satuan ?: $transaction->produk->satuan,
                'quantity' => (float) $transaction->jumlah,
                'type' => 'Penjualan',
                'amount' => (float) $transaction->total_harga_jual,
            ]);
        $purchases = Pembelian::query()
            ->with('produk')
            ->latest('tanggal')
            ->latest('id')
            ->limit(8)
            ->get()
            ->map(fn (Pembelian $transaction) => [
                'id' => $transaction->id,
                'date' => $transaction->tanggal,
                'created_at' => $transaction->created_at,
                'name' => $transaction->produk->nama,
                'unit' => $transaction->satuan ?: $transaction->produk->satuan,
                'quantity' => (float) $transaction->jumlah,
                'type' => 'Pembelian',
                'amount' => (float) $transaction->total_harga,
            ]);

        return view('pages.index', [
            'dashboardData' => [
                'labels' => $chartDates->all(),
                'sales' => $chartSales,
                'purchases' => $chartPurchases,
                'profit' => $chartProfit,
                'year' => $today->year,
                'yearSales' => $yearSales,
                'yearCost' => $yearCost,
                'yearProfit' => $yearProfit,
                'monthProfit' => array_sum($chartProfit),
                'monthName' => $today->locale('id')->translatedFormat('F Y'),
            ],
            'recentTransactions' => $sales
                ->concat($purchases)
                ->sortByDesc(fn (array $transaction) => $transaction['created_at']?->timestamp ?? 0)
                ->take(8)
                ->values(),
        ]);
    }
}
