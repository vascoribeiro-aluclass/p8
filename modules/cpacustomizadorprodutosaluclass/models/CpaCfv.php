<?php


class CpaCfv extends ObjectModel
{

  public $id_cpa_customization_field;
  public $price;
  public $cost_price;
  public $colorpicker;
  public $quantity_min;
  public $quantity_max;
  public $position;
  public $reference;
  public $isvisivel  = true;
  public $name;
  public $description;


  public static $definition = [
    'table' => 'cpa_customization_field_value',
    'primary' => 'id_cpa_customization_field_value',
    'multilang' => true,
    'multishop' => true,
    'fields' => [
      'id_cpa_customization_field' => ['type' => self::TYPE_STRING,  'required' => true],
      'price' =>   ['type' => self::TYPE_FLOAT,  'required' => true],
      'cost_price' =>   ['type' => self::TYPE_FLOAT,  'required' => false],
      'colorpicker' =>   ['type' => self::TYPE_STRING,  'required' => false],
      'quantity_min' =>   ['type' => self::TYPE_INT,  'required' => false],
      'quantity_max' =>   ['type' => self::TYPE_INT,  'required' => false],
      'position' =>   ['type' => self::TYPE_INT,  'required' => true],
      'reference' =>   ['type' => self::TYPE_STRING,  'required' => false],
      'isvisivel' =>   ['type' => self::TYPE_BOOL,  'required' => false],
      'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'required' => true],
      'description' => ['type' => self::TYPE_STRING, 'lang' => true, 'required' => false],

    ],
  ];

  public function __construct($id = null)
  {
    parent::__construct($id);
  }

  public function delete()
  {
    
    $id_cpa_customization_field_value = (int)Tools::getValue('id_cpa_customization_field_value');
    $id_cpa_customization_field_type = (int)Tools::getValue('id_cpa_customization_field_type');

    Db::getInstance()->delete(
      'cpa_customization_field_value',
      'id_cpa_customization_field_value = ' . (int)$id_cpa_customization_field_value
    );

    Db::getInstance()->delete(
      'cpa_customization_field_value_excludes_product',
      'id_cpa_customization_field_value = ' . (int)$id_cpa_customization_field_value
    );

    Db::getInstance()->delete(
      'cpa_customization_field_value_influence',
      'id_cpa_customization_field_value = ' . (int)$id_cpa_customization_field_value
    );

    Db::getInstance()->delete(
      'cpa_customization_field_value_lang',
      'id_cpa_customization_field_value = ' . (int)$id_cpa_customization_field_value
    );

    Db::getInstance()->delete(
      'cpa_customization_field_value_shop',
      'id_cpa_customization_field_value = ' . (int)$id_cpa_customization_field_value
    );

    $arrayPath =['preview','img','thumbs'];
    $arrayExt =['webp','jpg','png','jpeg'];

    foreach($arrayPath as $path){
        foreach($arrayExt as $ext){
            $destination = _PS_IMG_DIR_ . 'scenes/cpa/' . $path . '/' . (int)$id_cpa_customization_field_value . '.' . $ext;

            if (is_file($destination) && file_exists($destination)) {
                unlink($destination);
            }
        }
    }

    $context = Context::getContext();

    Tools::redirectAdmin(
      $context->link->getAdminLink('AdminCpaCustomizationValue') .
        '&id_cpa_customization_field=' . (int)$this->id_cpa_customization_field . '&id_cpa_customization_field_type=' . (int)$id_cpa_customization_field_type
    );
  }


  public static function setDefaultConfig($id_cpa_customization_field_value)
  {
    $config = new CpaCfv((int)$id_cpa_customization_field_value);
  }
}
