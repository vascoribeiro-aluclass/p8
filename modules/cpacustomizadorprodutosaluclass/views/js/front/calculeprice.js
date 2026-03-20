function addCustomerPrice() {
    pricebase = $('#cpafromPrice').attr('data-price-base');
    var pricecustomize = 0;
    var price = 0;


    $(".pricecal").each(function () {
        if (!$(this).is(":disabled")) {
            if($(this).attr("data-price-type") == 'amount'){
                pricecustomize = pricecustomize + parseFloat($(this).attr("data-price"));
            }else{
                pricecustomize = pricecustomize + ( (parseFloat($(this).attr("data-price"))/100)*parseFloat(pricebase) );
            }
            
        }
    });

    price = parseFloat(pricebase) + parseFloat(pricecustomize);

    price_with_iva = price + (price * ivaProduct / 100);

    const formatted = new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(price_with_iva);

    $('#cpafromPrice').html(formatted);
}

function getCustomerPricedimensions(element) {

    var field = $(element).attr('data-field');

    var minWidth  = parseInt($('input[data-field="' + field + '"].dimension_text_width').attr('min'));
    var maxWidth  = parseInt($('input[data-field="' + field + '"].dimension_text_width').attr('max'));
    var minHeight = parseInt($('input[data-field="' + field + '"].dimension_text_height').attr('min'));
    var maxHeight = parseInt($('input[data-field="' + field + '"].dimension_text_height').attr('max'));
    var minDepth  = parseInt($('input[data-field="' + field + '"].dimension_text_depth').attr('min'));
    var maxDepth  = parseInt($('input[data-field="' + field + '"].dimension_text_depth').attr('max'));

    var valWidth  = parseInt($('input[data-field="' + field + '"].dimension_text_width').val())  || 0;
    var valHeight = parseInt($('input[data-field="' + field + '"].dimension_text_height').val()) || 0;
    var valDepth  = parseInt($('input[data-field="' + field + '"].dimension_text_depth').val())  || 0;

    if (valWidth >= minWidth && valWidth <= maxWidth && valHeight >= minHeight && valHeight <= maxHeight && valDepth >= minDepth && valDepth <= maxDepth) {
        var datadimensions = {
            width: valWidth,
            height: valHeight,
            depth: valDepth
        };
        $.ajax({
            type: 'POST',
            url: url_ajax_cpacustomizadorprodutosaluclass,
            data: {
                ajax: true,
                action: 'ProcessCPADimensions',
                dimensions: datadimensions
            },
            success: function (response) {

                $('#cpafield_' + field).prop('disabled', false);
                $('#cpafield_' + field).attr('data-price', response.data);
                addCustomerPrice();
            },
            error: function (xhr) {
                $('#cpafield_' + field).attr('data-price', 0);
                addCustomerPrice();
                $('#cpafield_' + field).prop('disabled', true);
            }
        });
    } else {
        $('#cpafield_' + field).attr('data-price', 0);
        addCustomerPrice();
        $('#cpafield_' + field).prop('disabled', true);
    }


}

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
        HideCPAFieldError(field);

    } else {
        $('#cpafield_value_' + idvalue).removeClass('select-value');;
        $('#cpafield_value_' + idvalue).prop('disabled', true);
    }

    getCustomerPricedimensions(this);
});

$(document).on('click', '.cpafieldvalue', function () {
    var field = $(this).attr('data-field');
    var price = $(this).attr('data-price');
    var idvalue = $(this).attr('data-id-value');
    var qty = $(this).attr('data-qty');
    var type = $(this).attr('data-typefield');

    $('#cpafield_' + field).val(type + '_' + field + '_' + idvalue + '_' + qty);
    $('#cpafield_' + field).attr('data-price', price);

    $('#cpafield_' + field).prop('disabled', false);
    addCustomerPrice();
});

$(document).on('change', '.cpafieldvalue-qty', function () {
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

$(document).on('click', '.cpafieldvalue-qty-up', function () {
    var idvalue = $(this).attr('data-id-value');
    var qty = $("#cpafieldvalue-qty-" + idvalue).val();
    $("#cpafieldvalue-qty-" + idvalue).val(parseInt(qty) + 1);

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