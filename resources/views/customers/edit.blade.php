@extends('layouts.app')

@section('title', 'Edit Customer — Garage Management')

@section('content')
<div class="mb-4">
    <h1 class="page-header-title">Edit Customer</h1>
    <p class="page-header-subtitle">Update customer details</p>
</div>

<div class="row justify-content-center">
    <div class="col-12 col-lg-8">
        <div class="app-card p-4">
            <h2 class="h6 text-uppercase text-muted mb-3">Customer Information</h2>

            <form method="POST" action="{{ route('admin.customers.update', $customer) }}">
                @csrf
                @method('PUT')
                @include('customers._form', ['customer' => $customer])

                <div class="d-flex flex-column flex-sm-row justify-content-end gap-2 mt-4">
                    <a href="{{ route('admin.customers.show', $customer) }}" class="btn btn-outline-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Customer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
