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
			<input data-message=""  class="pricecal" 
				id="cpafield_{$id_cpa_customization_field}" type="hidden" name="cpafield_{$id_cpa_customization_field}"
				data-price="0" disabled />

			{foreach from=$fieldValues item=value}

				<div data-id-value="{$value.id_cpa_customization_field_value}" class="col-md-12 mt-1"
					data-root="{$id_cpa_customization_field}">
					<input data-message=""  
							class="fromset " 
							id="cpafield_value_{$value.id_cpa_customization_field_value}" 
							type="hidden" 
							name="cpafield_value_{$value.id_cpa_customization_field_value}"
							value="0_0_0" disabled />
					<label class="clear clearfix dimension_text_{$value.coor}_{$value.id_cpa_customization_field_value}">{$value.name}</label>
					<input  id="dimension_text_{$value.coor}_{$value.id_cpa_customization_field_value}" 
	  						placeholder="{$value.name}" 
							name="cpafield_{$value.id_cpa_customization_field_value}" 
							type="number" 
							data-typefield="{$type_id}"
							class="form-control cpa_dimension_text dimension_text_{$value.coor} dimension_text_{$value.id_cpa_customization_field_value} " 
							data-id-value="{$value.id_cpa_customization_field_value}" 
							data-field="{$id_cpa_customization_field}" 
							min="{$value.min_dimensions}" 
							max="{$value.max_dimensions}" 
							size="8"/>
				</div>

			{/foreach}
			<div id="error-{$id_cpa_customization_field}" class=" errorCPA alert-danger clear clearfix" style="display: none;"></div>
		</div>
		
	</div>


</div>