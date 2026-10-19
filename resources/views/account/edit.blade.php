@extends('layouts.modernize')

@section('title', 'Edit Account')
@section('page-title', 'Edit Account')

@section('content')
<div class="row justify-content-center">
  <div class="col-lg-8">
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center gap-3 mb-4">
          <img src="{{ asset('assets/images/logos/bmt-cinde-barokah.png') }}" alt="BMT Cinde Barokah logo" width="56" height="56">
          <div>
            <h5 class="card-title fw-semibold mb-1">Account Details</h5>
            <p class="text-muted mb-0">Update your profile and password.</p>
          </div>
        </div>
        <form method="POST" action="{{ route('account.update') }}">
          @csrf
          @method('PUT')
          <div class="mb-3">
            <label class="form-label" for="name">Name</label>
            <input class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" autocomplete="name" maxlength="255" required>
            @error('name')<div class="text-danger mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label" for="email">Email Address</label>
            <input class="form-control" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" autocomplete="email" maxlength="255" required>
            @error('email')<div class="text-danger mt-1">{{ $message }}</div>@enderror
          </div>
          <hr class="my-4">
          <h6 class="fw-semibold">Change Password</h6>
          <p class="text-muted">Leave the new password fields blank to keep your current password.</p>
          <div class="mb-3">
            <label class="form-label" for="current_password">Current Password</label>
            <input class="form-control" type="password" id="current_password" name="current_password" autocomplete="current-password" required>
            @error('current_password')<div class="text-danger mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-3">
            <label class="form-label" for="password">New Password</label>
            <input class="form-control" type="password" id="password" name="password" autocomplete="new-password" minlength="8">
            @error('password')<div class="text-danger mt-1">{{ $message }}</div>@enderror
          </div>
          <div class="mb-4">
            <label class="form-label" for="password_confirmation">Confirm New Password</label>
            <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" autocomplete="new-password" minlength="8">
          </div>
          <button class="btn btn-primary" type="submit">Save Account</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
