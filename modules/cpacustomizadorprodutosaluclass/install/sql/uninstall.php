<?php


$sql = array();
$prefix = _DB_PREFIX_;
$engine = _MYSQL_ENGINE_;

$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_lang`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_type`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_rel_customization_product`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_rel_customization_category`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_value`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_rel_customization_value_excludes_product`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_value_lang`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_shop`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_configuration`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_configuration_lang`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_csv`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_cache`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_rel_customization_field_shop`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_rel_customization_field_value_shop`";

foreach ($sql as $query)
	if (Db::getInstance()->execute($query) == false)
		return false;
