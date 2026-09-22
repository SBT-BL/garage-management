<form method="POST" action="{{ route('admin.customers.vehicles.update', [$customer, $vehicle]) }}">
    @csrf
    @method('PUT')
    <div class="modal-body">
        @include('vehicles._form', ['vehicle' => $vehicle])
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Update</button>
    </div>
</form>
