<?php


$sql = array();
$sqlIndexes = array();
$sqlUpdate = array();
$prefix = _DB_PREFIX_;
$engine = _MYSQL_ENGINE_;
$tables =
	array(
		array(
			'name' => 'cpa_customization_field',
			'primary' => 'id_cpa_customization_field',
			'cols' =>
			array(
				array('name' => 'id_cpa_customization_field', 'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT'),
				array('name' => 'id_cpa_customization_type', 'opts' => 'int(10) NOT NULL'),
				array('name' => 'id_shop', 'opts' => 'int(10) NOT NULL DEFAULT 0'),
				array('name' => 'position', 'opts' => 'int(10) NOT NULL DEFAULT 0'),
				array('name' => 'order_position', 'opts' => 'int(10) NOT NULL DEFAULT 0'),
				array('name' => 'required', 'opts' => 'tinyint(4) NOT NULL DEFAULT 0'),
				array('name' => 'is_visual', 'opts' => 'tinyint(4) NOT NULL DEFAULT 0'),
				array('name' => 'configurator', 'opts' => 'tinyint(4) NOT NULL DEFAULT 0'),
				array('name' => 'price', 'opts' => 'float NOT NULL'),
				array('name' => 'cost_price', 'opts' => 'float NOT NULL'),
				array('name' => 'unit', 'opts' => 'varchar(255) NOT NULL'),
				array('name' => 'price_type', 'opts' => 'varchar(255) NOT NULL DEFAULT "amount"'),
				array('name' => 'zindex', 'opts' => 'int(10) NOT NULL DEFAULT 0'),
				array('name' => 'colors', 'opts' => 'varchar(2500) NOT NULL'),
				array('name' => 'influences', 'opts' => 'varchar(255) NOT NULL'),
				array('name' => 'quantity_min', 'opts' => 'int(10) NOT NULL DEFAULT 0'),
				array('name' => 'quantity_max', 'opts' => 'int(10) NOT NULL DEFAULT 0'),
				array('name' => 'open_status', 'opts' => 'tinyint(4) NOT NULL DEFAULT 0'),
				array('name' => 'isvisivel', 'opts' => 'tinyint(4) NOT NULL DEFAULT 0'),

			)
		),
			array(
			'name' => 'cpa_customization_type',
			'primary' => 'id_cpa_customization_type',
			'cols' =>
			array(
				array('name' => 'id_cpa_customization_type', 'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT'),
				array('name' => 'name', 'opts' => 'varchar(255) NOT NULL'),
				array('name' => 'class', 'opts' => 'varchar(255) NOT NULL'),
			)
		),
		array(
			'name' => 'cpa_customization_field_lang',
			'index' => array('id_cpa_customization_field', 'id_lang'),
			'primary' => '',
			'cols' =>
			array(
				array('name' => 'id_cpa_customization_field', 'opts' => 'int(10) NOT NULL'),
				array('name' => 'id_lang', 'opts' => 'int(10) NOT NULL'),
				array('name' => 'name', 'opts' => 'varchar(255) NOT NULL '),
				array('name' => 'admin_name', 'opts' => 'varchar(255) NOT NULL'),
				array('name' => 'notice', 'opts' => 'text NOT NULL'),
				array('name' => 'tooltip', 'opts' => 'text NOT NULL'),
			)
		),
		array(
			'name' => 'cpa_rel_customization_field_shop',
			'index' => array('id_cpa_customization_field', 'id_shop'),
			'primary' => '',
			'cols' =>
			array(
				array('name' => 'id_cpa_customization_field', 'opts' => 'int(10) NOT NULL'),
				array('name' => 'id_shop', 'opts' => 'int(10) NOT NULL'),

			)
		),
		array(
			'name' => 'cpa_rel_customization_product',
			'index' => array('id_cpa_customization_field', 'id_product'),
			'primary' => '',
			'cols' =>
			array(
				array('name' => 'id_cpa_customization_field', 'opts' => 'int(10) NOT NULL'),
				array('name' => 'id_product', 'opts' => 'int(10) NOT NULL'),

			)
		),
		array(
			'name' => 'cpa_rel_customization_category',
			'index' => array('id_cpa_customization_field', 'id_category'),
			'primary' => '',
			'cols' =>
			array(
				array('name' => 'id_cpa_customization_field', 'opts' => 'int(10) NOT NULL'),
				array('name' => 'id_category', 'opts' => 'int(10) NOT NULL'),

			)
		),
		array(
			'name' => 'cpa_customization_field_value',
			'primary' => 'id_cpa_customization_field_value',
			'cols' =>
			array(
				array('name' => 'id_cpa_customization_field_value', 'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT'),
				array('name' => 'id_cpa_customization_field', 'opts' => 'int(10) NOT NULL'),
				array('name' => 'price', 'opts' => 'float NOT NULL'),
				array('name' => 'set_quantity', 'opts' => 'tinyint(4) NOT NULL DEFAULT 0'),
				array('name' => 'quantity', 'opts' => 'int(10) NOT NULL DEFAULT 0'),
				array('name' => 'color', 'opts' => 'varchar(255) NOT NULL'),
				array('name' => 'excludes_products', 'opts' => 'text NOT NULL'),
				array('name' => 'quantity_min', 'opts' => 'int(10) NOT NULL DEFAULT 0'),
				array('name' => 'quantity_max', 'opts' => 'int(10) NOT NULL DEFAULT 0'),
				array('name' => 'influences_restrictions', 'opts' => 'varchar(2500) NOT NULL'),
				array('name' => 'influences_obligations', 'opts' => 'varchar(2500) NOT NULL'),
				array('name' => 'position', 'opts' => 'int(10) NOT NULL DEFAULT 0'),
				array('name' => 'reference', 'opts' => 'varchar(255) NOT NULL '),
				array('name' => 'isvisivel', 'opts' => 'varchar(255) NOT NULL '),
			)
		),
				array(
			'name' => 'cpa_rel_customization_field_value_shop',
			'index' => array('id_cpa_customization_field_value', 'id_shop'),
			'primary' => '',
			'cols' =>
			array(
				array('name' => 'id_cpa_customization_field_value', 'opts' => 'int(10) NOT NULL'),
				array('name' => 'id_shop', 'opts' => 'int(10) NOT NULL'),

			)
		),
		array(
			'name' => 'cpa_rel_customization_value_excludes_product',
			'index' => array('id_cpa_customization_field_value', 'id_product'),
			'primary' => '',
			'cols' =>
			array(
				array('name' => 'id_cpa_customization_field_value', 'opts' => 'int(10) NOT NULL'),
				array('name' => 'id_product', 'opts' => 'int(10) NOT NULL'),

			)
		),
		array(
			'name' => 'cpa_customization_field_value_lang',
			'index' => array('id_cpa_customization_field_value', 'id_lang'),
			'primary' => '',
			'cols' =>
			array(
				array('name' => 'id_cpa_customization_field_value', 'opts' => 'int(10) NOT NULL'),
				array('name' => 'id_lang', 'opts' => 'int(10) NOT NULL'),
				array('name' => 'name', 'opts' => 'varchar(255) NOT NULL '),
				array('name' => 'description', 'opts' => 'varchar(2500) NOT NULL')

			)
		),
		array(
			'name' => 'cpa_customization_field_shop',
			'index' => array('id_cpa_customization_field', 'id_shop'),
			'primary' => '',
			'cols' =>
			array(
				array('name' => 'id_cpa_customization_field', 'opts' => 'int(10) NOT NULL'),
				array('name' => 'id_shop', 'opts' => 'int(10) NOT NULL')
			)
		),

		array(
			'name' => 'cpa_customization_field_configuration',
			'primary' => 'cpa_customization_field_configuration',
			'cols' =>
			array(
				array('name' => 'cpa_customization_field_configuration', 'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT'),
				array('name' => 'id_user', 'opts' => 'int(10) NOT NULL DEFAULT 0'),
				array('name' => 'id_lang_default', 'opts' => 'int(10) NOT NULL DEFAULT 0'),
				array('name' => 'id_product', 'opts' => 'int(10) NOT NULL'),
				array('name' => 'id_customization', 'opts' => 'int(10) NOT NULL DEFAULT 0'),
				array('name' => 'price', 'opts' => 'float NOT NULL'),
				array('name' => 'json_values', 'opts' => 'text NOT NULL'),
			)
		),
		array(
			'name' => 'cpa_customization_field_configuration_lang',
			'index' => array('id_cpa_customization_field_configuration', 'id_lang'),
			'primary' => '',
			'cols' =>
			array(
				array('name' => 'id_cpa_customization_field_configuration', 'opts' => 'int(10) NOT NULL '),
				array('name' => 'id_lang', 'opts' => 'int(10) NOT NULL'),
				array('name' => 'name', 'opts' => 'varchar(255) NOT NULL'),

			)
		),

		array(
			'name' => 'cpa_customization_field_csv',
			'primary' => 'id_cpa_customization_field_csv',
			'cols' =>
			array(
				array('name' => 'id_cpa_customization_field_csv', 'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT'),
				array('name' => 'id_cpa_customization_field', 'opts' => 'int(10) NOT NULL DEFAULT 0'),
				array('name' => 'width', 'opts' => 'varchar(255) NOT NULL'),
				array('name' => 'height', 'opts' => 'varchar(255) NOT NULL DEFAULT 0'),
				array('name' => 'depth', 'opts' => 'varchar(255) NOT NULL DEFAULT 0'),
				array('name' => 'price', 'opts' => 'float NOT NULL DEFAULT 0'),
			)
		),

		array(
			'name' => 'ndk_customization_field_cache',
			'primary' => 'id_cache',
			'cols' =>
			array(
				array('name' => 'id_cache', 'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT'),
				array('name' => 'key_cache', 'opts' => 'varchar(255) NOT NULL'),
				array('name' => 'content', 'opts' => 'LONGTEXT NOT NULL'),
				array('name' => 'expire', 'opts' => 'int(10) NOT NULL'),
			)
		),

	);

