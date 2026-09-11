<div class="modal fade" id="deleteCustomerModal" tabindex="-1" aria-labelledby="deleteCustomerModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="deleteCustomerModalLabel">Delete customer</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete <strong id="deleteCustomerName"></strong>? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteCustomerForm" method="POST" action="#">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Customer</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('deleteCustomerModal')?.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const url = button.getAttribute('data-delete-url');
        const name = button.getAttribute('data-customer-name');

        document.getElementById('deleteCustomerForm').setAttribute('action', url);
        document.getElementById('deleteCustomerName').textContent = name;
    });
</script>
@endpush
