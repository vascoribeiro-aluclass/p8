

$(document).on('click', '.cpafieldvalue', function () {
    var field = $(this).attr('data-field');
    var price = $(this).attr('data-price');
    var idvalue = $(this).attr('data-id-value');
    var qty = $(this).attr('data-qty');

    $('#cpafield_' + field).val(field + '_' + idvalue + '_' + qty);
    $('#cpafield_' + field).attr('data-price', price);
    $('#cpafield_' + field).prop('disabled', false);
});

$(document).on('change', '.cpafieldvalue-qty', function () {
    var field = $(this).attr('data-field');
    var price = $(this).attr('data-price');
    var idvalue = $(this).attr('data-id-value');
    var qty = $(this).val();

    $('#cpafield_value_' + idvalue).val(field + '_' + idvalue + '_' + qty);
    $('#cpafield_value_' + idvalue).attr('data-price', price);
    if (parseInt(qty) > 0) {
        $('#cpafield_value_' + idvalue).prop('disabled', false);
    } else {
        $('#cpafield_value_' + idvalue).prop('disabled', true);
    }

});

$(document).on('click', '.cpafieldvalue-qty-up', function () {
    var idvalue = $(this).attr('data-id-value');
    var qty = $("#cpafieldvalue-qty-" + idvalue).val();
    $("#cpafieldvalue-qty-" + idvalue).val(parseInt(qty) + 1);
    console.log('asdasd');
    console.log(idvalue);
    $("#cpafieldvalue-qty-" + idvalue).trigger('change');
});

$(document).on('click', '.cpafieldvalue-qty-down', function () {
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