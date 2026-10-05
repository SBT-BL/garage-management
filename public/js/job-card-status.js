/**
 * Change a job card status from the listing badge.
 */
(function () {
    'use strict';

    const badgeClasses = ['text-bg-warning', 'text-bg-info', 'text-bg-success'];

    document.addEventListener('focusin', function (event) {
        const select = event.target.closest('[data-job-card-status]');

        if (!select) {
            return;
        }

        select.dataset.previousStatus = select.value;
    });

    document.addEventListener('change', function (event) {
        const select = event.target.closest('[data-job-card-status]');

        if (!select || select.dataset.saving === '1') {
            return;
        }

        const previous = select.dataset.previousStatus || select.value;
        const picker = select.closest('.job-card-status-picker');
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        select.dataset.saving = '1';
        select.disabled = true;

        fetch(select.dataset.statusUrl, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ status: select.value }),
        })
            .then(function (response) {
                if (!response.ok) {
                    throw new Error('Unable to update status.');
                }

                return response.json();
            })
            .then(function (payload) {
                select.dataset.previousStatus = payload.status;

                if (picker) {
                    badgeClasses.forEach(function (className) {
                        picker.classList.remove(className);
                    });
                    picker.classList.add(payload.badge_class);
                }

                const filter = document.getElementById('job-card-filter-status');
                const filterValue = filter ? filter.value : 'open';
                const stillVisible = filterValue === 'all'
                    || ((filterValue === 'open' || filterValue === '') && (payload.status === 'Pending' || payload.status === 'In Progress'))
                    || filterValue === payload.status;

                if (!stillVisible) {
                    document.querySelector('[data-mobile-card-list]')?.dispatchEvent(new Event('mobile-card-list:reload'));
                    window.LaravelDataTables?.['job-cards-table']?.draw(false);
                }
            })
            .catch(function () {
                select.value = previous;
                window.alert('Unable to update the status. Please try again.');
            })
            .finally(function () {
                select.disabled = false;
                select.dataset.saving = '0';
            });
    });
})();
