{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2014 Hendrik Masson
 *  @license   Tous droits réservés
*}


<div class="form-group ndkackFieldItem aluclass-disable-div {$influences[$field.id_ndk_customization_field]}" data-rposition="{$field.ref_position|escape:'intval'}"
  data-typefield="{$field.type|escape:'intval'}" data-position="{$field.position|escape:'intval'}"
  data-iteration="{$field_iteration}" data-id="{$field.target|escape:'htmlall':'UTF-8'}"
  data-view="{$field.target_child|escape:'htmlall':'UTF-8'}"
  data-field="{$field.id_ndk_customization_field|escape:'htmlall':'UTF-8'}" data-qtty-min="{$field.quantity_min}"
  data-qtty-max="{$field.quantity_max}">
  <label class="toggler" {if $field.is_picto}
      style="background-image: url('{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/pictos/{$field.id_ndk_customization_field|escape:'intval'}.jpg');"
    {/if}>{$field.name|escape:'htmlall':'UTF-8'}
    {if $field.is_visual == 1}
      <span class="layer_view visible_layer" data-group="{$field.id_ndk_customization_field|escape:'intval'}"
        data-zindex="{$field.zindex|escape:'htmlall':'UTF-8'}" data-id="{$field.target|escape:'htmlall':'UTF-8'}"
        data-view="{$field.target_child|escape:'htmlall':'UTF-8'}" />&nbsp;</span>
    {/if}
    {if $field.tooltip !=''}
      <span class="tooltipDescMark">
      <div class="tooltip-ndk">
        <div class="tooltipDescription"> {$field.tooltip nofilter}</div>
      </div>
    </span>
    {/if}
  </label>
  <span class="progress-field-required">
    <span class="progress-required-text">
      (Optionnel)
    </span>
  </span>
  <div class="fieldPane clearfix"  style="display: none;">

    {if $field.notice !=''}
      <div class="field_notice clearfix clear pt-1">{$field.notice nofilter}</div>
    {/if}
    <!--<input data-message="{l s='Informe' mod='ndk_advanced_custom_fields'} {$field.name|escape:'htmlall':'UTF-8'}" id="ndkcsfield_{$field.id_ndk_customization_field|escape:'intval'}" type="text" name="ndkcsfield[{$field.id_ndk_customization_field|escape:'intval'}]" value="" class="{if $field.required == 1} required_field{/if}"/>-->
    <div class="minmaxBlock">
      <p class="quantity_error_up alert-danger clear clearfix">
        {l s="You can't add more than " mod='ndk_advanced_custom_fields'}{$field.quantity_max}
        {l s='quantities' mod='ndk_advanced_custom_fields'}</p>
      <p data-name="ndkcsfield[{$field.id_ndk_customization_field|escape:'intval'}]"
        class=" alert-danger clear clearfix quantity_error_down  {if $field.quantity_min > 0}required_field{/if}"
        val="">{l s="You must add a minimum of " mod='ndk_advanced_custom_fields'}{$field.quantity_min}
        {l s='quantities' mod='ndk_advanced_custom_fields'}</p>
    </div>
    <!-- bloc tags -->
    <div class="clearfix clear row" id="main-{$field.id_ndk_customization_field|escape:'intval'}">
      <div class="clear col-xs-12 clearfix visu-tools"></div>
    </div>
    <!-- bloc tags -->
    <ul class="ndk_accessory_list accessory_no_quantity">
      {foreach from=$field.values item=value}
        {assign var=tags value=','|explode:$value.tags}
        {if $field.price_type == 'percent'}
          {assign var='valuePrice' value=$value.price}
        {else}
          {assign var='valuePrice' value=Tools::convertPrice($value.price, Context::getContext()->currency->id)|round:2}
        {/if}
        {if $value.set_quantity == 0 || $value.quantity > 0}
          <li
            class="col-xs-6 clearfix product_{$field.id_ndk_customization_field|escape:'intval'}_{$value.id_product_value} accessory-ndk accessory-ndk-no-quantity {if $field.is_visual == 1}visual-effect {/if} filterTag {if $value.tags && $value.tags !=''} tagged {foreach from=$tags item=tag}{$tag|replace:' ':'-'} {/foreach}{/if}"
            data-tags="{foreach from=$tags item=tag}{$tag}|{/foreach}" data-value="{$value.value|escape:'htmlall':'UTF-8'}"
            title="{$value.value|escape:'htmlall':'UTF-8'}"
            data-src="{if $value.is_image}{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/thumbs/{$value.id|escape:'intval'}-{Configuration::get('NDK_IMAGE_LARGE_SIZE')}.jpg{else}0{/if}"
            data-group="{$field.id_ndk_customization_field|escape:'intval'}"
            data-zindex="{$field.zindex|escape:'htmlall':'UTF-8'}" data-dragdrop="{$field.draggable|escape:'intval'}"
            data-resizeable="{$field.resizeable|escape:'intval'}" data-rotateable="{$field.rotateable|escape:'intval'}"
            data-price="{if $valuePrice > 0}{$valuePrice|escape:'htmlall':'UTF-8'}{else}{$fieldPrice|escape:'htmlall':'UTF-8'}{/if}"
            data-id="{$field.target|escape:'htmlall':'UTF-8'}" data-id-value="{$value.id|escape:'htmlall':'UTF-8'}"
            data-view="{$field.target_child|escape:'htmlall':'UTF-8'}">
            <div class="accessory_img_block clear clearfix " id="img_div_{$value.id|escape:'htmlall':'UTF-8'}">
              <img loading="lazy" class="img-responsive img_div_acess" id="img_{$value.id|escape:'htmlall':'UTF-8'}"
                src="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/thumbs/{$value.id|escape:'intval'}-small_default.jpg" />

            </div>
            <div style="display:none">
              <div id="accessory-popup-{$value.id|escape:'intval'}" class="accessory-popup-ndk">
                {if $value.is_image}
                  <div class="col-md-6 ndk-img-block">
                    <!--<img data-target-value="{$value.id|escape:'intval'}" class="img-responsive set_one_quantity_img" src="{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/thumbs/{$value.id|escape:'intval'}-{Configuration::get('NDK_IMAGE_LARGE_SIZE')}.jpg"/>-->
                  </div>
                {/if}
                <div class="col-sm-10 ndk-infos-block">
                  <p class="title_block">{$value.value|escape:'htmlall':'UTF-8'}</p>
                  <div class="ndk-accessory-desc">{$value.description nofilter}</div>
                  <div class="price custumerprice_{$value.id|escape:'intval'}">
                    {if $valuePrice > 0}
                      {if $field.price_type == 'percent'}
                        +{$valuePrice}%
                      {else}
                        {if $reduction_value < 100}
                          <s>{convertPrice price=$valuePrice}</s>
                          <span style="color: var(--red);"> {convertPrice price=$valuePrice-($valuePrice*($reduction_value/100))}</span>
                        {else}
                          {convertPrice price=$valuePrice}
                        {/if}

                      {/if}
                    {else}
                      {if $fieldPrice > 0} :
                        {if $field.price_type == 'percent'}
                          +{$fieldPrice}%
                        {else}
                          {convertPrice price=$fieldPrice}
                        {/if}
                      {/if}
                    {/if}
                  </div>
                </div>
              </div>
            </div>
            <div class="clear clearfix accessory-infos">
              <b id="descriptionimg_{$value.id|escape:'htmlall':'UTF-8'}">{$value.value|escape:'htmlall':'UTF-8'}</b>

              <p class="ndk-accessory-quantity-block">
                {assign var='defaultValue' value=0}
                {if $value.step_quantity !=''}
                  {assign var="steps" value=";"|explode:$value.step_quantity}
                  {foreach from=$steps item=step}
                    {if $step|strstr:"*"}
                      {assign var="defaultValue" value=$step|replace:"*":""}
                    {/if}
                  {/foreach}
                {/if}

                <input type="text"
                  name="ndkcsfield[{$field.id_ndk_customization_field|escape:'intval'}][quantity][{$value.value|escape:'intval'}]"
                  {if $value.set_quantity == 1}data-qtty-available="{$value.quantity|escape:'intval'}" {/if}
                  data-qtty-max="{$value.quantity_max|escape:'intval'}"
                  data-qtty-min="{$value.quantity_min|escape:'intval'}"
                  {if $value.quantity_max > 0}max="{$value.quantity_max|escape:'intval'}" {/if}
                  min="{$value.quantity_min|escape:'intval'}" type="text" class="ndk-accessory-quantity price_overrided"  style ="display: none;"
                  id="ndk-accessory-quantity-{$value.id|escape:'intval'}"
                  value="{if $defaultValue > 0 && $defaultValue > $value.quantity_min}{$defaultValue}{else}{$value.quantity_min|escape:'intval'}{/if}"
                  data-default-value="{if $defaultValue > 0 && $defaultValue > $value.quantity_min}{$defaultValue}{else}{$value.quantity_min|escape:'intval'}{/if}"
                  data-step_quantity="{$value.step_quantity|escape:'htmlall'|replace:'*':''}"
                  data-price="{if $valuePrice > 0}{$valuePrice|escape:'htmlall':'UTF-8'}{else}{$fieldPrice|escape:'htmlall':'UTF-8'}{/if}"
                  data-group="{$field.id_ndk_customization_field|escape:'intval'}"
                  data-hide-field="{if $value.influences_restrictions|strpos:"all" !== false}1{else}0{/if}"
                  data-id-value="{$value.id|escape:'intval'}" data-value="{$value.value|escape:'htmlall':'UTF-8'}"
                  data-value-id="{$field.id_ndk_customization_field|escape:'intval'}-{$value.id|escape:'intval'}"
                  data-step_quantity="{$value.step_quantity|escape:'intval'}" />

              </p>

              {$arrayidfield = [4803,4750,4752,4753,4754,4756,4757,4758,4759,4761,4762,4763,4764,4765,3495,1121,1148,3488,1124,3494,3489,1142,1138,1128,1120,1125,1141,3490,1131,1134,1122,3491,1139,1153,3493,1137,3492,1126,1127,1133,1130,1136,1140,3496,1129,1135,1132,1145,3985,3699,1391,4607,4626,4613,4611,4609,4622,4624,4615,4628,4620,4720,4721,4722,4723,4725,4726,4727,4728,4699,4700,4701,4702,4681,4686,4687,4688,4685,4689,4690,4691,4693,4694,4695,4696,4704,4707,4705,4706,4735,4736,4737,4738,4729,4731,4732,4733,4714,4715,4716,4717,4745,4746,4747,4748,4709,4710,4711,4712,4740,4741,4742,4743]}

              {$arrayidfield = [5426,5417,5424,5425,5439,5440,5441,5442,5443,5444,5445,5446,5447,5448,5449,5450,5452,5453,5454,5455,5456,5457,5458,5459,5460,5462,5463,5465,5466,5467,5472,5473,5518,5522,5523,5524,5546]}


               {* {if in_array($field.id_ndk_customization_field, $arrayidfield)}
                {if $field.price_type != 'percent'}
                  {$valuePrice = ($valuePrice/1.2)*1.1}
                {/if}
              {/if} *}

              <div class="price custumerprice_{$value.id|escape:'intval'}" id="price_{$value.id|escape:'intval'}">
              {if $valuePrice > 0}
                {if $field.price_type == 'percent'}
                  +{$valuePrice}%
                {else}

                    {* {if $reduction_value < 100}
                      <s>{convertPrice price=$valuePrice}</s>
                      <span style="color: var(--red);">
                        {convertPrice price=$valuePrice-($valuePrice*($reduction_value/100))}</span>
                    {else}
                      {convertPrice price=$valuePrice}
                    {/if} *}

                    {if in_array($field.id_ndk_customization_field, $arrayidfield)}
                      {if $reduction_value < 100}
                        {convertPrice price=$valuePrice}
                      {else}
                        {convertPrice price=$valuePrice}
                      {/if}
                    {else}
                      {if $reduction_value < 100}
                        <s>{convertPrice price=$valuePrice}</s>
                        <span style="color: var(--red);">
                          {convertPrice price=$valuePrice-($valuePrice*($reduction_value/100))}</span>
                      {else}
                        {convertPrice price=$valuePrice}
                      {/if}
                    {/if}

                {/if}
              {else}
                  {if $fieldPrice > 0} :
                    {if $field.price_type == 'percent'}
                      +{$fieldPrice}%
                    {else}
                      {convertPrice price=$fieldPrice}
                    {/if}
                  {/if}
                {/if}
              </div>
            </div>
            {if $value.description !=''}
              <span class="tooltipDescMark">
              <div class="tooltip-ndk">
                <div class="tooltipDescription"> {$value.description nofilter}</div>
              </div>
            </span>
            {/if}
          </li>

        {/if}
      {/foreach}
    </ul>
    {include file='./specific_prices.tpl'}
  </div>
</div>
