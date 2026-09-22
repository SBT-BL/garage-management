<form method="POST" action="{{ route('admin.customers.vehicles.store', $customer) }}">
    @csrf
    <div class="modal-body">
        @include('vehicles._form')
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Add Vehicle</button>
    </div>
</form>
