<?php
abstract class CpaFields
{

    protected $id_cpa_customization_field;
    protected $position;
    protected $order_position;
    protected $required;
    protected $price_type;
    protected $open_status;
    protected $isvisivel;
    protected $name;
    protected $type_name;
    protected $type_id;
    protected $id_product;
    protected $arrayAssign = [];

    protected function __construct($arrayCustomizationField, $id_product)
    {
        $this->id_cpa_customization_field = $arrayCustomizationField['id_cpa_customization_field'];
        $this->position       = $arrayCustomizationField['position'];
        $this->order_position = $arrayCustomizationField['order_position'];
        $this->required       = $arrayCustomizationField['required'];
        $this->price_type     = $arrayCustomizationField['price_type'];
        $this->open_status    = $arrayCustomizationField['open_status'];
        $this->isvisivel      = $arrayCustomizationField['isvisivel'];
        $this->name           = $arrayCustomizationField['name'];
        $this->type_name      = $arrayCustomizationField['type_name'];
        $this->type_id        = $arrayCustomizationField['type_id'];
        $this->id_product     = $id_product;
    }

    abstract public function getTemplate();
    abstract public function processField();

    protected function getIVAPrice($price)
    {
        $tax_rate = Tax::getProductTaxRate($this->id_product,null,Context::getContext());
        $price_with_iva = $price + ($price * $tax_rate / 100);

        return $price_with_iva;
    }

    protected function getFieldValues()
    {
        $sqlfieldvalues = 'SELECT 
                                cfv.id_cpa_customization_field_value, 
                                cfv.price,  
                                cfv.cost_price,  
                                cfv.colorpicker,
                                cfv.quantity_min,
                                cfv.quantity_max,
                                cfv.reference,
                                cfv.isvisivel,
                                cfv.position,  
                                cfvl.name, 
                                cfvl.description
                            FROM ' . _DB_PREFIX_ . 'cpa_customization_field_value cfv
                            INNER JOIN ' . _DB_PREFIX_ . 'cpa_customization_field_value_lang  cfvl ON (cfv.id_cpa_customization_field_value = cfvl.id_cpa_customization_field_value) and cfvl.id_lang = ' . (int)Context::getContext()->language->id . '
                            INNER JOIN ' . _DB_PREFIX_ . 'cpa_customization_field_value_shop  cfs ON (cfv.id_cpa_customization_field_value = cfs.id_cpa_customization_field_value) and cfs.id_shop = ' . (int)Context::getContext()->shop->id . '
                            WHERE cfv.id_cpa_customization_field = ' . (int)$this->id_cpa_customization_field . '
                            ORDER BY cfv.position ASC';

        $result = Db::getInstance()->executeS($sqlfieldvalues);
        return $result ?: [];
    }

    public function getAssign()
    {
        return $this->arrayAssign;
    }


}
