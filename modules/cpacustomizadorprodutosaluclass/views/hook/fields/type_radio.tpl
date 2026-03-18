<div class="form-group cpaFieldItem " data-orderposition="{$order_position}" data-typefield="{$type_id}"
  data-position="{$position}" data-field="{$id_cpa_customization_field}">
  <label class="toggler {if $open_status == 1} active {/if}">
    {$name}
  </label>


  <div class="fieldPane clearfix" {if $open_status == 0}style="display: none;" {/if}>
    {if $notice !=''}
      <div class="field_notice clearfix clear">{$notice nofilter}</div>
    {/if}

    <div class="clearfix clear row mt-2 {if $required == 1} required_field{/if}" id="main-{$id_cpa_customization_field}"
      data-field="{$id_cpa_customization_field}" data-typefield="{$type_id}">
      <input class="fromset pricecal" data-message="" id="cpafield_{$id_cpa_customization_field}" type="hidden"
        name="cpafield_{$id_cpa_customization_field}" data-price="0" value="0_0_0" disabled />

      {foreach from=$fieldValues item=value}
        <div data-id-value="{$value.id_cpa_customization_field_value}" class="col-md-12 col-xs-12 "
          data-root="{$id_cpa_customization_field}">
          <span class="radio">
            <input id="radio_{$value.id_cpa_customization_field_value}" 
              type="radio"
              class="cpafieldvalueradio cpafieldvalue {if $is_visual == 1}is_visual{/if}" 
              data-src="{$value.img}"
              data-zindex="{$zindex|escape:'htmlall':'UTF-8'}" 
              data-value="{$value.name|escape:'htmlall':'UTF-8'}"
              data-price="{$value.price|escape:'htmlall':'UTF-8'}"
              data-qty="1" 
              name="{$id_cpa_customization_field}"
              data-typefield="{$type_id}"
              data-id-value="{$value.id_cpa_customization_field_value}" data-field="{$id_cpa_customization_field}"  />
            <label id="name_{$value.id_cpa_customization_field_value}">{$value.name nofilter} {if $value.price > 0} : + {Tools::convertPrice($value.price_with_iva, Context::getContext()->currency->id)|round:2} €{/if}</label>
          </span>
        </div>
      {/foreach}
      <div id="error-{$id_cpa_customization_field}" class=" errorCPA alert-danger clear clearfix"
        style="display: none;"></div>
    </div>

  </div>

</div>