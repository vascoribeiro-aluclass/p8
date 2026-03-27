<div class="form-group cpa-disable-div cpaFieldItem {$influencesput}" data-influences="{$influencesmain}" {if !$isvisivel} style="display: none;"{/if} data-orderposition="{$order_position}" data-typefield="{$type_id}"
	data-position="{$position}" data-field="{$id_cpa_customization_field}">
	<label class="toggler {if $open_status == 1} active {/if}">
		{$name} 
		{if $tooltip !=''}
			<span class="tooltipDescMark">
				<i class="material-icons tooltip-cpa" data-toggle="tooltip" title="{$tooltip|escape:'html'}">
					help
				</i>
			</span>
		{/if}
		<span id="progress-field-cpa-{$id_cpa_customization_field}" class="progress-field ">  </span>
	</label>


	<div class="fieldPane clearfix" {if $open_status == 0}style="display: none;" {/if}>
		{if $notice !=''}
			<div class="field_notice clearfix clear">{$notice nofilter}</div>
		{/if}

		<div class="clearfix clear row mt-1 {if $required == 1} required_field{/if}" id="main-{$id_cpa_customization_field}" data-field="{$id_cpa_customization_field}" data-typefield="{$type_id}">

			{foreach from=$fieldValues item=value}
        	<input class="fromset pricecal " data-price-type="{$price_type}"   data-influences-percentage="{$influencespercentage}"
										id="cpafield_value_{$value.id_cpa_customization_field_value}" type="hidden"
										name="cpafield_value_{$value.id_cpa_customization_field_value}" data-price="0"
										value="0_0_0_0" disabled />

			<div data-id-value="{$value.id_cpa_customization_field_value}" {if !$value.isvisivel} style="display: none;"{/if} class="col-md-3 col-xs-4 img-item-row"
					data-root="{$id_cpa_customization_field}">
					<center>
						<i id="name_{$value.id_cpa_customization_field_value}">{$value.name nofilter} </i>
					</center>
					<div id="tooltipPreview_{$value.id_cpa_customization_field_value}" class="tooltipPreview"></div>
					
						<picture>
							{if $value.thumbs|@count > 1}
								<source src="{$value.thumbs[1]}">
							{/if}
							<img loading="lazy" id="cpafieldvalue-qty-{$value.id_cpa_customization_field_value}" class="cpafieldvalue-qty img-value {if count($value.preview) > 0 }cpafieldvalue-preview-img {/if} {if $is_visual == 1}is_visual{/if}"
								data-value="{$value.name|escape:'htmlall':'UTF-8'}" 
								title="{$value.name|escape:'htmlall':'UTF-8'}"
								{if count($value.img) > 0 }
									data-src="{$value.img[0]};{$value.img[1]}" 
								{/if}
								{if count($value.preview) > 0 }
									data-src-view="{$value.preview[0]};{$value.preview[1]}" 
								{/if}
								{if $value.thumbs|@count > 1}
									src="{$value.thumbs[0]}" 
								{else}
									src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' width='100' height='100'><rect width='100' height='100' fill='%23{$value.colorpicker|replace:'#':''}'/></svg>"
								{/if}
								data-zindex="{$zindex|escape:'htmlall':'UTF-8'}"
								data-qty="1" 
								data-typefield="{$type_id}"
								data-price="{$value.price|escape:'htmlall':'UTF-8'}"
								data-id-value="{$value.id_cpa_customization_field_value}"
								data-field="{$id_cpa_customization_field}" />
						</picture>
					
					
					<center>
						<i id="descriptionPrice_{$value.id_cpa_customization_field_value|escape:'intval'}">
						{if $value.price > 0}
							{if $price_type == 'amount'} 
								+ {Tools::convertPrice($value.price_with_iva, Context::getContext()->currency->id)|round:2} €
							{else}
							    + {Tools::convertPrice($value.price, Context::getContext()->currency->id)|round:0} %
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