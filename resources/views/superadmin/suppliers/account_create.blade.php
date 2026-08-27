{{-- ⚠️ Ganti @extends di bawah ini kalau layout view superadmin kamu bukan 'layouts.admin' --}}
@extends('layouts.admin')

@section('title', 'Buat Akun Login Supplier')

@section('content')
<h4 class="mb-3">Buat Akun Login untuk {{ $supplier->name }}</h4>

@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('superadmin.suppliers.account.store', $supplier) }}">
    @csrf
    <div class="mb-3">
        <label class="form-label">Email Login</label>
        <input type="email" name="email" class="form-control" required>
    </div>
    <div class="mb-3">
        <label class="form-label">Password</label>
        <input type="password" name="password" class="form-control" required minlength="8">
    </div>
    <div class="mb-3">
        <label class="form-label">Konfirmasi Password</label>
        <input type="password" name="password_confirmation" class="form-control" required minlength="8">
    </div>
    <button type="submit" class="btn btn-primary">Buat Akun</button>
    <a href="{{ route('superadmin.suppliers.show', $supplier) }}" class="btn btn-secondary">Batal</a>
</form>
@endsection
