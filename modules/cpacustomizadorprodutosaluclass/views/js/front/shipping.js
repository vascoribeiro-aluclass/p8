
$(document).ready(function () {
    const $form = $('#CPAFormModal');
    const $errorname = $('#CPAAlertModalname');
    const $erroremail = $('#CPAAlertModalemail');


    $('#submitCpashareCart').on('click', function () {
        $form.find('[name="CPAname"]').val('');
        $form.find('[name="CPAemail"]').val('');
        $errorname.hide();
        $erroremail.hide();
        $('#CPAModalShare').modal('show');

    });

        $('#CPAcloseModal').on('click', function () {
        $form.find('[name="CPAname"]').val('');
        $form.find('[name="CPAemail"]').val('');
        $errorname.hide();
        $erroremail.hide();
        $('#CPAModalShare').modal('hide');

    });



    // submit do formulário
    $('#CPAsubmitFormModal').on('click', function () {

        let valid = true;

        const name = $.trim($form.find('[name="CPAname"]').val());
        const email = $.trim($form.find('[name="CPAemail"]').val());

        // reset erros
        $errorname.hide();
        $erroremail.hide();

        // valida nome
        if (!name) {
            $errorname.show();
            valid = false;
        }

        // valida email
        if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
            $erroremail.show();
            valid = false;
        }

        if (!valid) return;

    $.ajax({
        type: 'POST',
        url: url_ajax_cpacustomizadorprodutosaluclass,
        data: {
            ajax: true,
            action: 'ProcessCPAShare',
            name: name,
            email: email
        },
        success: function (response) {

        },
        error: function (xhr) {
          
        }
    });
    });
});

