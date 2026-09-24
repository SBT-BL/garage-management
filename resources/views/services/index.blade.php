@extends('layouts.app')

@section('title', 'Services — Garage Management')
@section('page_title', 'Services')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-3 mb-4">
    <div>
        <h1 class="page-header-title">Services</h1>
        <p class="page-header-subtitle">Garage service catalog and pricing</p>
    </div>
</div>

<div class="app-card placeholder-module">
    <div class="placeholder-module-icon" aria-hidden="true">
        <i class="bi bi-wrench-adjustable"></i>
    </div>
    <h2 class="placeholder-module-title">Services coming soon</h2>
    <p class="placeholder-module-text">
        Create and manage service items, labor rates, and packages here.
        Full CRUD will be available in a future update.
    </p>
</div>
@endsection
