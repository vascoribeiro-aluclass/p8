

$(document).ready(function () {
    $('#fbx_file-selectbutton').on('click', function () {
        $('#fbx_file').click(); // abre o seletor de ficheiros
    });

    $('#fbx_file').on('change', function () {
        var fileInput = this;
        var file = fileInput.files[0];

        if (!file) return;
        var ext = file.name.split('.').pop().toLowerCase();
        if (ext !== 'fbx') {
            showErrorMessage(text_error_filefbx);
            fileInput.value = '';
            $('#fbx_file-name').val('');
            return;
        }

        $('#fbx_file-name').val(file.name);

        var formData = new FormData();
        formData.append('fbx_file', file);

        $.ajax({
            url: ajaxUploadFbxUrl,
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function (response) {
                if (response.success) {
                    showSuccessMessage(response.msn);
                    $('#filesthreed').append($('<option>', {
                        value: file.name,
                        text: file.name
                    }));
                } else {
                    showErrorMessage(response.msn);
                }
            },
            error: function (xhr, status, error) {
                showErrorMessage(text_error_progress);
            }
        });
        $('#fbx_file-name').val('');
    });

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
                        $('#filescript').append($('<option>', {
                            value: name + '.js',
                            text: name + '.js'
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
