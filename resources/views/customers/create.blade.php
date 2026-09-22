<form method="POST" action="{{ route('admin.customers.store') }}">
    @csrf
    <div class="modal-body">
        @include('customers._form')
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Create</button>
    </div>
</form>
