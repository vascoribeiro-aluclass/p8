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
    protected $context;

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
        $this->context     = Context::getContext();
    }

    abstract public function getTemplate();
    abstract public function processField();

    protected function getIVAPrice($price)
    {
        $tax_rate = Tax::getProductTaxRate($this->id_product, null, $this->context);
        $price_with_iva = $price + ($price * $tax_rate / 100);

        return $price_with_iva;
    }

    protected function getInfluencesMain()
    {
        $stringresult = '';
        $sqlfieldvalues = 'SELECT 
                                cfi.id_cpa_customization_field_value_show,
                                cfi.id_cpa_customization_field_influence,
                                cfi.id_cpa_customization_field
                            FROM ' . _DB_PREFIX_ . 'cpa_customization_field_influences cfi
                            INNER JOIN ' . _DB_PREFIX_ . 'cpa_customization_field_product cfp on cfi.id_cpa_customization_field = cfp.id_cpa_customization_field and cfp.id_product = ' . (int)$this->id_product . '
                            WHERE cfi.id_cpa_customization_field = ' . (int)$this->id_cpa_customization_field . ' and cfp.id_product = ' . (int)$this->id_product;

        $result = Db::getInstance()->executeS($sqlfieldvalues);

        if (is_array($result) && !empty($result)) {
            $json = json_encode($result);
            if ($json !== false) {
                $stringresult = $json;
            }
        }

        return $stringresult;
    }

    protected function getBaseUrlWithoutVirtual()
    {
        $idShop = (int)$this->context->shop->id;

        $row = Db::getInstance()->getRow('
        SELECT domain, domain_ssl, physical_uri
        FROM ' . _DB_PREFIX_ . 'shop_url
        WHERE id_shop = ' . $idShop . ' AND main = 1
    ');

        $domain = Tools::usingSecureMode() ? $row['domain_ssl'] : $row['domain'];

        return (Tools::usingSecureMode() ? 'https://' : 'http://')
            . $domain
            . $row['physical_uri'];
    }

    protected function getInfluencesPut()
    {
        $stringresult = '';
        $sqlfieldvalues = 'SELECT 
                                cfi.id_cpa_customization_field_value_show
                            FROM ' . _DB_PREFIX_ . 'cpa_customization_field_influences cfi
                            INNER JOIN ' . _DB_PREFIX_ . 'cpa_customization_field_product cfp on cfi.id_cpa_customization_field = cfp.id_cpa_customization_field and cfp.id_product = ' . (int)$this->id_product . '
                            WHERE cfi.id_cpa_customization_field_influence = ' . (int)$this->id_cpa_customization_field . ' and cfp.id_product = ' . (int)$this->id_product;

        $result = Db::getInstance()->executeS($sqlfieldvalues);
        $arryInf = [];
        if (is_array($result)) {
            foreach ($result as $value) {
                $arryInf[] = "disabled_value_by_" . $value['id_cpa_customization_field_value_show'];
            }
        }

        $stringresult = implode(' ', $arryInf);
        return $stringresult;
    }

    protected function getInfluencesPercentage()
    {
        $stringresult = '';
        $sqlfieldvalues = 'SELECT 
                                cfi.id_cpa_customization_field
                            FROM ' . _DB_PREFIX_ . 'cpa_customization_field_influences_percentage cfi
                            INNER JOIN ' . _DB_PREFIX_ . 'cpa_customization_field_product cfp on cfi.id_cpa_customization_field = cfp.id_cpa_customization_field and cfp.id_product = ' . (int)$this->id_product . '
                            WHERE cfi.id_cpa_customization_field_percentage = ' . (int)$this->id_cpa_customization_field . ' and cfp.id_product = ' . (int)$this->id_product;

        $result = Db::getInstance()->executeS($sqlfieldvalues);
        $arryInf = [];
        if (is_array($result)) {
            foreach ($result as $value) {
                $arryInf[] = $value['id_cpa_customization_field'];
            }
        }

        $stringresult = implode(';', $arryInf);
        return $stringresult;
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
                            INNER JOIN ' . _DB_PREFIX_ . 'cpa_customization_field_value_lang  cfvl ON (cfv.id_cpa_customization_field_value = cfvl.id_cpa_customization_field_value) and cfvl.id_lang = ' . (int)$this->context->language->id . '
                            INNER JOIN ' . _DB_PREFIX_ . 'cpa_customization_field_value_shop  cfs ON (cfv.id_cpa_customization_field_value = cfs.id_cpa_customization_field_value) and cfs.id_shop = ' . (int)$this->context->shop->id . '
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
