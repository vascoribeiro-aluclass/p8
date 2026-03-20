function setupFileValidation(inputName, allowedTypes, acceptAttr, textError) {
    const $input = $(`input[name="${inputName}"]`);
    if (!$input.length) return;

    $input.attr('accept', acceptAttr);

    $input.on('change', function () {
        const file = this.files[0];
        const $formGroup = $(this).closest('.form-group');

        $formGroup.removeClass('has-error').find('.help-block.text-danger').remove();

        if (file) {
            if ($.inArray(file.type, allowedTypes) === -1) {
                $formGroup.addClass('has-error');
                $(this).after('<div class="help-block text-danger"><b>Erro:</b> ' + textError + '</div>');
                $(this).val('');
            }
        }
    });
}
function manageVisibility(selectedValue) {

    $('.visivel-1, .visivel-2, .visivel-3, .visivel-4, .visivel-5, .visivel-6').closest('.form-group').hide();

    if (selectedValue == '1') {
        $('.visivel-1').closest('.form-group').show();
    } else if (selectedValue == '2') {
        $('.visivel-2').closest('.form-group').show();
    } else if (selectedValue == '3') {
        $('.visivel-3').closest('.form-group').show();
    } else if (selectedValue == '4') {
        $('.visivel-4').closest('.form-group').show();
    } else if (selectedValue == '5') {
        $('.visivel-5').closest('.form-group').show();
    } else if (selectedValue == '6') {
        $('.visivel-6').closest('.form-group').show();
    }
}


function removeImgValueCPA( idfieldvalue,path) {

 if (confirm(cpa_delete_img)) {
    $.ajax({
        type: 'POST',
        url: ajaxRemoveImgUrl,
        data: {
            idfieldvalue: idfieldvalue,
            path: path,
        },
        dataType: 'json', 
        success: function (response) {
          $('#cpa_img_'+idfieldvalue).remove();
        }
    });
    }
}

$(document).ready(function () {

    $('.visivel-1, .visivel-2, .visivsel-3, .visivel-4, .visivel-5, .visivel-6').closest('.form-group').hide();

});