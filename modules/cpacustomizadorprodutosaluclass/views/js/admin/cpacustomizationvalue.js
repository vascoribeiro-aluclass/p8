$(document).ready(function() {
   
    manageVisibility(id_cpa_customization_field_type);
    setupFileValidation('icon_file', ['image/jpeg'],'.jpg, .jpeg',icon_file_text_error);
    setupFileValidation('img_file', ['image/jpeg', 'image/png'],'.png, .jpg, .jpeg',img_file_text_error);
    setupFileValidation('preview_file', ['image/jpeg'],'.jpg, .jpeg',preview_file_text_error);

    if (typeof already_selected_exc_products !== 'undefined' && already_selected_exc_products.length) {

        var ids = $.map(already_selected_exc_products, function (item) {
            return item.id;
        });

        $('.ajax-exc-product-search').val(ids.join(','));
    }

    $('.float-field').on('input', function () {
        this.value = this.value
            .replace(/[^0-9.]/g, '')
            .replace(/(\..*?)\..*/g, '$1')
            .replace(/^(\d+)(\.\d{0,2})?.*$/, '$1$2');
    });

    $('.integer-field').on('input', function () {
        this.value = this.value.replace(/[^0-9]/g, ''); // permite só dígitos
    });

    $('.ajax-exc-product-search').select2({
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
            url: ajaxExcProductUrl,
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
            if (typeof already_selected_exc_products !== 'undefined') {
                callback(already_selected_exc_products);
            }
        }
    });

});
