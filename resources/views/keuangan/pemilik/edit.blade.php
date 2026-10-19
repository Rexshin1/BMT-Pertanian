@extends('layouts.modernize')

@section('title', 'Edit Nama Pemilik')
@section('page-title', 'Edit Nama Pemilik')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-6">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-between mb-4">
          <h5 class="card-title fw-semibold mb-0">Edit Nama Pemilik</h5>
          <a class="btn btn-outline-secondary btn-sm" href="{{ route('pemilik.index') }}">Kembali</a>
        </div>
        <form method="POST" action="{{ route('pemilik.update', $pemilik) }}">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label class="form-label" for="kode">ID Pemilik</label>
            <input class="form-control" id="kode" value="{{ $pemilik->id_tampilan }}" readonly>
          </div>
          <div class="mb-4">
            <label class="form-label" for="nama">Nama pemilik <span class="text-danger" aria-hidden="true">*</span><span class="visually-hidden"> wajib diisi</span></label>
            <input class="form-control" id="nama" name="nama" value="{{ old('nama', $pemilik->nama) }}" maxlength="150" required>
            @error('nama')<div class="text-danger fs-3 mt-1">{{ $message }}</div>@enderror
          </div>
          <button class="btn btn-primary w-100" type="submit">Simpan Perubahan</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
