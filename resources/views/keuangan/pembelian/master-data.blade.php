@extends('layouts.modernize')

@section('title', 'View Pembelian')
@section('page-title', 'View Pembelian (HB)')

@section('content')
<div class="card">
  <div class="card-body">
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 mb-4">
      <div>
        <h5 class="card-title fw-semibold mb-1">Data Pembelian</h5>
        <span class="text-muted fs-3">Pembelian dikelompokkan per pemilik; pilih nama untuk melihat atau mengekspor datanya saja.</span>
      </div>
    </div>
    @include('keuangan.partials.date-filter', ['tableId' => 'pembelian-table'])
    <div class="table-responsive">
      <table id="pembelian-table" class="table table-hover align-middle text-nowrap mb-0">
        <thead>
          <tr><th>No.</th><th>Tgl</th><th>ID</th><th>Nama Pemilik</th><th>Nama Barang</th><th>Unit</th><th class="text-end">Harga HB / Unit</th><th class="text-end">Jumlah</th></tr>
        </thead>
        <tbody>
          @foreach ($pembelians as $pembelian)
            <tr data-date="{{ $pembelian->tanggal->format('Y-m-d') }}" data-owner-code="{{ $pembelian->produk->pemilik->kode ?? '' }}">
              <td>{{ $loop->iteration }}</td>
              <td data-order="{{ $pembelian->tanggal->format('Y-m-d') }}">{{ $pembelian->tanggal->format('d/m/Y') }}</td>
              <td>{{ $pembelian->produk->pemilik?->id_tampilan ?? '-' }}</td>
              <td>{{ $pembelian->produk->pemilik?->nama ?? 'Belum ditentukan' }}</td>
              <td class="fw-semibold">{{ $pembelian->produk->nama }}</td>
              <td>{{ $pembelian->satuan ?: $pembelian->produk->satuan }}</td>
              <td class="text-end" data-order="{{ $pembelian->harga_beli }}">Rp {{ number_format((float) $pembelian->harga_beli, 2, ',', '.') }}</td>
              <td class="text-end fw-semibold" data-order="{{ $pembelian->jumlah }}">{{ rtrim(rtrim(number_format((float) $pembelian->jumlah, 3, ',', '.'), '0'), ',') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@include('keuangan.partials.data-table', [
    'tableId' => 'pembelian-table',
    'title' => $pemilikTerpilihNama ? 'Data Pembelian - '.$pemilikTerpilihNama : 'Data Pembelian',
    'emptyMessage' => 'Belum ada data pembelian.',
])
