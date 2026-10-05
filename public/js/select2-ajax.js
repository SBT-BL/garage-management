/**
 * Select2 AJAX dropdowns.
 * The server returns a raw array of {id, text}. Search is sent as search.value.
 */
window.Helpers = window.Helpers || {};

Helpers.ajaxSelect2 = function (element) {
    const $el = $(element);

    if ($el.hasClass('select2-hidden-accessible')) {
        return;
    }

    const url = $el.attr('data-url');
    const placeholder = $el.attr('data-placeholder') || 'Search';

    $el.select2({
        ajax: {
            url: url,
            dataType: 'json',
            delay: 250,
            cache: false,
            data: function (params) {
                const extra = {};
                const dependsOn = $el.attr('data-depends-on');
                const dependsParam = $el.attr('data-depends-param');

                if (dependsOn && dependsParam) {
                    extra[dependsParam] = $('#' + dependsOn).val() || '';
                }

                return {
                    search: { value: params.term || '' },
                    page: params.page || 1,
                    ...extra,
                };
            },
            processResults: function (data) {
                return {
                    results: Array.isArray(data) ? data : [],
                };
            },
        },
        allowClear: true,
        width: '100%',
        theme: 'bootstrap-5',
        placeholder: placeholder,
        dropdownParent: $(document.body),
        templateSelection: function (data) {
            if (data && data.element && data.data_attributes) {
                Object.keys(data.data_attributes).forEach(function (key) {
                    data.element.setAttribute(key, data.data_attributes[key]);
                });
            }

            return data.text || '';
        },
    });
};

$(function () {
    document.querySelectorAll('.init_select_dynamic').forEach(function (element) {
        Helpers.ajaxSelect2(element);
    });
});
