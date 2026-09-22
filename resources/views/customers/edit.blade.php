<form method="POST" action="{{ route('admin.customers.update', $customer) }}">
    @csrf
    @method('PUT')
    <div class="modal-body">
        @include('customers._form', ['customer' => $customer])
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Update</button>
    </div>
</form>
