@props([
    'url',
    'searchPlaceholder' => 'Search...',
    'endMessage' => "You're all caught up",
    'errorMessage' => 'Could not load results. Please try again.',
])

{{--
    Reusable mobile infinite-scroll card list.
    Feed endpoint must return JSON: { html, meta: { current_page, last_page, has_more, total } }
    Card HTML partials should use classes: mobile-card, mobile-card-grid spanning empty via mobile-cards-empty.
--}}
<section
    {{ $attributes->class(['mobile-card-list', 'd-lg-none']) }}
    data-mobile-card-list
    data-cards-url="{{ $url }}"
    data-error-message="{{ $errorMessage }}"
>
    <div class="mobile-search-bar">
        <i class="bi bi-search" aria-hidden="true"></i>
        <input
            type="search"
            class="mobile-search-input"
            data-mobile-card-search
            placeholder="{{ $searchPlaceholder }}"
            autocomplete="off"
            enterkeyhint="search"
        >
        <button
            type="button"
            class="mobile-search-clear d-none"
            data-mobile-card-clear
            aria-label="Clear search"
        >
            <i class="bi bi-x-lg" aria-hidden="true"></i>
        </button>
    </div>

    <div class="mobile-card-grid" data-mobile-card-grid aria-live="polite"></div>

    <div class="mobile-card-status" aria-live="polite">
        <div class="mobile-card-loader d-none" data-mobile-card-loader>
            <span class="mobile-card-spinner" aria-hidden="true"></span>
            <span>Loading…</span>
        </div>
        <p class="mobile-card-end d-none mb-0" data-mobile-card-end>{{ $endMessage }}</p>
    </div>

    <div class="mobile-card-sentinel" data-mobile-card-sentinel aria-hidden="true"></div>
</section>

@pushOnce('scripts')
    <script src="{{ asset('js/mobile-card-list.js') }}?v={{ filemtime(public_path('js/mobile-card-list.js')) }}" defer></script>
@endpushOnce
