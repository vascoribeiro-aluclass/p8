
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



$(document).on('change', '.cpa_dimension_text', function () {
    ActiveFieldCPA(this);
});

$(document).on('change', '.cpafieldvalue-qty', function () {
    ActiveFieldCPA(this);
});

$(document).on('click', '.cpafieldvalue.is_visual, img.cpafieldvalue-qty.is_visual ', function () {
    var dataSrc = $(this).attr('data-src');
    if (dataSrc) {
        var srcArray = dataSrc.split(";");
        var zindex = $(this).attr('data-zindex');
        var field = $(this).attr('data-field');
        var ishasClass = $(this).hasClass('select-value');

        $('#visual_' + field).remove();
        if (!ishasClass) {
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
    }
});

$(document).on('click', 'img.cpafieldvalue-qty', function () {
    var ishasClass = $(this).hasClass('select-value');

    if (!ishasClass) {
        $(this).addClass('select-value');
        ProccessPriceCPAFieldWithoutQty(this, 1);
    } else {
        $(this).removeClass('select-value');
        ProccessPriceCPAFieldWithoutQty(this, 0);
    }

    ActiveFieldCPA(this);
});

$(document).on('click', '.cpafieldvalue ', function () {
    var field = $(this).attr('data-field');
    var idvalue = $(this).attr('data-id-value');
    var ishasClass = $(this).hasClass('select-value');

    HideCPAFieldError(field);

    $('[data-field="' + field + '"]').removeClass('select-value');
    if (!ishasClass) {
        $(this).addClass('select-value');
        MarkField(field, true);
    } else {
        MarkField(field, false);
    }

    ProccessPriceCPAFieldValue(this);
    ControlInfluences(field, idvalue);
    ActiveFieldCPA(this);
});

$(document).on('change', 'input.cpa_field_text', function () {
    var field = $(this).attr('data-field');

    var idvalue = $(this).attr('data-id-value');
    var type = $(this).attr('data-typefield');
    var qty = $(this).val();

    $('#cpafield_value_' + idvalue).val(type + '_' + field + '_' + idvalue + '_' + qty);
    if (qty != '') {
        $('#cpafield_value_' + idvalue).prop('disabled', false);
    } else {
        $('#cpafield_value_' + idvalue).prop('disabled', true);
    }
    ActiveFieldCPA(this);
});


//########################### INICIO - cpa Submit Produto ###########################
$('#submitCpafields, .submitCpafields').off('click').on('click', function (event) {
    $('#cpafields').cpaSubmit(event);
});

$.fn.cpaSubmit = function (event) {
    $('body').append('<div class="cpa-loader" id="cpaloader"><div class="sk-folding-ball"><div class="sk-ball1 sk-ball"></div><div class="sk-ball2 sk-ball"></div><div class="sk-ball4 sk-ball"></div><div class="sk-ball3 sk-ball"></div></div></div>');
    event.preventDefault();

    datacustom = Checkfields();

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
            $('#cpaloader').fadeOut().remove();

            if (tokencpa !== false && cpacustomizationfield !== false) {
                prestashop.emit('updateCart', {
                    reason: {
                        idProduct: response.data.new_id_product,
                        idCustomization: response.data.new_id_customization,
                        linkAction: 'add-to-cart'
                    },
                    resp: response
                });

            } else {

                $.post(prestashop.urls.pages.cart, {
                    ajax: true,
                    action: 'update',
                    add: 1,
                    id_product: response.data.new_id_product,
                    id_customization: response.data.new_id_customization,
                    qty: 1
                }).then(function (resp) {

                    prestashop.emit('updateCart', {
                        reason: {
                            idProduct: response.data.new_id_product,
                            idCustomization: response.data.new_id_customization,
                            linkAction: 'add-to-cart'
                        },
                        resp: resp
                    });

                });
            }

        },
        error: function (xhr) {
            $('#cpaloader').fadeOut().remove();
            console.log('Erro:', xhr);
        }
    });

};

//########################### INICIO - cpa budget Produto ###########################
$('#submitCpabudget, .submitCpabudget').off('click').on('click', function (event) {
    $('#cpafields').cpaSubmitBudget(event);
});

