<form method="POST" action="{{ route('admin.services.store') }}">
    @csrf
    <div class="modal-body">
        @include('services._form')
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Create</button>
    </div>
</form>
