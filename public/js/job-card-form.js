/**
 * Job card form: cascading vehicle select, repeatable service rows, and grand total.
 */
(function () {
    'use strict';

    const form = document.getElementById('job-card-form');

    if (!form) {
        return;
    }

    const rowsContainer = form.querySelector('[data-service-rows]');
    const addButton = form.querySelector('[data-add-service]');
    const totalDisplay = form.querySelector('[data-grand-total]');
    const template = document.getElementById('job-card-service-row-template');
    const customerSelect = $('#customer_id');
    const vehicleSelect = $('#vehicle_id');
    const vehicleAdd = form.querySelector('[data-create-url-template]');
    const modal = document.getElementById('commonModal');

    let nextIndex = rowsContainer ? rowsContainer.querySelectorAll('[data-service-row]').length : 0;
    let previousCustomerId = customerSelect.val() || '';

    function formatTotal(amount) {
        return amount.toFixed(2);
    }

    function recalculateTotal() {
        if (!totalDisplay || !rowsContainer) {
            return;
        }

        let total = 0;

        rowsContainer.querySelectorAll('[data-service-price]').forEach(function (input) {
            const value = parseFloat(input.value);

            if (!Number.isNaN(value)) {
                total += value;
            }
        });

        totalDisplay.textContent = formatTotal(total);
    }

    function initSelects(root) {
        if (!window.Helpers || typeof window.Helpers.ajaxSelect2 !== 'function') {
            return;
        }

        root.querySelectorAll('.init_select_dynamic').forEach(function (element) {
            window.Helpers.ajaxSelect2(element);
        });
    }

    function syncVehicleAdd(customerId) {
        if (!vehicleAdd) {
            return;
        }

        const templateUrl = vehicleAdd.getAttribute('data-create-url-template') || '';
        const enabled = Boolean(customerId);
        const url = enabled ? templateUrl.replace('__CUSTOMER__', encodeURIComponent(customerId)) : '';

        vehicleAdd.setAttribute('data-url', url);
        vehicleAdd.disabled = !enabled;
        vehicleAdd.title = enabled ? 'Add Vehicle' : 'Select a customer first';
        vehicleSelect.prop('disabled', !enabled);
    }

    function applyCatalogPrice(select, price) {
        const row = select.closest('[data-service-row]');
        const priceInput = row?.querySelector('[data-service-price]');

        if (priceInput && price !== undefined && price !== null && price !== '') {
            priceInput.value = price;
            recalculateTotal();
        }
    }

    function showAjaxErrors(modalForm, xhr) {
        modalForm.querySelectorAll('.ajax-error').forEach(function (element) {
            element.remove();
        });
        modalForm.querySelectorAll('.is-invalid').forEach(function (element) {
            element.classList.remove('is-invalid');
        });

        const errors = xhr.responseJSON?.errors;

        if (!errors) {
            window.alert(xhr.responseJSON?.message || 'Unable to save. Please try again.');

            return;
        }

        Object.keys(errors).forEach(function (key) {
            const input = modalForm.querySelector('[name="' + key + '"]');

            if (!input) {
                return;
            }

            input.classList.add('is-invalid');

            const feedback = document.createElement('div');
            feedback.className = 'invalid-feedback ajax-error d-block';
            feedback.textContent = errors[key][0];
            input.insertAdjacentElement('afterend', feedback);
        });
    }

    function selectCreatedOption(target, payload) {
        const $select = $(target);
        const value = String(payload.id);
        let option = $select.find('option').filter(function () {
            return this.value === value;
        }).get(0);

        if (!option) {
            option = new Option(payload.text, value, true, true);
            $select.append(option);
        }

        if (payload.data_attributes) {
            Object.keys(payload.data_attributes).forEach(function (key) {
                option.setAttribute(key, payload.data_attributes[key]);
            });
        }

        $select.val(value).trigger('change');
    }

    customerSelect.on('change', function () {
        const nextCustomerId = customerSelect.val() || '';

        if (String(nextCustomerId) === String(previousCustomerId)) {
            return;
        }

        previousCustomerId = nextCustomerId;
        vehicleSelect.val(null).trigger('change');
        syncVehicleAdd(nextCustomerId);
    });

    syncVehicleAdd(previousCustomerId);

    addButton?.addEventListener('click', function () {
        if (!template || !rowsContainer) {
            return;
        }

        const html = template.innerHTML.replaceAll('__INDEX__', String(nextIndex));
        nextIndex += 1;
        rowsContainer.insertAdjacentHTML('beforeend', html);
        initSelects(rowsContainer.lastElementChild);
        recalculateTotal();
    });

    rowsContainer?.addEventListener('click', function (event) {
        const button = event.target.closest('[data-remove-service]');

        if (!button) {
            return;
        }

        const row = button.closest('[data-service-row]');

        if (!row) {
            return;
        }

        const removeRow = function () {
            const select = row.querySelector('select.init_select_dynamic');

            if (select && $(select).hasClass('select2-hidden-accessible')) {
                $(select).select2('destroy');
            }

            row.remove();
            recalculateTotal();
        };

        if (!window.Swal) {
            removeRow();

            return;
        }

        window.Swal.fire({
            title: 'Remove this service?',
            text: 'This service line will be removed from the job card.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove',
            cancelButtonText: 'Cancel',
            confirmButtonColor: '#dc3545',
            reverseButtons: true,
        }).then(function (result) {
            if (result.isConfirmed) {
                removeRow();
            }
        });
    });

    rowsContainer?.addEventListener('change', function (event) {
        const select = event.target.closest('[data-service-select]');

        if (!select) {
            return;
        }

        applyCatalogPrice(select, select.selectedOptions[0]?.dataset.price);
    });

    if (rowsContainer) {
        $(rowsContainer).on('select2:select', '[data-service-select]', function (event) {
            applyCatalogPrice(this, event.params?.data?.data_attributes?.['data-price']);
        });
    }

    rowsContainer?.addEventListener('input', function (event) {
        if (event.target.closest('[data-service-price]')) {
            recalculateTotal();
        }
    });

    if (modal) {
        $(document).on('click', '#job-card-form [data-select-target]', function () {
            modal.dataset.selectTarget = this.getAttribute('data-select-target') || '';
        });

        $(modal).on('submit', 'form', function (event) {
            const target = modal.dataset.selectTarget || '';

            if (!target || !form.querySelector(target)) {
                return;
            }

            event.preventDefault();

            const modalForm = this;
            const submitButton = modalForm.querySelector('[type="submit"]');

            if (submitButton) {
                submitButton.disabled = true;
            }

            $.ajax({
                url: modalForm.action,
                method: 'POST',
                data: $(modalForm).serialize(),
                dataType: 'json',
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            }).done(function (payload) {
                selectCreatedOption(target, payload);
                bootstrap.Modal.getInstance(modal)?.hide();
            }).fail(function (xhr) {
                showAjaxErrors(modalForm, xhr);
            }).always(function () {
                if (submitButton) {
                    submitButton.disabled = false;
                }
            });
        });

        $(modal).on('hidden.bs.modal', function () {
            modal.dataset.selectTarget = '';
        });
    }

    recalculateTotal();
})();
