@extends('layouts.app')

@section('title', 'Add Customer — Garage Management')

@section('content')
<div class="mb-4">
    <h1 class="page-header-title">Add Customer</h1>
    <p class="page-header-subtitle">Add a new customer to your garage</p>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="app-card p-4">
            <h2 class="h6 text-uppercase text-muted mb-3">Customer Information</h2>

            <form method="POST" action="{{ route('admin.customers.store') }}">
                @csrf
                @include('customers._form')

                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.customers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Save Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
