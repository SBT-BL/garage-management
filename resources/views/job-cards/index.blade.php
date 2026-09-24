@extends('layouts.app')

@section('title', 'Job Cards — Garage Management')
@section('page_title', 'Job Cards')

@section('content')
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-start gap-3 mb-4">
    <div>
        <h1 class="page-header-title">Job Cards</h1>
        <p class="page-header-subtitle">Track repair jobs from intake to delivery</p>
    </div>
</div>

<div class="app-card placeholder-module">
    <div class="placeholder-module-icon" aria-hidden="true">
        <i class="bi bi-clipboard2-check"></i>
    </div>
    <h2 class="placeholder-module-title">Job card form coming soon</h2>
    <p class="placeholder-module-text">
        Open new job cards, assign work, and track progress for every vehicle.
        Full CRUD will be available in a future update.
    </p>
</div>
@endsection
