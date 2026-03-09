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
  public $dimensions;
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
      'dimensions' =>   ['type' => self::TYPE_INT,  'required' => false],
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

    $id_cpa_customization_field = (int)Tools::getValue('id_cpa_customization_field');

    $customizationValues = Db::getInstance()->executeS("Select id_cpa_customization_field_value from " . _DB_PREFIX_ . "cpa_customization_field_value where id_cpa_customization_field = " . (int)$id_cpa_customization_field);



    Db::getInstance()->delete(
      'cpa_customization_field',
      'id_cpa_customization_field = ' . (int)$id_cpa_customization_field
    );

    Db::getInstance()->delete(
      'cpa_customization_field_lang',
      'id_cpa_customization_field = ' . (int)$id_cpa_customization_field
    );

    Db::getInstance()->delete(
      'cpa_customization_field_shop',
      'id_cpa_customization_field = ' . (int)$id_cpa_customization_field
    );

    Db::getInstance()->delete(
      'cpa_customization_field_influences',
      'id_cpa_customization_field = ' . (int)$id_cpa_customization_field
    );

    Db::getInstance()->delete(
      'cpa_customization_field_csv',
      'id_cpa_customization_field = ' . (int)$id_cpa_customization_field
    );

    Db::getInstance()->delete(
      'cpa_customization_field_product',
      'id_cpa_customization_field = ' . (int)$id_cpa_customization_field
    );

    foreach ($customizationValues as $customizationValue) {
      $id_cpa_customization_field_value = (int)$customizationValue['id_cpa_customization_field_value'];

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

      $arrayPath = ['preview', 'img', 'thumbs'];
      $arrayExt = ['webp', 'jpg', 'png', 'jpeg'];

      foreach ($arrayPath as $path) {
        foreach ($arrayExt as $ext) {
          $destination = _PS_IMG_DIR_ . 'scenes/cpa/' . $path . '/' . (int)$id_cpa_customization_field_value . '.' . $ext;

          if (is_file($destination) && file_exists($destination)) {
            unlink($destination);
          }
        }
      }
    }


    $context = Context::getContext();

    Tools::redirectAdmin(
      $context->link->getAdminLink('AdminCpaCustomization')
    );
  }


  public static function setDefaultConfig($id_cpa_customization_field)
  {
    $config = new CpaCf((int)$id_cpa_customization_field);
  }
}
