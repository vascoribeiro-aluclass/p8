<?php


$sql = array();
$prefix = _DB_PREFIX_;
$engine = _MYSQL_ENGINE_;

$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_lang`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_shop`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_product`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_type`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_influences`";

$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_value`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_value_lang`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_value_shop`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_value_excludes_product`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_value_influences`";

// tabelas de produtos presonalizados
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_configuration`";
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_configuration_lang`";
// tabela medidas
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_csv`";
// tabela cache
$sql[] = "DROP TABLE IF EXISTS `".$prefix."cpa_customization_field_cache`";



foreach ($sql as $query)
	if (Db::getInstance()->execute($query) == false)
		return false;
