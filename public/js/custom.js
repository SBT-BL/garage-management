/**
 * Shared AJAX modal helpers (BookingGo-style).
 * Trigger: <a data-ajax-popup="true" data-url="..." data-title="..." data-size="md">
 */
(function ($) {
    'use strict';

    const MODAL_SIZES = ['modal-sm', 'modal-lg', 'modal-xl'];

    function setModalSize($dialog, size) {
        $dialog.removeClass(MODAL_SIZES.join(' '));

        if (size && size !== 'md' && size !== '') {
            $dialog.addClass('modal-' + size);
        }
    }

    function showLoader() {
        $('.loader-wrapper').removeClass('d-none');
    }

    function hideLoader() {
        $('.loader-wrapper').addClass('d-none');
    }

    function showBootstrapModal(selector) {
        const el = document.querySelector(selector);
        if (!el || typeof bootstrap === 'undefined') {
            return;
        }

        bootstrap.Modal.getOrCreateInstance(el).show();
    }

    function loadAjaxModal(options) {
        const modalSelector = options.modalSelector;
        const $modal = $(modalSelector);
        const $dialog = $modal.find('.modal-dialog');

        $modal.find('.modal-title').html(options.title || '');
        setModalSize($dialog, options.size || 'md');

        $.ajax({
            url: options.url,
            beforeSend: showLoader,
            success: function (data) {
                hideLoader();
                $modal.find('.body').html(data);
                showBootstrapModal(modalSelector);
            },
            error: function (xhr) {
                hideLoader();
                const message =
                    (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error)) ||
                    'Unable to load the form. Please try again.';
                window.alert(message);
            },
        });
    }

    $(document).on(
        'click',
        'a[data-ajax-popup="true"], button[data-ajax-popup="true"], div[data-ajax-popup="true"]',
        function (e) {
            e.preventDefault();

            const $el = $(this);
            const url = $el.data('url');

            if (!url) {
                return;
            }

            loadAjaxModal({
                modalSelector: '#commonModal',
                title: $el.data('title'),
                size: $el.data('size'),
                url: url,
            });
        }
    );

    $(document).on(
        'click',
        'a[data-ajax-popup-over="true"], button[data-ajax-popup-over="true"], div[data-ajax-popup-over="true"]',
        function (e) {
            e.preventDefault();

            const $el = $(this);
            let url = $el.data('url');
            const validate = $el.attr('data-validate');

            if (!url) {
                return;
            }

            if (validate) {
                const id = $(validate).val();
                url += (url.indexOf('?') === -1 ? '?' : '&') + 'id=' + encodeURIComponent(id || '');
            }

            loadAjaxModal({
                modalSelector: '#commonModalOver',
                title: $el.data('title'),
                size: $el.data('size'),
                url: url,
            });
        }
    );

    $('#commonModal, #commonModalOver').on('hidden.bs.modal', function () {
        $(this).find('.body').empty();
        setModalSize($(this).find('.modal-dialog'), 'md');
    });
})(jQuery);
