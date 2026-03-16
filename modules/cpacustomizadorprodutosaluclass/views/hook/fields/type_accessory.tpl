<div class="form-group cpaFieldItem " data-orderposition="{$order_position}" data-typefield="{$type_id}"
	data-position="{$position}" data-field="{$id_cpa_customization_field}">
	<label class="toggler {if $open_status == 1} active {/if}">
		{$name}
	</label>


	<div class="fieldPane clearfix" {if $open_status == 0}style="display: none;" {/if}>
		{if $notice !=''}
			<div class="field_notice clearfix clear">{$notice nofilter}</div>
		{/if}

		<ul class="cpa-accessory-list" id="main-{$id_cpa_customization_field}"
			data-field="{$id_cpa_customization_field}" data-typefield="{$type_id}">


			{foreach from=$fieldValues item=value}
				<li class="cpa-accessory-item row" data-id-value="{$value.id_cpa_customization_field_value}"
					data-root="{$id_cpa_customization_field}">
					<div class="col-md-4">
						<img loading="lazy" class="img-responsive" src="{$value.thumbs}">
					</div>
					<div class="col-md-8 cpa-infos-block">
						<div class="row">
							<div class="col-md-4 ">
								<div class="input-group bootstrap-touchspin">
									<span class="input-group-addon bootstrap-touchspin-prefix"
										style="display: none;"></span>
									<input class="fromset" data-message=""
										id="cpafield_value_{$value.id_cpa_customization_field_value}" type="hidden"
										name="cpafield_value_{$value.id_cpa_customization_field_value}" data-price="0"
										value="0_0_0" disabled/>

									<input id="cpafieldvalue-qty-{$value.id_cpa_customization_field_value}" type="number" data-id-value="{$value.id_cpa_customization_field_value}" data-field="{$id_cpa_customization_field}"  data-price="{$value.price|escape:'htmlall':'UTF-8'}" name="qty" id="quantity_wanted" inputmode="numeric"
										pattern="[0-9]*" value="0" min="0" class="input-group form-control cpafieldvalue-qty "
										aria-label="Quantidade" style="display: block;">
									<span class="input-group-addon bootstrap-touchspin-postfix"
										style="display: none;"></span><span class="input-group-btn-vertical"><button
											class="btn btn-touchspin js-touchspin bootstrap-touchspin-up cpafieldvalue-qty-up"  data-id-value="{$value.id_cpa_customization_field_value}" type="button"><i
												class="material-icons touchspin-up"></i></button><button
											class="btn btn-touchspin js-touchspin bootstrap-touchspin-down  cpafieldvalue-qty-down"  data-id-value="{$value.id_cpa_customization_field_value}" type="button"><i
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
									{if $value.price > 0} +
										{Tools::convertPrice($value.price_with_iva, Context::getContext()->currency->id)|round:2}
									€{/if}</label>
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