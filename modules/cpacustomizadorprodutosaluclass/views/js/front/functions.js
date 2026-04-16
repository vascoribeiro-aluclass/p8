//########################### INICIO - Função de control de visualidade ###########################
function ActiveFieldCPA(obj) {
    var position = $(obj).closest('[data-position]');
    next_position = position.data('position') + 1;
    $(".form-group[data-position='" + next_position + "']").removeClass("cpa-disable-div");
    var iteration = $(obj).closest('[data-orderposition]');
    next_iteration = iteration.data('orderposition') + 1;
    $(".form-group[data-orderposition='" + next_iteration + "']").removeClass("cpa-disable-div");
}
//########################### FIM - Função de control de visualidade ###########################

function GetRequiredProgressBar() {
    var requiredcount = 0;
    $("input.fromset.required_field").each(function () {
        if (!$(this).is(":disabled")) {
            requiredcount++;
        }
    });

    return requiredcount;
}

function GetSelectProgressBar() {
    var selectcount = 0;
    $("input.fromset.required_field").each(function () {
        selectcount++;
    });

    return selectcount;
}

function MarkField(field, isMark) {
    var isRequeired = $('#main-' + field).hasClass('required_field');

    if (isRequeired) {
        if (isMark) {
            $('#progress-field-cpa-' + field).html('<span class="material-icons progress-field-success"> done </span>');
        } else {
            $('#progress-field-cpa-' + field).html('');
        }
    }
}

