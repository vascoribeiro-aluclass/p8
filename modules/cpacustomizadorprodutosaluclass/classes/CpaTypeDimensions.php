<?php
class CpaTypeDimensions extends CpaFields
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
        $fieldValues[0]["coor"] = 'width';
        $fieldValues[1]["coor"] = 'height';
        $fieldValues[2]["coor"] = 'depth';

        $arraydimensions = $this->getDimension();
        if (count($arraydimensions) > 0) {
            $fieldValues[0]["min_dimensions"] = $arraydimensions[0]["min_width"];
            $fieldValues[0]["max_dimensions"] = $arraydimensions[0]["max_width"];
            $fieldValues[1]["min_dimensions"] = $arraydimensions[0]["min_height"];
            $fieldValues[1]["max_dimensions"] = $arraydimensions[0]["max_height"];
            $fieldValues[2]["min_dimensions"] = $arraydimensions[0]["min_depth"];
            $fieldValues[2]["max_dimensions"] = $arraydimensions[0]["max_depth"];
        }

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
        return 'views/hook/fields/type_dimension_text.tpl';
    }

    private function getDimension()
    {
        $sqldimensions = 'SELECT 
                                MIN(width) AS min_width,
                                MAX(width) AS max_width,
                                MIN(height) AS min_height,
                                MAX(height) AS max_height,
                                MIN(`depth`) AS min_depth,
                                MAX(`depth`) AS max_depth
                            FROM ' . _DB_PREFIX_ . 'cpa_customization_field_csv 
                            where id_cpa_customization_field = ' . $this->id_cpa_customization_field;


        $result = Db::getInstance()->executeS($sqldimensions);
        return $result ?: [];
    }
}
