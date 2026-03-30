<div class="form-group cpa-disable-div cpaFieldItem {$influencesput}" data-influences="{$influencesmain}"  {if !$isvisivel} style="display: none;" {/if}
	data-orderposition="{$order_position}" data-typefield="{$type_id}" data-position="{$position}"
	data-field="{$id_cpa_customization_field}">
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

		<div class="clearfix clear row mt-1 {if $required == 1} required_field{/if}"
			id="main-{$id_cpa_customization_field}" data-field="{$id_cpa_customization_field}"
			data-typefield="{$type_id}">

			{foreach from=$fieldValues item=value}

				<div data-id-value="{$value.id_cpa_customization_field_value}" {if !$value.isvisivel} style="display: none;"
					{/if} class="col-md-12 " data-root="{$id_cpa_customization_field}">
					<input data-message="" class="fromset {if $required == 1} required_field{/if} {if !$value.isvisivel}select-value{/if}"
						id="cpafield_value_{$value.id_cpa_customization_field_value}" type="hidden"
						name="cpafield_value_{$value.id_cpa_customization_field_value}" {if !$value.isvisivel}
							value="{$type_id}_{$id_cpa_customization_field}_{$value.id_cpa_customization_field_value}_0"
						{else} value="0_0_0" disabled 
						{/if} />
					<label
						class="clear clearfix field_text_{$value.id_cpa_customization_field_value}">{$value.name}</label>
					<input id="field_text_{$value.id_cpa_customization_field_value}"
						placeholder="{$value.name}" name="cpafield_{$value.id_cpa_customization_field_value}" type="text"
						data-typefield="{$type_id}"
						class="form-control cpa_field_text  field_text_{$value.id_cpa_customization_field_value} "
						data-id-value="{$value.id_cpa_customization_field_value}" data-field="{$id_cpa_customization_field}"
						 size="20" />
						<span id="error-dimension-{$value.id_cpa_customization_field_value}" style="display: none;" class="error-dimension">{l s='Medida fora dos valores premitidos' mod='cpacustomizadorprodutosaluclass'}</span>
				</div>

			{/foreach}
			<div id="error-{$id_cpa_customization_field}" class=" errorCPA alert-danger clear clearfix"
				style="display: none;"></div>
		</div>

	</div>


</div>