function Checkfields() {
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
                break;
            case '2':
                if ($(this).find('.select-value').length == 0) {
                    ShowCPAFieldError($(this).attr('data-field'), "Error");
                    hasError = true;
                    return false;
                }
                break;
            case '3':
                if ($(this).find('.select-value').length == 0) {
                    ShowCPAFieldError($(this).attr('data-field'), "Error");
                    hasError = true;
                    return false;
                }
                break;
            case '7':
                if ($(this).find('.select-value').length < 3) {
                    ShowCPAFieldError($(this).attr('data-field'), "Error");
                    hasError = true;
                    return false;
                }
                break;
        }

    });

    $('.alert-danger-cpa').each(function () {
        hasError = true;
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

    if (tokencpa !== false && cpacustomizationfield !== false) {
        datacustom['tokencpa'] = tokencpa;
    }


    datacustom['id_product'] = $('#cpafields-block').attr('data-key');
    return datacustom;
}

function show3D() {
    var productCover = $(".product-cover");
    var container;
    let exists = $(".3dshow").length > 0;

    if (!exists) {
        console.log("Criando div #3dshow...");
        var container = $("<div>", {
            id: "3dshow",
            class: "3dshow",
            css: {
                width: "100%",
                height: "100%",
                backgroundColor: "red",
                position: "absolute",
                zIndex: 998
            }
        });
        // Cria o botão dentro do container
        var btn3D = $("<div>", {
            class: "btn btn-primary",
            text: "Mudar ambiente",
            css: {
                position: "absolute",
                left: "5px",
                top: "5px",
                zIndex: 999 // botão acima do container
            },
            click: function () {
                changeBackground(); // chama a função ao clicar
            }
        });

        // Adiciona o botão dentro do container
        container.append(btn3D);
        productCover.prepend(container);

        let colordefaut = $('.img-value.is_visual.treed.select-value').data('color');
        if (!colordefaut) {
            colordefaut = '#383E42';
        }

        init(colordefaut);
        animate();

        $('.img-value.is_visual.treed').off('click').on('click', function () {
            var color = $(this).data('color');
            if (!color) {
                color = '#383E42';
            } else {
                toggleMaterial(color);
            }
        });

        $('.cpa_dimension_text').off('change').on('change', function () {
            var idfield = $(this).data('field');
            var widthMin = parseInt($('.cpa_dimension_text[data-field="' + idfield + '"].dimension_text_width').attr('min'));
            var heightMin = parseInt($('.cpa_dimension_text[data-field="' + idfield + '"].dimension_text_height').attr('min'));
            var depthMin = parseInt($('.cpa_dimension_text[data-field="' + idfield + '"].dimension_text_depth').attr('min'));
            var widthMax = parseInt($('.cpa_dimension_text[data-field="' + idfield + '"].dimension_text_width').attr('max'));
            var heightMax = parseInt($('.cpa_dimension_text[data-field="' + idfield + '"].dimension_text_height').attr('max'));
            var depthMax = parseInt($('.cpa_dimension_text[data-field="' + idfield + '"].dimension_text_depth').attr('max'));
            var width = parseInt($('.cpa_dimension_text[data-field="' + idfield + '"].dimension_text_width').val()) || widthMin;
            var height = parseInt($('.cpa_dimension_text[data-field="' + idfield + '"].dimension_text_height').val()) || heightMin;
            var depth = parseInt($('.cpa_dimension_text[data-field="' + idfield + '"].dimension_text_depth').val()) || depthMin;

            toggleSize(width, widthMin, widthMax, height, heightMin, heightMax, depth, depthMin, depthMax);
        });


    } else {
        $(".3dshow").remove();
        $('.img-value.is_visual.treed').off('click');
        $('.cpa_dimension_text').off('change');
    }

}

//  progressBar
function ProgressBar() {
    var requiredcount = 0;
    var selectcount = 0;

    requiredcount = GetRequiredProgressBar();
    selectcount = GetSelectProgressBar();

    if (selectcount > 0) {
        $("#productprogressbarfluid").show();
    } else {
        $("#productprogressbarfluid").hide();
    }

    progress = (requiredcount / selectcount) * 100;

    if (parseInt(progress) >= 0 && parseInt(progress) < 20) {
        color = "#E22128";
    } else if (parseInt(progress) >= 20 && parseInt(progress) < 40) {
        color = "#e25821e7";
    } else if (parseInt(progress) >= 40 && parseInt(progress) < 60) {
        color = "#a78528";
    } else if (parseInt(progress) >= 60 && parseInt(progress) < 80) {
        color = "#d6e232";
    } else if (parseInt(progress) >= 80 && parseInt(progress) < 99) {
        color = "#81a728";
    } else {
        color = "#28a745";
    }

    if (progress > 0) {
        $('.progress-text-begin').hide();
    } else {
        $('.progress-text-begin').show();
    }

    $(".progress-bar").css({ 'background-color': color });
    $(".progress-bar").css({ 'width': parseInt(progress) + '%' });
    if (parseInt(progress) > 40)
        $(".progress-text").text(text_progress + ' : ' + parseInt(progress) + '%');
    else
        $(".progress-text").text('' + parseInt(progress) + '%');

}

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

//########################### INICIO - Influencias ###########################
function ControlInfluences(field, idvalue) {
    var influences = $('.form-group[data-field="' + field + '"]').attr('data-influences');
    if (influences != '') {
        var influencesobj = JSON.parse(influences);
        influencesobj.forEach(function (item) {
            $('#visual_' + item.id_cpa_customization_field_influence).remove();
            $('#cpafield_' + item.id_cpa_customization_field_influence).prop('disabled', true);
            $('.cpafieldvalue[data-field="' + item.id_cpa_customization_field_influence + '"]').removeClass('select-value');
            $('.cpafieldvalue-qty[data-field="' + item.id_cpa_customization_field_influence + '"]').val(0);
             $('.cpafieldvalue-qty[data-field="' + item.id_cpa_customization_field_influence + '"]').trigger('change');
            $('.form-group[data-field="' + item.id_cpa_customization_field_influence + '"]').addClass('disabled_value_by_' + item.id_cpa_customization_field_value_show);
        });
        $('.disabled_value_by_' + idvalue).removeClass('disabled_value_by_' + idvalue);
    }
}
//########################### FIM - - Influencias ###########################

function PriceFormat(price) {

    price_with_iva = price + (price * ivaProduct / 100);
    return new Intl.NumberFormat('pt-PT', {
        style: 'currency',
        currency: 'EUR'
    }).format(price_with_iva);
}

function addCustomerPrice() {
    pricebase = $('#cpafromPrice').attr('data-price-base');
    var pricecustomize = 0;
    var price = 0;
    var influencesPercentage = '';
    var pricefields = 0;

    $(".pricecal").each(function () {
        influencesPercentage = '';

        if (!$(this).is(":disabled")) {
            pricefields = parseFloat($(this).attr("data-price"));

            if ($(this).attr("data-price-type") == 'amount') {
                pricecustomize = pricecustomize + pricefields;
            } else {
                pricecustomize = pricecustomize + ((pricefields / 100) * parseFloat(pricebase));
            }

            influencesPercentage = $(this).attr("data-influences-percentage");
            if (!influencesPercentage == '') {
                var arrayinfluencesPercentage = influencesPercentage.split(";");
                var priceprecentage = 0;
                arrayinfluencesPercentage.forEach(function (item, index) {
                    priceprecentage = $('#cpafield_' + item).attr("data-price");
                    if (priceprecentage !== undefined && priceprecentage !== null && priceprecentage !== '') {
                        var newpricecustomize = ((parseFloat(priceprecentage) / 100) * pricefields);
                        pricecustomize = pricecustomize + newpricecustomize;
                    }
                });
            }
        }
    });

    price = parseFloat(pricebase) + parseFloat(pricecustomize);

    $('#cpafromPrice').html(PriceFormat(price));
}

function getCustomerPricedimensions(element) {

    var field = $(element).attr('data-field');

    var minWidth = parseInt($('.cpa_dimension_text[data-field="' + field + '"].dimension_text_width').attr('min'));
    var maxWidth = parseInt($('.cpa_dimension_text[data-field="' + field + '"].dimension_text_width').attr('max'));
    var minHeight = parseInt($('.cpa_dimension_text[data-field="' + field + '"].dimension_text_height').attr('min'));
    var maxHeight = parseInt($('.cpa_dimension_text[data-field="' + field + '"].dimension_text_height').attr('max'));
    var minDepth = parseInt($('.cpa_dimension_text[data-field="' + field + '"].dimension_text_depth').attr('min'));
    var maxDepth = parseInt($('.cpa_dimension_text[data-field="' + field + '"].dimension_text_depth').attr('max'));

    var valWidth = parseInt($('.cpa_dimension_text[data-field="' + field + '"].dimension_text_width').val()) || 0;
    var valHeight = parseInt($('.cpa_dimension_text[data-field="' + field + '"].dimension_text_height').val()) || 0;
    var valDepth = parseInt($('.cpa_dimension_text[data-field="' + field + '"].dimension_text_depth').val()) || 0;

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
                MarkField(field, true);
            },
            error: function (xhr) {
                $('#cpafield_' + field).attr('data-price', 0);
                addCustomerPrice();
                $('#cpafield_' + field).prop('disabled', true);
                MarkField(field, false);
            }
        });
    } else {
        $('#cpafield_' + field).attr('data-price', 0);
        addCustomerPrice();
        $('#cpafield_' + field).prop('disabled', true);
        MarkField(field, false);
    }


}

