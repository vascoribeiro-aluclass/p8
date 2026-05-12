<div class="modal fade" id="CPAModalShare" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title" id="CPAModalTitle">{l s='Partilha de Carrinho' mod='cpacustomizadorprodutosaluclass.model'}</h5>
            </div>

            <div class="modal-body">
                <form id="CPAFormModal">

                    <div class="mb-3">
                        <label>{l s='Nome' mod='cpacustomizadorprodutosaluclass.model'}</label>
                        <input type="text" name="CPAname" class="form-control" required>
                        <div id="CPAAlertModalname" class="alert alert-danger " style="display: none;" role="alert">{l s='Falta o nome.' mod='cpacustomizadorprodutosaluclass.model'}</div>
                    </div>

                    <div class="mb-3">
                        <label>{l s='E-mail' mod='cpacustomizadorprodutosaluclass.model'}</label>
                        <input type="email" name="CPAemail" class="form-control" required>
                        <div id="CPAAlertModalemail" class="alert alert-danger " style="display: none;" role="alert">{l s='Falta o e-mail ou e-mail inválido.' mod='cpacustomizadorprodutosaluclass.model'}</div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <div class="row">
                    <div class="col-6 col-md-6 d-flex justify-content-start">
                        <button type="button" class="btn btn-secondary" id="CPAcloseModal">{l s='Fechar' mod='cpacustomizadorprodutosaluclass.model'}</button>
                    </div>
                    <div class="col-6 col-md-6 d-flex justify-content-end">
                        <button type="button" class="btn btn-primary" id="CPAsubmitFormModal">{l s='Enviar' mod='cpacustomizadorprodutosaluclass.model'}</button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>