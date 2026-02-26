console.log(already_selected_products);

$(document).ready(function () {

    if (typeof already_selected_products !== 'undefined' && already_selected_products.length) {

        var ids = $.map(already_selected_products, function (item) {
            return item.id;
        });

        $('.ajax-product-search').val(ids.join(','));
    }

    if (typeof already_selected_fields_influence !== 'undefined' && already_selected_fields_influence.length) {

        var ids = $.map(already_selected_fields_influence, function (item) {
            return item.id;
        });

        $('.ajax-cpa-fields-search').val(ids.join(','));
    }

    $('.integer-field').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, ''); // permite só dígitos
    });

    $('.ajax-cpa-fields-search').select2({
        width: '100%',
        multiple: true,
        placeholder: select2_translations.searchingProducts,
        minimumInputLength: 3,
        formatInputTooShort: function (input, min) {
            return select2_translations.inputTooShort.replace('%d', min);
        },

        formatNoMatches: function () {
            return select2_translations.noMatches;
        },

        formatSearching: function () {
            return select2_translations.searching;
        },

        ajax: {
            url: ajaxFieldsUrl,
            dataType: 'json',
            quietMillis: 250,
            data: function (term) {
                return {
                    q: term
                };
            },
            results: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id_cpa_customization_field,
                            text: item.admin_name 
                        };
                    })
                };
            }
        },
        initSelection: function (element, callback) {
            if (typeof already_selected_fields_influence !== 'undefined') {
                callback(already_selected_fields_influence);
            }
        }
    });

    $('.ajax-product-search').select2({
        width: '100%',
        multiple: true,
        placeholder: select2_translations.searchingProducts,
        minimumInputLength: 3,
        formatInputTooShort: function (input, min) {
            return select2_translations.inputTooShort.replace('%d', min);
        },

        formatNoMatches: function () {
            return select2_translations.noMatches;
        },

        formatSearching: function () {
            return select2_translations.searching;
        },

        ajax: {
            url: ajaxProductUrl,
            dataType: 'json',
            quietMillis: 250,
            data: function (term) {
                return {
                    q: term
                };
            },
            results: function (data) {
                return {
                    results: $.map(data, function (item) {
                        return {
                            id: item.id_product,
                            text: item.name + ' (Ref: ' + item.reference + ')'
                        };
                    })
                };
            }
        },
        initSelection: function (element, callback) {
            if (typeof already_selected_products !== 'undefined') {
                callback(already_selected_products);
            }
        }
    });

});