function ProccessPriceCPAFieldValue(element) {

    var field = $(element).attr('data-field');
    var price = $(element).attr('data-price');
    var idvalue = $(element).attr('data-id-value');
    var qty = $(element).attr('data-qty');
    var type = $(element).attr('data-typefield');
    var ishasClass = $(element).hasClass('select-value');

    if (ishasClass) {
        $('#cpafield_' + field).val(type + '_' + field + '_' + idvalue + '_' + qty);
        $('#cpafield_' + field).attr('data-price', price);
        $('#cpafield_' + field).prop('disabled', false);
    } else {
        $('#cpafield_' + field).val('0_0_0_0');
        $('#cpafield_' + field).attr('data-price', 0);
        $('#cpafield_' + field).prop('disabled', true);
    }
    addCustomerPrice();
    ProgressBar();
}

function ProccessPriceCPAFieldWithoutQty(element, qty) {
    var field = $(element).attr('data-field');
    var price = $(element).attr('data-price');
    var idvalue = $(element).attr('data-id-value');
    var type = $(element).attr('data-typefield');

    $('#cpafield_value_' + idvalue).val(type + '_' + field + '_' + idvalue + '_' + qty);
    $('#cpafield_value_' + idvalue).attr('data-price', parseFloat(price) * parseInt(qty));
    if (parseInt(qty) > 0) {
        $('#cpafield_value_' + idvalue).prop('disabled', false);
        addCustomerPrice();
    } else {
        addCustomerPrice();
        $('#cpafield_value_' + idvalue).prop('disabled', true);
    }
}