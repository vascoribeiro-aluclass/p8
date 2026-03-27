<div class="form-group cpa-disable-div cpaFieldItem {$influencesput}" data-influences="{$influencesmain}" data-orderposition="{$order_position}" {if !$isvisivel} style="display: none;"
	{/if} data-typefield="{$type_id}" data-position="{$position}" data-field="{$id_cpa_customization_field}">
	<label class="toggler {if $open_status == 1} active {/if}">
		{$name}
		{if $tooltip !=''}
			<span class="tooltipDescMark">
				<i class="material-icons tooltip-cpa" data-toggle="tooltip" title="{$tooltip|escape:'html'}">
					help
				</i>
			</span>
		{/if}
	</label>


	<div class="fieldPane clearfix" {if $open_status == 0}style="display: none;" {/if}>
		{if $notice !=''}
			<div class="field_notice clearfix clear">{$notice nofilter}</div>
		{/if}

		<ul class="cpa-accessory-list" id="main-{$id_cpa_customization_field}"
			data-field="{$id_cpa_customization_field}" data-typefield="{$type_id}">
			{foreach from=$fieldValues item=value}
				<li class="cpa-accessory-item row" data-id-value="{$value.id_cpa_customization_field_value}"
					{if !$value.isvisivel} style="display: none;" {/if} data-root="{$id_cpa_customization_field}">
					
					<div class="col-md-4">
					<div id="tooltipPreview_{$value.id_cpa_customization_field_value}" class="tooltipPreview"></div>
						<picture>
							{if $value.thumbs|@count > 1}
								<source src="{$value.thumbs[1]}">
							{/if}
							<img loading="lazy"
								class="img-responsive {if count($value.preview) > 0 }cpafieldvalue-preview-img {/if}"
								{if count($value.preview) > 0 } data-src-view="{$value.preview[0]};{$value.preview[1]}"
									{/if} {if $value.thumbs|@count > 1} src="{$value.thumbs[0]}" {else}
									src="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' width='100' height='100'><rect width='100' height='100' fill='%23{$value.colorpicker|replace:'#':''}'/></svg>"
								{/if} data-id-value="{$value.id_cpa_customization_field_value}">
							<picture>
					</div>
					<div class="col-md-8 cpa-infos-block">
						<div class="row">
							<div class="col-md-4 ">
								<div class="input-group bootstrap-touchspin">
									<span class="input-group-addon bootstrap-touchspin-prefix"
										style="display: none;"></span>
									<input class="fromset pricecal " data-price-type="{$price_type}"   data-influences-percentage="{$influencespercentage}"
										id="cpafield_value_{$value.id_cpa_customization_field_value}" type="hidden"
										name="cpafield_value_{$value.id_cpa_customization_field_value}" data-price="0"
										value="0_0_0" disabled />

									<input id="cpafieldvalue-qty-{$value.id_cpa_customization_field_value}"
										data-typefield="{$type_id}" type="number"
										data-id-value="{$value.id_cpa_customization_field_value}"
										data-field="{$id_cpa_customization_field}"
										data-price="{$value.price|escape:'htmlall':'UTF-8'}" name="qty" id="quantity_wanted"
										inputmode="numeric" pattern="[0-9]*" value="0" min="0"
										class="input-group form-control cpafieldvalue-qty " aria-label="Quantidade"
										style="display: block;">
									<span class="input-group-addon bootstrap-touchspin-postfix"
										style="display: none;"></span><span class="input-group-btn-vertical"><button
											class="btn btn-touchspin js-touchspin bootstrap-touchspin-up cpafieldvalue-qty-up"
											data-id-value="{$value.id_cpa_customization_field_value}" type="button"><i
												class="material-icons touchspin-up"></i></button><button
											class="btn btn-touchspin js-touchspin bootstrap-touchspin-down  cpafieldvalue-qty-down"
											data-id-value="{$value.id_cpa_customization_field_value}" type="button"><i
												class="material-icons touchspin-down"></i></button></span>
								</div>
							</div>
							<div class="col-md-8 ">
								<label style="text-align: left;"
									id="name_{$value.id_cpa_customization_field_value}">{$value.name nofilter}
								</label>
							</div>
							<div class="col-md-12 ">
								<label id="price_{$value.id_cpa_customization_field_value}">
									<i id="descriptionPrice_{$value.id_cpa_customization_field_value|escape:'intval'}">
										{if $price_type == 'amount'} 
											+ {Tools::convertPrice($value.price_with_iva, Context::getContext()->currency->id)|round:2} €
										{else}
											+ {Tools::convertPrice($value.price, Context::getContext()->currency->id)|round:0} %
										{/if}
									</i>
			  					</label>
							{if $value.description !=''}
								<span class="tooltipDescMark">
									<i class="material-icons tooltip-cpa" data-toggle="tooltip"
										title="{$value.description|escape:'html'}">
										help
									</i>
								</span>
							{/if}
							</div>
						</div>


					</div>

				</li>
			{/foreach}
			<div id="error-{$id_cpa_customization_field}" class=" errorCPA alert-danger clear clearfix"
				style="display: none;"></div>
		</ul>

	</div>

</div>