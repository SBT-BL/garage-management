<div class="modal fade" id="deleteJobCardModal" tabindex="-1" aria-labelledby="deleteJobCardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h2 class="modal-title fs-5" id="deleteJobCardModalLabel">Delete job card</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to delete <strong id="deleteJobCardNumber"></strong>? This action cannot be undone.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteJobCardForm" method="POST" action="#">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete Job Card</button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.getElementById('deleteJobCardModal')?.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const url = button.getAttribute('data-delete-url');
        const number = button.getAttribute('data-job-card-number');

        document.getElementById('deleteJobCardForm').setAttribute('action', url);
        document.getElementById('deleteJobCardNumber').textContent = number;
    });
</script>
@endpush
