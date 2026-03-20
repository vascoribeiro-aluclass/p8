// function ActiveFieldNDK(obj) {
//   var position = $(obj).closest('[data-position]');
//   next_position = position.data('position') + 1;
//   $(".form-group[data-position='" + next_position + "']").removeClass("aluclass-disable-div");
//   var iteration = $(obj).closest('[data-iteration]');
//   next_iteration = iteration.data('iteration') + 1;
//   $(".form-group[data-iteration='" + next_iteration + "']").removeClass("aluclass-disable-div");
//   var rposition = $(obj).closest('[data-rposition]');
//   next_rposition = rposition.data('rposition') + 1;
//   $(".form-group[data-rposition='" + next_rposition + "']").removeClass("aluclass-disable-div");
// }

//########################### INICIO - mensagem de erro ###########################
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
//########################### FIM - mensagem de erro ###########################

//########################### INICIO - Abrir e campo ###########################
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
//########################### FIM - Abrir e campo ###########################

//########################### INICIO - Preview Imagem ###########################

$(".cpafieldvalue-preview-img").on("mouseenter", function (e) {
    const srcview = $(this).attr("data-src-view");
    var srcviewArray = srcview.split(";");
    var idvalue = $(this).attr('data-id-value');

    var newHtml = '<picture>';
    if (srcviewArray.length > 1) {
        newHtml += '<source src="' + srcviewArray[1] + '">';
    }
    newHtml += '<img src="' + srcviewArray[0] + '">';
    newHtml += '</picture>';

    $("#tooltipPreview_" + idvalue)
        .html(newHtml)
        .css({
            top: e.offsetY + 20,
            left: e.offsetX + 20
        })
        .stop(true, true)
        .fadeIn(200);
});

$(".cpafieldvalue-preview-img").on("mousemove", function (e) {
    var idvalue = $(this).attr('data-id-value');

    $("#tooltipPreview_" + idvalue).css({
        top: e.offsetY + 20,
        left: e.offsetX + 20
    });
});

$(".cpafieldvalue-preview-img").on("mouseleave", function () {
    var idvalue = $(this).attr('data-id-value');
    $("#tooltipPreview_" + idvalue).stop(true, true)
        .fadeOut(200);
});

//########################### FIM - Preview Imagem ###########################

$(document).on('click', '.cpafieldvalue', function () {
    var field = $(this).attr('data-field');
    HideCPAFieldError(field);

    $('[data-field="' + field + '"]').removeClass('select-value');

    $(this).addClass('select-value');
});

$(document).on('click', '.cpafieldvalue.is_visual', function () {
      var dataSrc = $(this).attr('data-src');
    if (dataSrc) {
        var srcArray = dataSrc.split(";");
        var zindex = $(this).attr('data-zindex');
        var field = $(this).attr('data-field');

        $('#visual_' + field).remove();

        var newHtml = '<picture data-group="' + field + '"' +
            'data-zindex="' + zindex + '"' +
            'id="visual_' + field + '"' +
            'class=" group-' + field + ' ">';
        if (srcArray.length > 1) {
            newHtml += '<source src="' + srcArray[1] + '">';
        }

        newHtml += '<img class="absolute-visu  absolute-img " ' + 'style="z-index:' + zindex + ' ; "' + ' src="' + srcArray[0] + '">';

        newHtml += '</picture>';
        $('.js-qv-product-cover').closest('.product-cover').append(newHtml);
    }
});

//########################### INICIO - cpa Submit Produto ###########################
$('#submitCpafields, .submitCpafields').off('click').on('click', function (event) {
    $('#cpafields').cpaSubmit(event);
});

$.fn.cpaSubmit = function (event) {
    $('body').append('<div class="cpa-loader" id="cpaloader"><div class="sk-folding-ball"><div class="sk-ball1 sk-ball"></div><div class="sk-ball2 sk-ball"></div><div class="sk-ball4 sk-ball"></div><div class="sk-ball3 sk-ball"></div></div></div>');
    event.preventDefault();
    var required = $(".form-group.cpaFieldItem:not([class*='disabled_value_by'])").find('.required_field');
    var hasError = false;
    required.each(function () {
        switch ($(this).attr('data-typefield')) {
            case '1':
                if ($(this).find('.select-value').length < 3) {
                    ShowCPAFieldError($(this).attr('data-field'), "Error");
                    hasError = true;
                    return false;
                }
            case '2':
                if ($(this).find('.select-value').length == 0) {
                    ShowCPAFieldError($(this).attr('data-field'), "Error");
                    hasError = true;
                    return false;
                }
                break;
        }

    });

    if (hasError) {
        $('#cpaloader').fadeOut().remove();
        return false;
    }


    var dataArray = $(".fromset").serializeArray();

    var datacustom = {};

    datacustom['cpafields'] = {};
    dataArray.forEach(function (item) {
        datacustom['cpafields'][item.name] = item.value;
    });


    datacustom['id_product'] = $('#cpafields-block').attr('data-key');

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

//########################### FIM - cpa Submit Produto ###########################