<form method="POST" action="{{ route('admin.services.update', $service) }}">
    @csrf
    @method('PUT')
    <div class="modal-body">
        @include('services._form', ['service' => $service])
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-success">Update</button>
    </div>
</form>