$.fn.cpaSubmitBudget = function (event) {
    $('body').append('<div class="cpa-loader" id="cpaloader"><div class="sk-folding-ball"><div class="sk-ball1 sk-ball"></div><div class="sk-ball2 sk-ball"></div><div class="sk-ball4 sk-ball"></div><div class="sk-ball3 sk-ball"></div></div></div>');

    event.preventDefault();

    datacustom = Checkfields();

    $.ajax({
        type: 'POST',
        url: url_ajax_cpacustomizadorprodutosaluclass,
        data: {
            ajax: true,
            action: 'ProcessCPABudget',
            datacustom: datacustom
        },
        success: function (response) {
            $('#cpaloader').fadeOut().remove();
            console.log(response.data);
            window.open(response.data, '_blank');
        },
        error: function (xhr) {
            $('#cpaloader').fadeOut().remove();
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

$(window).on("load", function () {

    if (is3dshow) {
        $(".product-cover").prepend('<div style="position: absolute;zindex: 998;z-index: 998; right: 5px;top: 5px;"  class="btn btn-primary" onclick="show3D()">Ver 3D</div>');
    }

    $(".form-group[data-position='1']").removeClass("cpa-disable-div");

    if (GetSelectProgressBar() == 0) {
        $("#productprogressbarfluid").hide();
    }

    $(".cpaFieldItem").each(function () {
        var dataInfluences = $(this).attr('data-influences');
        var id_cpa_customization_field_value_show = 0;
        if (dataInfluences != '') {
            var influencesobj = JSON.parse(dataInfluences);
            influencesobj.forEach(function (item) {
                if (id_cpa_customization_field_value_show == 0) {
                    id_cpa_customization_field_value_show = item.id_cpa_customization_field_value_show;
                    $('.disabled_value_by_' + item.id_cpa_customization_field_value_show).removeClass('disabled_value_by_' + item.id_cpa_customization_field_value_show);
                } else if (id_cpa_customization_field_value_show == item.id_cpa_customization_field_value_show) {
                    $('.disabled_value_by_' + item.id_cpa_customization_field_value_show).removeClass('disabled_value_by_' + item.id_cpa_customization_field_value_show);
                }
            });
        }
    });


    if (
        navigator.userAgent.match(
            /Mobile|Windows Phone|Lumia|Android|webOS|iPhone|iPod|Blackberry|PlayBook|BB10|Opera Mini|\bCrMo\/|Opera Mobi/i
        )
    ) {
        $(window).scroll(function () {
            if ($("#productprogressbarfluid").length == 1) {
                var pos1pro = $(window).scrollTop();
                var pos2pro = $("#productprogressbarfluid").offset().top;
                var pos3pro = $("#submitCpafields").offset().top;

                if (pos1pro + 37 >= pos2pro + 37) {
                    if (pos1pro + 150 < pos3pro) {
                        widthcpa = $("#cpafields").width();
                        $("#productprogressbar").addClass("progress-scroll");
                        $("#productprogressbar").show();
                        $("#productprogressbar").css({ width: widthcpa + "px" });
                    } else $("#productprogressbar").hide();
                } else {
                    $("#productprogressbar").removeClass("progress-scroll");
                }
            }
        });
    } else {
        $(window).scroll(function () {
            if ($("#productprogressbarfluid").length == 1) {
                var pos1pro = $(window).scrollTop();
                var pos2pro = $("#productprogressbarfluid").offset().top;
                var pos3pro = $("#submitCpafields").offset().top;

                if (pos1pro + 37 >= pos2pro + 37) {
                    if (pos1pro + 150 < pos3pro) {
                        widthcpa = $("#cpafields").width();
                        $("#productprogressbar").addClass("progress-scroll");
                        $("#productprogressbar").show();
                        $("#productprogressbar").css({ width: widthcpa + "px" });
                    } else $("#productprogressbar").hide();
                } else {
                    $("#productprogressbar").removeClass("progress-scroll");
                }
            }
        });
    }

    if (cpacustomizationfield !== false) {
        $('body').append('<div class="cpa-loader" id="cpaloader"><div class="sk-folding-ball"><div class="sk-ball1 sk-ball"></div><div class="sk-ball2 sk-ball"></div><div class="sk-ball4 sk-ball"></div><div class="sk-ball3 sk-ball"></div></div></div>');
        console.log(typeof cpacustomizationfield);
        data = JSON.parse(cpacustomizationfield);
        data.forEach(function (item, index) {
            setTimeout(function () {

                let result = item.value.split("_");

                let type = result[0];
                let field = result[1];
                let idvalue = result[2];
                let qty = result[3];

                switch (type) {
                    case '1':
                        $('input[data-id-value="' + idvalue + '"]').val(qty);
                        $('input[data-id-value="' + idvalue + '"]').trigger('change');
                        break;
                    case '2':
                        $('img[data-id-value="' + idvalue + '"]').trigger('click');
                        break;
                    case '3':
                        $('input[data-id-value="' + idvalue + '"]').trigger('click');
                        break;
                    case '4':
                        $('img[data-id-value="' + idvalue + '"]').trigger('click');
                        break;
                    case '5':
                        $('input[data-id-value="' + idvalue + '"]').val(qty);
                        $('input[data-id-value="' + idvalue + '"]').trigger('change');
                        break;
                    case '6':
                        $('input[data-id-value="' + idvalue + '"]').val(qty);
                        $('input[data-id-value="' + idvalue + '"]').trigger('change');
                        break;
                    case '7':
                        $('select[data-id-value="' + idvalue + '"]').val(qty);
                        $('select[data-id-value="' + idvalue + '"]').trigger('change');
                        break;
                }

                if (index === data.length - 1) {
                    $('#cpaloader').fadeOut(function () {
                        $(this).remove();
                    });
                }
            }, index * 350);
        });

    }

});