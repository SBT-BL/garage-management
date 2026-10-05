@extends('layouts.app')

@section('title', 'Create Job Card — Garage Management')
@section('page_title', 'Create Job Card')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.job-cards.index') }}" class="text-decoration-none text-muted">
        <i class="bi bi-arrow-left me-1"></i> Back to Job Cards
    </a>
</div>

<div class="d-none d-lg-block mb-4">
    <h1 class="page-header-title">Create Job Card</h1>
    <p class="page-header-subtitle">Open a new job for a customer vehicle</p>
</div>

<div class="app-card p-3 p-md-4">
    <form
        method="POST"
        action="{{ route('admin.job-cards.store') }}"
        id="job-card-form"
    >
        @csrf
        @include('job-cards._form')

        <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4">
            <a href="{{ route('admin.job-cards.index') }}" class="btn btn-outline-secondary">Cancel</a>
            <button type="submit" class="btn btn-success">Create Job Card</button>
        </div>
    </form>
</div>
@endsection

@include('job-cards.partials.form-assets')
