@extends('layouts.modernize')

@section('title', 'View Penjualan')
@section('page-title', 'View Penjualan (HJ)')

@section('content')
<div class="card">
  <div class="card-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
      <div>
        <h5 class="card-title fw-semibold mb-1">Data Penjualan</h5>
        <span class="text-muted fs-3">Penjualan dikelompokkan per pemilik; pilih nama untuk melihat atau mengekspor datanya saja.</span>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('laporan.laba-rugi') }}" class="btn btn-outline-primary">Laporan Laba Rugi</a>
      </div>
    </div>
    @include('keuangan.partials.date-filter', ['tableId' => 'penjualan-table'])
    <div class="table-responsive">
      <table id="penjualan-table" class="table table-hover align-middle text-nowrap mb-0">
        <thead>
          <tr><th>No.</th><th>Tgl</th><th>ID</th><th>Nama Pemilik</th><th>Nama Barang</th><th>Unit</th><th class="text-end">Harga HJ / Unit</th><th class="text-end">Jumlah</th></tr>
        </thead>
        <tbody>
          @foreach ($penjualans as $penjualan)
            <tr data-date="{{ $penjualan->tanggal->format('Y-m-d') }}" data-owner-code="{{ $penjualan->produk->pemilik->kode ?? '' }}">
              <td>{{ $loop->iteration }}</td>
              <td data-order="{{ $penjualan->tanggal->format('Y-m-d') }}">{{ $penjualan->tanggal->format('d/m/Y') }}</td>
              <td>{{ $penjualan->produk->pemilik?->id_tampilan ?? '-' }}</td>
              <td>{{ $penjualan->produk->pemilik?->nama ?? 'Belum ditentukan' }}</td>
              <td class="fw-semibold">{{ $penjualan->produk->nama }}</td>
              <td>{{ $penjualan->satuan ?: $penjualan->produk->satuan }}</td>
              <td class="text-end" data-order="{{ $penjualan->harga_jual }}">Rp {{ number_format((float) $penjualan->harga_jual, 2, ',', '.') }}</td>
              <td class="text-end fw-semibold" data-order="{{ $penjualan->jumlah }}">{{ rtrim(rtrim(number_format((float) $penjualan->jumlah, 3, ',', '.'), '0'), ',') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@include('keuangan.partials.data-table', [
    'tableId' => 'penjualan-table',
    'title' => $pemilikTerpilihNama ? 'Data Penjualan - '.$pemilikTerpilihNama : 'Data Penjualan',
    'emptyMessage' => 'Belum ada data penjualan.',
])
