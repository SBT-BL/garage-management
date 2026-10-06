@extends('layouts.app')

@section('title', 'Login — Garage Management')

@section('main_class', '')

@section('content')
<div class="min-vh-100 d-flex align-items-center justify-content-center py-4 px-3">
    <div class="card shadow-sm border-0 w-100" style="max-width: 420px;">
        <div class="card-body p-4 p-md-5">
            <div class="text-center mb-4">
                <img
                    src="{{ asset('logo/logo-dark.png') }}"
                    alt="Garage Management"
                    class="mb-3"
                    style="max-height: 40px; width: auto;"
                >
                <h1 class="h4 mb-1 fw-semibold">Welcome back</h1>
                <p class="text-muted mb-0">Sign in to your admin account</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('login.store') }}" novalidate>
                @csrf

                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        name="email"
                        id="email"
                        value="{{ old('email') }}"
                        class="form-control @error('email') is-invalid @enderror"
                        autocomplete="username"
                        autofocus
                        required
                    >
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <input
                        type="password"
                        name="password"
                        id="password"
                        class="form-control @error('password') is-invalid @enderror"
                        autocomplete="current-password"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Login
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
