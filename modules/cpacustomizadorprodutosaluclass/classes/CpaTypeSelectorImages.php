<?php
class CpaTypeSelectorImages extends CpaFields
{

    protected $notice;
    protected $tooltip;
    protected $zindex;
    protected $is_visual;



    public function __construct($arrayCustomizationField,  $id_product)
    {
        $this->notice    = isset($arrayCustomizationField['notice'])    ? $arrayCustomizationField['notice']    : '';
        $this->tooltip   = isset($arrayCustomizationField['tooltip'])   ? $arrayCustomizationField['tooltip']   : '';
        $this->zindex    = isset($arrayCustomizationField['zindex'])    ? $arrayCustomizationField['zindex']    :  0;
        $this->is_visual = isset($arrayCustomizationField['is_visual']) ? $arrayCustomizationField['is_visual'] :  0;

        parent::__construct($arrayCustomizationField, $id_product);
        $this->processField();
    }

    public function processField()
    {
        $fieldValues = $this->getFieldValues();
        foreach ($fieldValues as &$value) {
            $value['price_with_iva'] = 0;
            if ($value['price'] > 0) {
                $value['price_with_iva'] = $this->getIVAPrice($value['price']);
            }
            $arrayImgLink = $this->getImgs($value['id_cpa_customization_field_value']);

            if (count($arrayImgLink) > 0) {
                $value['thumbs']  = $arrayImgLink['thumbs'];
                $value['img']     = $arrayImgLink['img'];
                $value['preview'] = $arrayImgLink['preview'];
            } else {
                $value['thumbs']  = [];
                $value['img']     = [];
                $value['preview'] = [];
            }
        }
        $this->arrayAssign = [
            "fieldValues"    => $fieldValues,
            "notice"         => $this->notice,
            "tooltip"        => $this->tooltip,
            "zindex"         => $this->zindex,
            "influencesmain" => $this->getInfluencesMain(),
            "influencesput" => $this->getInfluencesPut(),
            "influencespercentage" => $this->getInfluencesPercentage(),
            "is_visual"      => $this->is_visual,
            "isvisivel"      => $this->isvisivel,
            "position"       => $this->position,
            "order_position" => $this->order_position,
            "type_id"        => $this->type_id,
            "required"       => $this->required,
            "price_type"     => $this->price_type,
            "id_cpa_customization_field" => $this->id_cpa_customization_field,
            "open_status"    => $this->open_status,
            "name"           => $this->name,
        ];
    }



    public function getTemplate()
    {
        return 'views/hook/fields/type_image.tpl';
    }

    private function getHtmlImg($path, $arrayImg, $id_cpa_customization_field_value)
    {
        $arrayImgLink = [];

        if (key_exists($path, $arrayImg)) {

            foreach ($arrayImg[$path] as $imgIconValue) {
                $arrayImgLink[] =  $this->getBaseUrlWithoutVirtual().'img/scenes/' . $path . $id_cpa_customization_field_value . '.' . $imgIconValue;
            }
            
        }
        return $arrayImgLink;
    }

    private function getImgs($id_cpa_customization_field_value)
    {
        $sqlImgvalues = 'SELECT 
                                id_cpa_customization_field_value, 
                                `ext`, 
                                `type`
                            FROM ' . _DB_PREFIX_ . 'cpa_customization_field_value_img cfv
                            WHERE cfv.id_cpa_customization_field_value = ' . (int)$id_cpa_customization_field_value . '
                            ';

        $arrayImgTemp = Db::getInstance()->executeS($sqlImgvalues);
        $resultImgLink = [];
 
        if (is_array($arrayImgTemp)) {
            if (count($arrayImgTemp) > 0) {
                foreach ($arrayImgTemp as $valueImg) {
                    $arrayImg[$valueImg['type']][] = $valueImg['ext'];
                }

                $resultImgLink['thumbs']  = $this->getHtmlImg('cpa/thumbs/', $arrayImg, $id_cpa_customization_field_value);
                $resultImgLink['img']     = $this->getHtmlImg('cpa/img/', $arrayImg, $id_cpa_customization_field_value);
                $resultImgLink['preview'] = $this->getHtmlImg('cpa/preview/', $arrayImg, $id_cpa_customization_field_value);
            }
        }

        return $resultImgLink;
    }
}
