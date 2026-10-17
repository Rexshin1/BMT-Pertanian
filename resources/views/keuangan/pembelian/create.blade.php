@extends('layouts.modernize')

@section('title', 'Create Pembelian')
@section('page-title', 'Create Pembelian / Stok Masuk (HB)')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-4">
          <h5 class="card-title fw-semibold mb-0">Form Pembelian</h5>
          <a class="btn btn-outline-primary btn-sm" href="{{ route('pembelian.index') }}">View Pembelian</a>
        </div>
          <form method="POST" action="{{ route('pembelian.store') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label" for="nama_barang">Nama Barang</label>
              <input class="form-control" type="text" id="nama_barang" name="nama_barang" value="{{ old('nama_barang') }}" maxlength="150" required>
              @error('nama_barang')<div class="text-danger fs-3 mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="row g-3 mb-3">
              <div class="col-6 col-md-2">
                <label class="form-label" for="nomor">No.</label>
                <input class="form-control" id="nomor" value="{{ $nomorBerikutnya }}" readonly>
              </div>
              <div class="col-6 col-md-2">
                <label class="form-label" for="tanggal">Tgl</label>
                <input class="form-control" type="date" id="tanggal" name="tanggal" value="{{ old('tanggal', now()->toDateString()) }}" required>
                @error('tanggal')<div class="text-danger fs-3 mt-1">{{ $message }}</div>@enderror
              </div>
              <div class="col-4 col-md-2">
                <label class="form-label" for="satuan">Unit</label>
                <input class="form-control" type="text" id="satuan" name="satuan" value="{{ old('satuan') }}" placeholder="per pcs / per botol" maxlength="30" required>
                @error('satuan')<div class="text-danger fs-3 mt-1">{{ $message }}</div>@enderror
              </div>
              <div class="col-8 col-md-3">
                <label class="form-label" for="harga_beli">Harga beli (HB)</label>
                <div class="input-group"><span class="input-group-text">Rp</span><input class="form-control" type="text" id="harga_beli_display" value="{{ old('harga_beli') ? number_format((float) old('harga_beli'), 2, ',', '.') : '' }}" inputmode="decimal" autocomplete="off" required><input type="hidden" id="harga_beli" name="harga_beli" value="{{ old('harga_beli') }}"></div>
                <small class="text-muted">Per unit</small>
                @error('harga_beli')<div class="text-danger fs-3 mt-1">{{ $message }}</div>@enderror
              </div>
              <div class="col-4 col-md-3">
                <label class="form-label" for="jumlah">Jumlah</label>
                <input class="form-control" type="number" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" min="0.001" max="999999.999" step="0.001" required>
                @error('jumlah')<div class="text-danger fs-3 mt-1">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="mb-4">
              <label class="form-label" for="total_jumlah">Total harga pembelian (Jumlah × Harga)</label>
              <div class="input-group"><span class="input-group-text">Rp</span><input class="form-control" id="total_jumlah" value="0,00" readonly></div>
            </div>
            <button class="btn btn-primary w-100" type="submit">Simpan Pembelian</button>
          </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  (() => {
    const quantityInput = document.getElementById('jumlah');
    const priceInput = document.getElementById('harga_beli');
    const priceDisplay = document.getElementById('harga_beli_display');
    const totalInput = document.getElementById('total_jumlah');

    const parsePrice = (value) => {
      const normalized = value.replace(/\./g, '').replace(',', '.').replace(/[^\d.]/g, '');
      const amount = Number(normalized);

      return Number.isFinite(amount) ? amount : 0;
    };

    const formatPrice = (value) => {
      const cleaned = value.replace(/[^\d,]/g, '');
      const [whole = '', ...decimalParts] = cleaned.split(',');
      const groupedWhole = whole.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
      const hasDecimal = cleaned.includes(',');
      const decimal = decimalParts.join('').slice(0, 2);

      return groupedWhole + (hasDecimal ? ',' + decimal : '');
    };

    const updateTotal = () => {
      const quantity = Number(quantityInput.value);
      const price = parsePrice(priceDisplay.value);
      priceInput.value = price > 0 ? price.toFixed(2) : '';
      totalInput.value = (quantity > 0 && price > 0)
        ? new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2, maximumFractionDigits: 2 }).format(quantity * price)
        : '0,00';
    };

    priceDisplay.addEventListener('input', () => {
      const start = priceDisplay.selectionStart;
      const oldValue = priceDisplay.value;
      const formatted = formatPrice(oldValue);
      const delta = formatted.length - oldValue.length;
      priceDisplay.value = formatted;
      priceDisplay.setSelectionRange(Math.max(0, start + delta), Math.max(0, start + delta));
      updateTotal();
    });

    quantityInput.addEventListener('input', updateTotal);
    updateTotal();
  })();
</script>
@endpush
