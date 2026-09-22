{{-- Shared create/edit modal shell — load form HTML via data-ajax-popup --}}
<div
    class="modal fade"
    id="commonModal"
    tabindex="-1"
    aria-labelledby="commonModalTitle"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commonModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="body"></div>
        </div>
    </div>
</div>

{{-- Nested / stacked modal (e.g. create role while creating user) --}}
<div
    class="modal fade"
    id="commonModalOver"
    tabindex="-1"
    aria-labelledby="commonModalOverTitle"
    aria-hidden="true"
    data-bs-backdrop="static"
    data-bs-keyboard="false"
>
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="commonModalOverTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="body"></div>
        </div>
    </div>
</div>

<div class="loader-wrapper d-none" aria-hidden="true">
    <div class="site-loader" role="status">
        <span class="visually-hidden">Loading…</span>
    </div>
</div>
