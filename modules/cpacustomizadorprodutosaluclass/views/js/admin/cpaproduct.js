

$(document).ready(function () {


    $('#create_js_file').on('click', function () {
        var name = $('#js_filename').val();
        $('#js_filename').val('');
        if (name == '') {
            showErrorMessage(text_error_nothing);
        } else {
            $.ajax({
                url: ajaxFileUrl,
                type: 'POST',
                data: { name: name },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        showSuccessMessage(response.msn);
                        $('#file').append($('<option>', {
                            value: name + '.js',
                            text: name
                        }));
                    } else {
                        showErrorMessage(response.msn);
                    }
                },
                error: function (xhr, status, error) {
                    showErrorMessage(text_error_progress);
                }
            });
        }


    });

});
