<div class="form-group cpaFieldItem " data-orderposition="{$order_position}" data-typefield="{$type_id}"
	data-position="{$position}" data-field="{$id_cpa_customization_field}">
	<label class="toggler {if $open_status == 1} active {/if}">
		{$name}
	</label>


	<div class="fieldPane clearfix" {if $open_status == 0}style="display: none;" {/if}>
		{if $notice !=''}
			<div class="field_notice clearfix clear">{$notice nofilter}</div>
		{/if}

		<div class="clearfix clear row mt-2 {if $required == 1} required_field{/if}" id="main-{$id_cpa_customization_field}" data-field="{$id_cpa_customization_field}" data-typefield="{$type_id}">
			<input data-message=""  class="fromset pricecal" 
				id="cpafield_{$id_cpa_customization_field}" type="hidden" name="cpafield_{$id_cpa_customization_field}"
				data-price="0" value="0_0_0" disabled />

			{foreach from=$fieldValues item=value}

				<div data-id-value="{$value.id_cpa_customization_field_value}" class="col-md-3 col-xs-4 img-item-row"
					data-root="{$id_cpa_customization_field}">
					<center>
						<i id="name_{$value.id_cpa_customization_field_value}">{$value.name nofilter} </i>
					</center>
					<img loading="lazy" class="cpafieldvalue img-value {if $is_visual == 1}is_visual{/if}"
						data-value="{$value.name|escape:'htmlall':'UTF-8'}" 
						title="{$value.name|escape:'htmlall':'UTF-8'}"
						data-src="{$value.img}" 
						src="{$value.thumbs}" 
						data-zindex="{$zindex|escape:'htmlall':'UTF-8'}"
						data-qty="1" 
						data-typefield="{$type_id}"
						data-price="{$value.price|escape:'htmlall':'UTF-8'}"
						data-id-value="{$value.id_cpa_customization_field_value}"
						data-field="{$id_cpa_customization_field}" />
					<center>
						<i id="descriptionPrice_{$value.id_cpa_customization_field_value|escape:'intval'}">
						{if $value.price > 0}
							{if $price_type == 'amount'} 
								+ {Tools::convertPrice($value.price_with_iva, Context::getContext()->currency->id)|round:2} €</i>
							{else}
							    + {Tools::convertPrice($value.price, Context::getContext()->currency->id)|round:0} %</i>
							{/if}
						{/if}
						</i>
						{if $value.description !=''}
							<span class="tooltipDescMark">
								<i class="material-icons tooltip-cpa" data-toggle="tooltip"
									title="{$value.description|escape:'html'}">
									help
								</i>
							</span>
						{/if}
					</center>
				</div>

			{/foreach}
			<div id="error-{$id_cpa_customization_field}" class=" errorCPA alert-danger clear clearfix" style="display: none;"></div>
		</div>
		
	</div>


</div>