<?php


class CpaCfv extends ObjectModel
{

  public $id_cpa_customization_field;
  public $price;
  public $cost_price;
  public $color;
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
      'price' =>   ['type' => self::TYPE_INT,  'required' => true],
      'cost_price' =>   ['type' => self::TYPE_INT,  'required' => false],
      'color' =>   ['type' => self::TYPE_INT,  'required' => false],
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

    // Db::getInstance()->delete(
    //   'specific_price_customize_product',
    //   'id_specific_price_rule = ' . (int) Tools::getValue('id_specific_price_rule')
    // );

    // return  Db::getInstance()->delete(
    //   'specific_price_rule_customize_product',
    //   'id_specific_price_rule = ' . (int) Tools::getValue('id_specific_price_rule')
    // );
  }


  public static function setDefaultConfig($id_cpa_customization_field_value)
  {
    $config = new CpaCfv((int)$id_cpa_customization_field_value);
  }
}
