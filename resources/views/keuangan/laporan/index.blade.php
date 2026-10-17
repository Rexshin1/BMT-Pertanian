@extends('layouts.modernize')

@section('title', 'Laporan Laba Rugi')
@section('page-title', 'Laporan Laba Rugi (HB & HJ)')

@section('content')
<div class="card">
  <div class="card-body">
    <form class="row align-items-end g-3" method="GET" action="{{ route('laporan.laba-rugi') }}">
      <div class="col-sm-5 col-md-4">
        <label class="form-label" for="tanggal_mulai">Periode laporan: dari tanggal</label>
        <input class="form-control" type="date" id="tanggal_mulai" name="tanggal_mulai" value="{{ $tanggalMulai }}" required>
      </div>
      <div class="col-sm-5 col-md-4">
        <label class="form-label" for="tanggal_selesai">Periode laporan: sampai tanggal</label>
        <input class="form-control" type="date" id="tanggal_selesai" name="tanggal_selesai" value="{{ $tanggalSelesai }}" required>
      </div>
      <div class="col-sm-2 col-md-4">
        <button class="btn btn-primary" type="submit">Tampilkan Laporan</button>
      </div>
    </form>
  </div>
</div>

<div class="row">
  <div class="col-md-4">
    <div class="card"><div class="card-body">
      <h6 class="text-muted fw-semibold">Total Harga Beli (HB)</h6>
      <h4 class="fw-semibold mb-0">Rp {{ number_format((float) $ringkasan->total_hb, 2, ',', '.') }}</h4>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card"><div class="card-body">
      <h6 class="text-muted fw-semibold">Total Harga Jual (HJ)</h6>
      <h4 class="fw-semibold mb-0">Rp {{ number_format((float) $ringkasan->total_hj, 2, ',', '.') }}</h4>
    </div></div>
  </div>
  <div class="col-md-4">
    <div class="card"><div class="card-body">
      <h6 class="text-muted fw-semibold">Laba / Rugi</h6>
      <h4 class="fw-semibold mb-0 {{ (float) $ringkasan->total_laba < 0 ? 'text-danger' : 'text-success' }}">Rp {{ number_format((float) $ringkasan->total_laba, 2, ',', '.') }}</h4>
    </div></div>
  </div>
</div>

<div class="card">
  <div class="card-body">
    <div class="d-flex align-items-center justify-content-between mb-4">
      <div>
        <h5 class="card-title fw-semibold mb-1">Rincian Laba Rugi</h5>
        <span class="text-muted fs-3">{{ \Illuminate\Support\Carbon::parse($tanggalMulai)->format('d/m/Y') }} – {{ \Illuminate\Support\Carbon::parse($tanggalSelesai)->format('d/m/Y') }}</span>
      </div>
    </div>
    @include('keuangan.partials.date-filter', ['tableId' => 'laba-rugi-table'])
    <div class="table-responsive">
      <table id="laba-rugi-table" class="table table-hover align-middle text-nowrap mb-0">
        <thead><tr><th>No.</th><th>Tgl</th><th>Nama Barang</th><th>Unit</th><th class="text-end">Harga HB / Unit</th><th class="text-end">Jumlah HB</th><th class="text-end">Harga HJ / Unit</th><th class="text-end">Jumlah HJ</th><th class="text-end">Laba / Rugi</th></tr></thead>
        <tbody>
          @foreach ($penjualans as $penjualan)
            <tr data-date="{{ $penjualan->tanggal->format('Y-m-d') }}">
              <td>{{ $penjualan->id }}</td>
              <td data-order="{{ $penjualan->tanggal->format('Y-m-d') }}">{{ $penjualan->tanggal->format('d/m/Y') }}</td>
              <td class="fw-semibold">{{ $penjualan->produk->nama }}</td>
              <td>{{ $penjualan->satuan ?: $penjualan->produk->satuan }}</td>
              <td class="text-end" data-order="{{ $penjualan->harga_beli_satuan }}">Rp {{ number_format((float) $penjualan->harga_beli_satuan, 2, ',', '.') }}</td>
              <td class="text-end" data-order="{{ $penjualan->jumlah }}">{{ rtrim(rtrim(number_format((float) $penjualan->jumlah, 3, ',', '.'), '0'), ',') }}</td>
              <td class="text-end" data-order="{{ $penjualan->harga_jual }}">Rp {{ number_format((float) $penjualan->harga_jual, 2, ',', '.') }}</td>
              <td class="text-end" data-order="{{ $penjualan->jumlah }}">{{ rtrim(rtrim(number_format((float) $penjualan->jumlah, 3, ',', '.'), '0'), ',') }}</td>
              <td class="text-end fw-semibold {{ (float) $penjualan->laba < 0 ? 'text-danger' : 'text-success' }}" data-order="{{ $penjualan->laba }}">Rp {{ number_format((float) $penjualan->laba, 2, ',', '.') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>
@endsection

@include('keuangan.partials.data-table', [
    'tableId' => 'laba-rugi-table',
    'title' => 'Laporan Laba Rugi',
    'emptyMessage' => 'Tidak ada penjualan pada periode ini.',
])