foreach ($tables as $table) {

	$sql[$table['name']] = 'CREATE TABLE IF NOT EXISTS ' . $prefix . $table['name'] . ' ( remove_me_after float NOT NULL';
	$sql[$table['name']] .= ' )  ENGINE=' . $engine;

	foreach ($table['cols'] as $col) {

		//check if col exists
		$sqlCheck = 'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
		WHERE table_name = "' . $prefix . $table['name'] . '" 
		AND table_schema = "' . _DB_NAME_ . '" 
		AND column_name = "' . $col['name'] . '" ';

		$check = Db::getInstance()->executeS($sqlCheck);

		if (sizeof($check) == 0)
			$sql[] = "ALTER TABLE `" . $prefix . $table['name'] . "` ADD  `" . $col["name"] . "` " . $col["opts"];
	}

}


foreach ($sql as $query)
	if (Db::getInstance()->execute($query) == false)
		return false;


foreach ($tables as $table) {

	if (isset($table['index'])) {
		if (sizeof($table['index']) > 0) {
			$chekIndex = Db::getInstance()->executeS('SHOW INDEX FROM ' . $prefix . $table['name']);
			if (sizeof($chekIndex) > 0)
				$sqlIndexes[] = 'ALTER TABLE ' . $prefix . $table['name'] . ' DROP PRIMARY KEY';

			$sqlIndexes[] = 'ALTER TABLE ' . $prefix . $table['name'] . ' ADD PRIMARY KEY ' . implode('_', $table['index']) . ' (' . implode(',', $table['index']) . ')';
		}
	}
}


foreach ($sqlIndexes as $query)
	if (Db::getInstance()->execute($query) == false)
		return false;

$sqlinsert[] = "INSERT INTO `".$prefix."cpa_customization_type` (name,class) VALUES ('Dimensões','cpatypedimensions')";
$sqlinsert[] = "INSERT INTO `".$prefix."cpa_customization_type` (name,class) VALUES ('Seletor por imagens','cpatypeselectorimages')";
$sqlinsert[] = "INSERT INTO `".$prefix."cpa_customization_type` (name,class) VALUES ('Seletor Radio Button ','cpatypeselectorradio')";
$sqlinsert[] = "INSERT INTO `".$prefix."cpa_customization_type` (name,class) VALUES ('Seletor Checkbox','cpatypeselectorcheckbox')";
$sqlinsert[] = "INSERT INTO `".$prefix."cpa_customization_type` (name,class) VALUES ('Acessórios Quantidade','cpatypeaccessquant')";
$sqlinsert[] = "INSERT INTO `".$prefix."cpa_customization_type` (name,class) VALUES ('Acessórios Sem Quantidade','cpatypeaccessnoquant')";


foreach ($sqlinsert as $query)
	if (Db::getInstance()->execute($query) == false)
		return false;
