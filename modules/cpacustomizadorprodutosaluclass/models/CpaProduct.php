<?php


class CpaProduct extends ObjectModel
{

  public $filescript;
  public $filesthreed;
  public $id_product;

  public static $definition = [
    'table' => 'cpa_customization_product',
    'primary' => 'id_cpa_customization_product',
    'fields' => [
      'filescript' => ['type' => self::TYPE_STRING,  'required' => false],
      'filesthreed' => ['type' => self::TYPE_STRING,  'required' => false],
      'id_product' => ['type' => self::TYPE_INT,  'required' => true],
    ],
  ];

  public function __construct($id = null)
  {
    parent::__construct($id);
  }

  public function delete()
  {
    parent::delete();
  }


  public static function setDefaultConfig($id_cpa_customization_product)
  {
    $config = new CpaProduct((int)$id_cpa_customization_product);
  }
}
