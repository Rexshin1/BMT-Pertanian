@extends('layouts.modernize')

@section('title', 'Create Penjualan')
@section('page-title', 'Create Penjualan / Stok Keluar (HJ)')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-10">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-4">
          <h5 class="card-title fw-semibold mb-0">Form Penjualan</h5>
          <a class="btn btn-outline-primary btn-sm" href="{{ route('penjualan.index') }}">View Penjualan</a>
        </div>
          <form method="POST" action="{{ route('penjualan.store') }}">
            @csrf
            <div class="mb-3">
              <label class="form-label" for="produk_id">Nama Barang</label>
              <select class="form-select" id="produk_id" name="produk_id" required>
                <option value="">Pilih barang yang stoknya tersedia</option>
                @foreach ($produks as $produk)
                  <option value="{{ $produk->id }}" data-unit="{{ $produk->satuan }}" @selected((string) old('produk_id') === (string) $produk->id)>
                    {{ $produk->nama }} — stok {{ rtrim(rtrim(number_format((float) $produk->stok, 3, ',', '.'), '0'), ',') }} {{ $produk->satuan }}
                  </option>
                @endforeach
              </select>
              @if ($produks->isEmpty())
                <small class="text-muted d-block mt-1">Belum ada barang dengan stok tersedia. Catat Pembelian terlebih dahulu.</small>
              @endif
              @error('produk_id')<div class="text-danger fs-3 mt-1">{{ $message }}</div>@enderror
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
                <label class="form-label" for="harga_jual">Harga jual (HJ)</label>
                <div class="input-group"><span class="input-group-text">Rp</span><input class="form-control" type="text" id="harga_jual_display" value="{{ old('harga_jual') ? number_format((float) old('harga_jual'), 2, ',', '.') : '' }}" inputmode="decimal" autocomplete="off" required><input type="hidden" id="harga_jual" name="harga_jual" value="{{ old('harga_jual') }}"></div>
                <small class="text-muted">Per unit</small>
                @error('harga_jual')<div class="text-danger fs-3 mt-1">{{ $message }}</div>@enderror
              </div>
              <div class="col-4 col-md-3">
                <label class="form-label" for="jumlah">Jumlah</label>
                <input class="form-control" type="number" id="jumlah" name="jumlah" value="{{ old('jumlah') }}" min="0.001" max="999999.999" step="0.001" required>
                @error('jumlah')<div class="text-danger fs-3 mt-1">{{ $message }}</div>@enderror
              </div>
            </div>
            <div class="mb-4">
              <label class="form-label" for="total_jumlah">Total harga penjualan (Jumlah × Harga)</label>
              <div class="input-group"><span class="input-group-text">Rp</span><input class="form-control" id="total_jumlah" value="0,00" readonly></div>
            </div>
            <button class="btn btn-primary w-100" type="submit">Simpan Penjualan</button>
          </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('scripts')
<script>
  (() => {
    const productSelect = document.getElementById('produk_id');
    const unitInput = document.getElementById('satuan');
    const quantityInput = document.getElementById('jumlah');
    const priceInput = document.getElementById('harga_jual');
    const priceDisplay = document.getElementById('harga_jual_display');
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

    productSelect.addEventListener('change', () => {
      const selectedProduct = productSelect.selectedOptions[0];
      unitInput.value = selectedProduct?.dataset.unit || '';
    });

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
    if (productSelect.value && !unitInput.value) {
      unitInput.value = productSelect.selectedOptions[0]?.dataset.unit || '';
    }
    updateTotal();
  })();
</script>
@endpush
