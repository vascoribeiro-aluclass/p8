{*
 *  Tous droits réservés NDKDESIGN
 *
 *  @author Hendrik Masson <postmaster@ndk-design.fr>
 *  @copyright Copyright 2013 - 2014 Hendrik Masson
 *  @license   Tous droits réservés
*}

<div class="form-group ndkackFieldItem aluclass-disable-div {$influences[$field.id_ndk_customization_field]}" data-rposition = "{$field.ref_position|escape:'intval'}" data-typefield = "{$field.type|escape:'intval'}" data-position = "{$field.position|escape:'intval'}"  data-iteration="{$field_iteration}"  data-id="{$field.target|escape:'htmlall':'UTF-8'}" data-view="{$field.target_child|escape:'htmlall':'UTF-8'}" data-field="{$field.id_ndk_customization_field|escape:'htmlall':'UTF-8'}">
	<label class="toggler"
		{if $field.is_picto} style="background-image: url('{if isset($is_https) && $is_https}{$base_dir_ssl}{else}{$base_dir}{/if}img/scenes/ndkcf/pictos/{$field.id_ndk_customization_field|escape:'intval'}.jpg');"{/if}
	><span id="resultValue_{$field.id_ndk_customization_field|escape:'intval'}"></span>{$field.name|escape:'htmlall':'UTF-8'}
		{if $field.is_visual == 1}
			<span class="layer_view visible_layer" data-group="{$field.id_ndk_customization_field|escape:'intval'}" data-zindex="{$field.zindex|escape:'htmlall':'UTF-8'}" data-id="{$field.target|escape:'htmlall':'UTF-8'}" data-view="{$field.target_child|escape:'htmlall':'UTF-8'}"/>&nbsp;</span>
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
			{if $field.required}
				(Obligatoire)
			{else}
				(Optionnel)
			{/if}
		</span>
	</span>
	<div class="fieldPane clearfix"  style="display: none;">
		{if $field.notice !=''}
			<div class="field_notice clearfix clear pt-1 pl-2">{$field.notice nofilter}</div>
		{/if}
		<div id="warning_text_{$field.id_ndk_customization_field|escape:'htmlall':'UTF-8'}" class=" pt-1 pl-2  pb-1"></div>
	<p class="dimensions_block">
		{if $field.values.0.value != ''}
			<label class="clear clearfix dimension_text_width_{$field.id_ndk_customization_field|escape:'intval'}">{$field.values.0.value}</label>
		{else}
			<label class="clear clearfix dimension_text_width_{$field.id_ndk_customization_field|escape:'intval'}">{l s='width' mod='ndk_advanced_custom_fields'}</label>
		{/if}
		<input id="dimension_text_width_{$field.id_ndk_customization_field|escape:'intval'}" data-message="{l s='Informe' mod='ndk_advanced_custom_fields'} {$field.name|escape:'htmlall':'UTF-8'}"  placeholder="{l s='width' mod='ndk_advanced_custom_fields'}" name="ndkcsfield[{$field.id_ndk_customization_field|escape:'intval'}][width]" data-val="" data-group="{$field.id_ndk_customization_field|escape:'intval'}" data-price="" type="number" class="form-control dimension_text dimension_text_width dimension_text_{$field.id_ndk_customization_field|escape:'intval'} {if $field.required == 1} required_field{/if}" data-id="{$field.target|escape:'htmlall':'UTF-8'}" data-view="{$field.target_child|escape:'htmlall':'UTF-8'}" value="" min="{$field.price_range_min_width}" max="{$field.price_range_max_width}" size="8"/> {* value="{$field.price_range_min_width}"*}
		{if $field.values.1.value != ''}
			<label class="clear clearfix dimension_text_height_{$field.id_ndk_customization_field|escape:'intval'}">{$field.values.1.value}</label>
		{else}
			<label class="clear clearfix dimension_text_height_{$field.id_ndk_customization_field|escape:'intval'}">{l s='height' mod='ndk_advanced_custom_fields'}</label>
		{/if}
		<input id="dimension_text_height_{$field.id_ndk_customization_field|escape:'intval'}" data-message="{l s='Informe' mod='ndk_advanced_custom_fields'} {$field.name|escape:'htmlall':'UTF-8'}"  placeholder="{l s='height' mod='ndk_advanced_custom_fields'}" name="ndkcsfield[{$field.id_ndk_customization_field|escape:'intval'}][height]" data-val="" data-group="{$field.id_ndk_customization_field|escape:'intval'}" data-price="" type="number" class="form-control dimension_text dimension_text_height dimension_text_{$field.id_ndk_customization_field|escape:'intval'} {if $field.required == 1} required_field{/if}" data-id="{$field.target|escape:'htmlall':'UTF-8'}" data-view="{$field.target_child|escape:'htmlall':'UTF-8'}" value="" min="{$field.price_range_min_height}" max="{$field.price_range_max_height}" size="8"/> {* value="{$field.price_range_min_height}"*}
	</p>
	</div>
</div>
