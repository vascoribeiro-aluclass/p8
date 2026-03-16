
<div class="block " id="cpafields-block" data-key="{$id_product}">
    <form id="cpafields" class="ajax_form" action="http://localhost/p8/modules/cpacustomizadorprodutosaluclass/">
        <div class=" groupFieldBlock packlistGroup">
            {$htmlFields nofilter}
        </div>
        <div class="form-group clearfix box-info-product submitContainer">
        <button class="btn btn-primary submitCpafields "  form="cpafields" id="submitCpafields" name="submitCpafields">
            <i class="material-icons shopping-cart" aria-hidden="true">shopping_cart</i>
            <div class="mon_text">{l s=' Adicionar ao carrinho' mod='cpacustomizadorprodutosaluclass'}</div>
        </button>
    </form>
</div>