


$(document).on('change', '.cpa_dimension_text', function () {
    var field = $(this).attr('data-field');
    var idvalue = $(this).attr('data-id-value');
    var type = $(this).attr('data-typefield');
    var qty = $(this).val();

    var min = parseInt($(this).attr('min'));
    var max = parseInt($(this).attr('max'));

    if (qty >= min && qty <= max) {
        $('#cpafield_value_' + idvalue).addClass('select-value');
        $('#cpafield_value_' + idvalue).val(type + '_' + field + '_' + idvalue + '_' + qty);
        $('#cpafield_value_' + idvalue).prop('disabled', false);
        $(this).removeClass('alert-danger-cpa');
        $('#error-dimension-' + idvalue).hide();
        HideCPAFieldError(field);

    } else {
        $('#cpafield_value_' + idvalue).removeClass('select-value');;
        $('#cpafield_value_' + idvalue).prop('disabled', true);
        $('#error-dimension-' + idvalue).show();
        $(this).addClass('alert-danger-cpa');
    }

    getCustomerPricedimensions(this);
    ProgressBar();
});


$(document).on('change', 'input.cpafieldvalue-qty', function () {
    var field = $(this).attr('data-field');
    var price = $(this).attr('data-price');
    var idvalue = $(this).attr('data-id-value');
    var type = $(this).attr('data-typefield');
    var qty = $(this).val();

    $('#cpafield_value_' + idvalue).val(type + '_' + field + '_' + idvalue + '_' + qty);
    $('#cpafield_value_' + idvalue).attr('data-price', parseFloat(price) * parseInt(qty));
    if (parseInt(qty) > 0) {
        $('#cpafield_value_' + idvalue).prop('disabled', false);
        addCustomerPrice();
    } else {
        addCustomerPrice();
        $('#cpafield_value_' + idvalue).prop('disabled', true);
    }

});

$(document).on('click', 'button.cpafieldvalue-qty-up', function () {
    var idvalue = $(this).attr('data-id-value');
    var qty = $("#cpafieldvalue-qty-" + idvalue).val();
    $("#cpafieldvalue-qty-" + idvalue).val(parseInt(qty) + 1);

    $("#cpafieldvalue-qty-" + idvalue).trigger('change');
});

$(document).on('click', 'button.cpafieldvalue-qty-down', function () {
    var idvalue = $(this).attr('data-id-value');
    var qty = $("#cpafieldvalue-qty-" + idvalue).val();
    var qty = parseInt(qty) - 1;
    if (qty > 0) {
        $("#cpafieldvalue-qty-" + idvalue).val(qty);
    } else {
        $("#cpafieldvalue-qty-" + idvalue).val(0);
    }

    $("#cpafieldvalue-qty-" + idvalue).trigger('change');

});