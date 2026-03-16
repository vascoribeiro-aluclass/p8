// Mostra erro no próprio campo NDK [idfield = id do campo CPA; message = messagem a ser mostada]
function ShowCPAFieldError(idfield, message) {

    $(".form-group[data-field='" + idfield + "']").css('background', '#F2DEDE').focus();
    $("#error-" + idfield).html(message);
    $("#error-" + idfield).show();
    if (!$(".form-group[data-field='" + idfield + "'] > .toggler").hasClass('active')) {
        $(".form-group[data-field='" + idfield + "'] > .toggler").trigger('click');
    }
}

// Remove erro no próprio campo NDK [idfield = id do campo CPA]
function HideCPAFieldError(idfield) {
    $(".form-group[data-field='" + idfield + "']").css('background', '#ffffff').focus();
    $("#error-" + idfield).html('');
    $("#error-" + idfield).hide();
}


$('[data-toggle="tooltip"]').tooltip();

$(document).on('click', '.toggler', function () {
    toggler = $(this);
    thisFieldPane = $(this).parent().find('.fieldPane');
    $('.fieldPane:visible').not(thisFieldPane).hide();
    $.when($(this).parent().find('.fieldPane').toggle()).then(function () {
        if ($(this).parent().find('.fieldPane').is(':visible')) {
            toggler.addClass('active');
            toggler.find('.toggleText').html(toggleCloseText);
        }
        else {
            toggler.removeClass('active');
            toggler.find('.toggleText').html(toggleOpenText);
        }
    });
});

$(document).on('click', '.cpafieldvalue', function () {
    var field = $(this).attr('data-field');
    HideCPAFieldError(field);

    $('[data-field="' + field + '"]').removeClass('select-value');
    $(this).addClass('select-value');
});



$(document).on('click', '.cpafieldvalue.is_visual', function () {
    var src = $(this).attr('data-src');
    var zindex = $(this).attr('data-zindex');
    var field = $(this).attr('data-field');

    $('#visual_' + field).remove();

    var newHtml = '<picture data-group="' + field + '"' +
        'data-zindex="' + zindex + '"' +
        'id="visual_' + field + '"' +
        'class=" group-' + field + ' ">' +
        '<img class="absolute-visu  absolute-img " ' + 'style="z-index:' + zindex + ' ; "' + ' src="' + src + '">' +
        '</picture>';
    $('.js-qv-product-cover').closest('.product-cover').append(newHtml);

});


$('#submitCpafields, .submitCpafields').off('click').on('click', function (event) {
    $('#cpafields').cpaSubmit(event);
});

$.fn.cpaSubmit = function (event) {
    $('body').append('<div class="cpa-loader" id="cpaloader"><div class="sk-folding-ball"><div class="sk-ball1 sk-ball"></div><div class="sk-ball2 sk-ball"></div><div class="sk-ball4 sk-ball"></div><div class="sk-ball3 sk-ball"></div></div></div>');
    event.preventDefault();
    var required = $(".form-group.cpaFieldItem:not([class*='disabled_value_by'])").find('.required_field');

    required.each(function () {
        switch ($(this).attr('data-typefield')) {
            case '2':
                if ($(this).find('.select-value').length == 0) {
                    ShowCPAFieldError($(this).attr('data-field'), "Error");
                    return false;
                }
            case '2':
                if ($(this).find('.select-value').length == 0) {
                    ShowCPAFieldError($(this).attr('data-field'), "Error");
                    return false;
                }
                break;
        }

    });



    var dataArray = $(".fromset").serializeArray();

    var datacustom = {};

    datacustom['cpafields'] = {};
    dataArray.forEach(function (item) {
        datacustom['cpafields'][item.name] = item.value;
    });


    datacustom['id_product'] = $('#cpafields-block').attr('data-key');

    // console.log(datacustom);
    //$('#cpaloader').fadeOut().remove();
    // return;

    $.ajax({
        type: 'POST',
        url: url_ajax_cpacustomizadorprodutosaluclass,
        data: {
            ajax: true,
            action: 'ProcessCPAProduct',
            datacustom: datacustom
        },
        success: function (response) {
            if (!response.data) {
                console.log('Produto inválido, não foi adicionado ao carrinho.');
                return;
            }
            $.post(prestashop.urls.pages.cart, {
                ajax: true,
                action: 'update',
                add: 1,
                id_product: response.data,
                qty: 1
            }).then(function (resp) {
                $('#cpaloader').fadeOut().remove();
                prestashop.emit('updateCart', {
                    reason: {
                        idProduct: response.data,
                        linkAction: 'add-to-cart'
                    },
                    resp: resp
                });

            });
        },
        error: function (xhr) {
            console.log('Erro:', xhr);
        }
    });

};

$(document).on("click", "[data-dismiss='modal']", function () {
    location.reload();
});
$(document).on('hidden.bs.modal', '#blockcart-modal', function () {
    location.reload();
})