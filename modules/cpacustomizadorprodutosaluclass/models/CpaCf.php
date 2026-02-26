<?php


class CpaCf extends ObjectModel
{

  public $id_cpa_customization_field_type;
  public $position;
  public $order_position;
  public $required = false;
  public $is_visual = false;
  public $price_type;
  public $zindex;
  public $open_status;
  public $isvisivel = true;
  public $name;
  public $admin_name;
  public $notice;
  public $tooltip;



  public static $definition = [
    'table' => 'cpa_customization_field',
    'primary' => 'id_cpa_customization_field',
    'multilang' => true,
    'multishop' => true,
    'fields' => [
      'admin_name' => ['type' => self::TYPE_STRING,  'required' => true],
      'id_cpa_customization_field_type' =>   ['type' => self::TYPE_INT,  'required' => true],
      'position' =>   ['type' => self::TYPE_INT,  'required' => true],
      'order_position' =>   ['type' => self::TYPE_INT,  'required' => true],
      'required' =>   ['type' => self::TYPE_BOOL,  'required' => true],
      'is_visual' =>   ['type' => self::TYPE_BOOL,  'required' => true],
      'price_type' =>   ['type' => self::TYPE_STRING,  'required' => true],
      'zindex' =>   ['type' => self::TYPE_INT,  'required' => false],
      'open_status' =>   ['type' => self::TYPE_BOOL,  'required' => true],
      'isvisivel' =>   ['type' => self::TYPE_BOOL,  'required' => true],
      'name' => ['type' => self::TYPE_STRING, 'lang' => true, 'required' => true],
      'notice' => ['type' => self::TYPE_STRING, 'lang' => true, 'required' => false],
      'tooltip' => ['type' => self::TYPE_STRING, 'lang' => true, 'required' => false],

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


  public static function setDefaultConfig($id_cpa_customization_field)
  {
    $config = new CpaCf((int)$id_cpa_customization_field);
  }
}
