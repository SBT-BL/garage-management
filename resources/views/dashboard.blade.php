@extends('layouts.app')

@section('title', 'Dashboard — Garage Management')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4">
    <div>
        <h1 class="page-header-title">Dashboard</h1>
        <p class="page-header-subtitle">Welcome back to your garage management system.</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('admin.customers.index') }}" class="btn btn-primary">
            <i class="bi bi-people me-1"></i> Customers
        </a>
    </div>
</div>

<div class="app-card p-4">
    <p class="text-muted mb-0">Use the Customers module to manage your garage customers.</p>
</div>
@endsection
