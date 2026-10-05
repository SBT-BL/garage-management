/**
 * Reusable mobile card list: search + infinite scroll.
 * Mounts on every [data-mobile-card-list] element.
 * Expected JSON: { html: string, meta: { current_page, has_more, total, last_page } }
 */
(function () {
    function initMobileCardList(root) {
        const cardsUrl = root.dataset.cardsUrl;
        const errorMessage =
            root.dataset.errorMessage || 'Could not load results. Please try again.';
        const grid = root.querySelector('[data-mobile-card-grid]');
        const sentinel = root.querySelector('[data-mobile-card-sentinel]');
        const loader = root.querySelector('[data-mobile-card-loader]');
        const endMessage = root.querySelector('[data-mobile-card-end]');
        const searchInput = root.querySelector('[data-mobile-card-search]');
        const clearButton = root.querySelector('[data-mobile-card-clear]');

        if (!cardsUrl || !grid || !sentinel) {
            return;
        }

        let page = 1;
        let query = '';
        let loading = false;
        let hasMore = true;
        let searchTimer = null;

        function setLoaderVisible(visible) {
            loader?.classList.toggle('d-none', !visible);
        }

        function setEndVisible(visible) {
            endMessage?.classList.toggle('d-none', !visible);
        }

        function updateClearButton() {
            if (!searchInput || !clearButton) {
                return;
            }

            clearButton.classList.toggle('d-none', searchInput.value.trim() === '');
        }

        function hasRenderedCards() {
            return grid.querySelector('.mobile-card') !== null;
        }

        async function loadPage(reset) {
            if (loading) {
                return;
            }

            if (reset) {
                page = 1;
                hasMore = true;
                grid.innerHTML = '';
                setEndVisible(false);
            }

            if (!hasMore) {
                return;
            }

            loading = true;
            setLoaderVisible(true);

            const url = new URL(cardsUrl, window.location.origin);
            url.searchParams.set('page', String(page));

            if (query !== '') {
                url.searchParams.set('q', query);
            }

            const filterForm = root.dataset.filterForm
                ? document.querySelector(root.dataset.filterForm)
                : null;

            if (filterForm) {
                new FormData(filterForm).forEach(function (value, key) {
                    url.searchParams.set(key, String(value));
                });
            }

            try {
                const response = await fetch(url.toString(), {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    credentials: 'same-origin',
                });

                if (!response.ok) {
                    throw new Error('Failed to load card feed');
                }

                const payload = await response.json();
                const html = payload.html || '';
                const meta = payload.meta || {};

                if (html.trim() !== '') {
                    grid.insertAdjacentHTML('beforeend', html);
                }

                hasMore = Boolean(meta.has_more);
                page = Number(meta.current_page || page) + 1;

                if (!hasMore) {
                    setEndVisible(hasRenderedCards());
                }
            } catch (error) {
                hasMore = false;

                if (!hasRenderedCards()) {
                    grid.innerHTML =
                        '<div class="mobile-cards-empty"><p class="mb-0 text-danger">' +
                        errorMessage +
                        '</p></div>';
                }
            } finally {
                loading = false;
                setLoaderVisible(false);
            }
        }

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        loadPage(false);
                    }
                });
            },
            {
                root: null,
                rootMargin: '240px 0px',
                threshold: 0,
            }
        );

        observer.observe(sentinel);

        searchInput?.addEventListener('input', function () {
            updateClearButton();
            window.clearTimeout(searchTimer);
            searchTimer = window.setTimeout(function () {
                query = searchInput.value.trim();
                loadPage(true);
            }, 300);
        });

        clearButton?.addEventListener('click', function () {
            if (!searchInput) {
                return;
            }

            searchInput.value = '';
            updateClearButton();
            query = '';
            loadPage(true);
            searchInput.focus();
        });

        root.addEventListener('mobile-card-list:reload', function () {
            loadPage(true);
        });

        loadPage(true);
    }

    document.querySelectorAll('[data-mobile-card-list]').forEach(initMobileCardList);
})();
