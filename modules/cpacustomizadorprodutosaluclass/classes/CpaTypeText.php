<?php
class CpaTypeText extends CpaFields
{

    protected $notice;
    protected $tooltip;

    public function __construct($arrayCustomizationField,  $id_product)
    {
        $this->notice    = isset($arrayCustomizationField['notice'])    ? $arrayCustomizationField['notice']    : '';
        $this->tooltip   = isset($arrayCustomizationField['tooltip'])   ? $arrayCustomizationField['tooltip']   : '';

        parent::__construct($arrayCustomizationField, $id_product);
        $this->processField();
    }

    public function processField()
    {
        $fieldValues = $this->getFieldValues();

        $this->arrayAssign = [
            "fieldValues"    => $fieldValues,
            "position"       => $this->position,
            "notice"         => $this->notice,
            "influencesmain" => $this->getInfluencesMain(),
            "influencesput" => $this->getInfluencesPut(),
            "influencespercentage" => $this->getInfluencesPercentage(),
            "tooltip"        => $this->tooltip,
            "order_position" => $this->order_position,
            "type_id"        => $this->type_id,
            "isvisivel"      => $this->isvisivel,
            "required"       => $this->required,
            "price_type"     => $this->price_type,
            "id_cpa_customization_field" => $this->id_cpa_customization_field,
            "open_status"    => $this->open_status,
            "name"           => $this->name,
        ];
    }

    public function getTemplate(): string
    {
        return 'views/hook/fields/type_text.tpl';
    }
}
