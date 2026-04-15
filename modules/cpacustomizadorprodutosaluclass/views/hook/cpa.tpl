<div class="block " id="cpafields-block" data-key="{$id_product}">
    <div id="productprogressbarfluid" style="height: 88px;">
        <div id="productprogressbar" style="height: 88px; background: rgb(255, 255, 255);">
            <div class="ndkcfTitle" style="border: 1px solid #dbdbdb !important;
			box-shadow: 0 0 7px #dedede;padding: 8px 8px 8px 8px !important;">
                <h2 style="font-size: 1rem;"> {l s='Estado da sua personalização' mod='cpacustomizadorprodutosaluclass'}
                </h2>
                <div class="progress">
                    <span class="progress-text-begin">{l s='Processo : 0%' mod='cpacustomizadorprodutosaluclass'}
                    </span>
                    <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:0%">
                        <span class="progress-text">{l s='0% Completo' mod='cpacustomizadorprodutosaluclass'} </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id="cpafields" class="ajax_form">
        <div class=" groupFieldBlock packlistGroup">
            {$htmlFields nofilter}
        </div>
        <div class="form-group clearfix box-info-product submitContainer">
            <div class='row'>
                <div class='col-xl-5 col-lg-12 col-md-12 col-sm-12 col-12'>

                    <button class="btn btn-primary submitCpabudget mt-1" id="submitCpabudget" name="submitCpabudget">
                        <span class="material-icons">
                            picture_as_pdf
                        </span>
                        <div class="mon_text">{l s='Orçamentos' mod='cpacustomizadorprodutosaluclass'}</div>
                    </button>
                </div>
                <div class='col-xl-7 col-lg-12 col-md-12 col-sm-12 col-12'>
                    <button class="btn btn-primary submitCpafields mt-1 " form="cpafields" id="submitCpafields"
                        name="submitCpafields">
                        <i class="material-icons shopping-cart" aria-hidden="true">shopping_cart</i>
                        <div class="mon_text">{l s='Adicionar ao carrinho' mod='cpacustomizadorprodutosaluclass'}</div>
                    </button>
                </div>

            </div>
        </div>
    </form>
</div>