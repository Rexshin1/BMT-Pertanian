@extends('layouts.modernize')

@section('title', 'Data Barang')
@section('page-title', 'Data Barang & Stok')

@section('content')
<div class="row">
  <div class="col-lg-4">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title fw-semibold mb-4">Tambah Barang</h5>
        <form method="POST" action="{{ route('produk.store') }}">
          @csrf
          <div class="mb-3">
            <label class="form-label" for="nama">Nama barang</label>
            <input class="form-control" id="nama" name="nama" value="{{ old('nama') }}" maxlength="150" required>
            @error('nama')<div class="text-danger fs-3 mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label" for="satuan">Satuan</label>
            <input class="form-control" id="satuan" name="satuan" value="{{ old('satuan', 'pcs') }}" maxlength="30" required>
            @error('satuan')<div class="text-danger fs-3 mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-4">
            <label class="form-label" for="harga_jual">Harga jual standar (opsional)</label>
            <input class="form-control" type="number" id="harga_jual" name="harga_jual" value="{{ old('harga_jual') }}" min="0" max="9999999.99" step="0.01">
            @error('harga_jual')<div class="text-danger fs-3 mt-1">{{ $message }}</div>@enderror
          </div>
          <button class="btn btn-primary w-100" type="submit">Simpan Barang</button>
        </form>
        <p class="text-muted fs-3 mt-3 mb-0">Stok bertambah saat transaksi pembelian dicatat.</p>
      </div>
    </div>
  </div>
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-4">
          <h5 class="card-title fw-semibold mb-0">Persediaan Barang</h5>
          <a class="btn btn-sm btn-outline-primary" href="{{ route('pembelian.create') }}">Create Pembelian</a>
        </div>
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr><th>Nama Barang</th><th>Satuan</th><th class="text-end">Stok</th><th class="text-end">HB Terakhir</th><th class="text-end">Harga Jual</th></tr>
            </thead>
            <tbody>
              @forelse ($produks as $produk)
                <tr>
                  <td class="fw-semibold">{{ $produk->nama }}</td>
                  <td>{{ $produk->satuan }}</td>
                  <td class="text-end">{{ rtrim(rtrim(number_format((float) $produk->stok, 3, ',', '.'), '0'), ',') }}</td>
                  <td class="text-end">Rp {{ number_format((float) $produk->harga_beli, 2, ',', '.') }}</td>
                  <td class="text-end">Rp {{ number_format((float) $produk->harga_jual, 2, ',', '.') }}</td>
                </tr>
              @empty
                <tr><td colspan="5" class="text-center text-muted py-4">Belum ada barang. Tambahkan barang melalui formulir.</td></tr>
              @endforelse
            </tbody>
          </table>
        </div>
        <div class="mt-3">{{ $produks->links() }}</div>
      </div>
    </div>
  </div>
</div>
@endsection
