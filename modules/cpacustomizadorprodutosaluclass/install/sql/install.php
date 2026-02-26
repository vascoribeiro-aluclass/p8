<?php


$sql = [];
$sqlIndexes = [];
$sqlUpdate = [];
$prefix = _DB_PREFIX_;
$engine = _MYSQL_ENGINE_;
$tables =
	[
		[
			'name' => 'cpa_customization_field',
			'primary' => 'id_cpa_customization_field',
			'cols' =>
			[
				['name' => 'id_cpa_customization_field', 'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT'],
				['name' => 'id_cpa_customization_field_type', 'opts' => 'int(10) NOT NULL'],
				['name' => 'position', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
				['name' => 'order_position', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
				['name' => 'required', 'opts' => 'tinyint(4) NOT NULL DEFAULT 0'],
				['name' => 'is_visual', 'opts' => 'tinyint(4) NOT NULL DEFAULT 0'],
				['name' => 'price_type', 'opts' => 'varchar(255) NOT NULL DEFAULT "amount"'],
				['name' => 'zindex', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
				['name' => 'admin_name', 'opts' => 'varchar(255) NOT NULL'],
				['name' => 'open_status', 'opts' => 'tinyint(4) NOT NULL DEFAULT 0'],
				['name' => 'isvisivel', 'opts' => 'tinyint(4) NOT NULL DEFAULT 0'],

			]
		],
		[
			'name' => 'cpa_customization_field_lang',
			'index' => ['id_cpa_customization_field', 'id_lang'],
			'primary' => '',
			'cols' =>
			[
				['name' => 'id_cpa_customization_field', 'opts' => 'int(10) NOT NULL'],
				['name' => 'id_lang', 'opts' => 'int(10) NOT NULL'],
				['name' => 'name', 'opts' => 'varchar(255) NOT NULL'],
				['name' => 'notice', 'opts' => 'text NOT NULL'],
				['name' => 'tooltip', 'opts' => 'text NOT NULL'],
			]
		],
		[
			'name' => 'cpa_customization_field_shop',
			'index' => ['id_cpa_customization_field', 'id_shop'],
			'primary' => '',
			'cols' =>
			[
				['name' => 'id_cpa_customization_field', 'opts' => 'int(10) NOT NULL'],
				['name' => 'id_shop', 'opts' => 'int(10) NOT NULL'],

			]
		],
		[
			'name' => 'cpa_customization_field_type',
			'primary' => 'id_cpa_customization_field_type',
			'cols' =>
			[
				['name' => 'id_cpa_customization_field_type', 'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT'],
				['name' => 'name', 'opts' => 'varchar(255) NOT NULL'],
				['name' => 'class', 'opts' => 'varchar(255) NOT NULL'],
			]
		],
		[
			'name' => 'cpa_customization_field_influences',
			'index' => ['id_cpa_customization_field', 'id_cpa_customization_field_influence'],
			'primary' => '',
			'cols' =>
			[
				['name' => 'id_cpa_customization_field_influence', 'opts' => 'int(10) NOT NULL'],
				['name' => 'id_cpa_customization_field', 'opts' => 'int(10) NOT NULL'],

			]
		],

		[
			'name' => 'cpa_customization_field_product',
			'index' => ['id_cpa_customization_field', 'id_product'],
			'primary' => '',
			'cols' =>
			[
				['name' => 'id_cpa_customization_field', 'opts' => 'int(10) NOT NULL'],
				['name' => 'id_product', 'opts' => 'int(10) NOT NULL'],

			]
		],

		[
			'name' => 'cpa_customization_field_value',
			'primary' => 'id_cpa_customization_field_value',
			'cols' =>
			[
				['name' => 'id_cpa_customization_field_value', 'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT'],
				['name' => 'id_cpa_customization_field', 'opts' => 'int(10) NOT NULL'],
				['name' => 'price', 'opts' => 'float NOT NULL'],
				['name' => 'cost_price', 'opts' => 'float NOT NULL'],
				['name' => 'color', 'opts' => 'varchar(255) NOT NULL'],
				['name' => 'quantity_min', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
				['name' => 'quantity_max', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
				['name' => 'position', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
				['name' => 'reference', 'opts' => 'varchar(255) NOT NULL'],
				['name' => 'isvisivel', 'opts' => 'tinyint(4) NOT NULL DEFAULT 1'],

			]
		],
		[
			'name' => 'cpa_customization_field_value_influence',
			'index' => ['id_cpa_customization_field_value', 'id_cpa_cust_field_val_infl'],
			'primary' => '',
			'cols' =>
			[
				['name' => 'id_cpa_customization_field_value', 'opts' => 'int(10) NOT NULL'],
				['name' => 'id_cpa_cust_field_val_infl', 'opts' => 'int(10) NOT NULL'],

			]
		],
		[
			'name' => 'cpa_customization_field_value_lang',
			'index' => ['id_cpa_customization_field_value', 'id_lang'],
			'primary' => '',
			'cols' =>
			[
				['name' => 'id_cpa_customization_field_value', 'opts' => 'int(10) NOT NULL'],
				['name' => 'id_lang', 'opts' => 'int(10) NOT NULL'],
				['name' => 'name', 'opts' => 'varchar(255) NOT NULL'],
				['name' => 'description', 'opts' => 'varchar(2500) NOT NULL'],

			]
		],
		[
			'name' => 'cpa_customization_field_value_shop',
			'index' => ['id_cpa_customization_field_value', 'id_shop'],
			'primary' => '',
			'cols' =>
			[
				['name' => 'id_cpa_customization_field_value', 'opts' => 'int(10) NOT NULL'],
				['name' => 'id_shop', 'opts' => 'int(10) NOT NULL'],

			]
		],
		[
			'name' => 'cpa_customization_field_value_excludes_product',
			'index' => ['id_cpa_customization_field_value', 'id_product'],
			'primary' => '',
			'cols' =>
			[
				['name' => 'id_cpa_customization_field_value', 'opts' => 'int(10) NOT NULL'],
				['name' => 'id_product', 'opts' => 'int(10) NOT NULL'],

			]
		],

		[
			'name' => 'cpa_customization_field_configuration',
			'primary' => 'cpa_customization_field_configuration',
			'cols' =>
			[
				['name' => 'cpa_customization_field_configuration', 'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT'],
				['name' => 'id_user', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
				['name' => 'id_lang_default', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
				['name' => 'id_product', 'opts' => 'int(10) NOT NULL'],
				['name' => 'id_customization', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
				['name' => 'price', 'opts' => 'float NOT NULL'],
				['name' => 'json_values', 'opts' => 'text NOT NULL'],
			]
		],
		[
			'name' => 'cpa_customization_field_configuration_lang',
			'index' => ['id_cpa_customization_field_configuration', 'id_lang'],
			'primary' => '',
			'cols' =>
			[
				['name' => 'id_cpa_customization_field_configuration', 'opts' => 'int(10) NOT NULL'],
				['name' => 'id_lang', 'opts' => 'int(10) NOT NULL'],
				['name' => 'name', 'opts' => 'varchar(255) NOT NULL'],

			]
		],

		[
			'name' => 'cpa_customization_field_csv',
			'primary' => 'id_cpa_customization_field_csv',
			'cols' =>
			[
				['name' => 'id_cpa_customization_field_csv', 'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT'],
				['name' => 'id_cpa_customization_field', 'opts' => 'int(10) NOT NULL DEFAULT 0'],
				['name' => 'width', 'opts' => 'varchar(255) NOT NULL'],
				['name' => 'height', 'opts' => 'varchar(255) NOT NULL DEFAULT 0'],
				['name' => 'depth', 'opts' => 'varchar(255) NOT NULL DEFAULT 0'],
				['name' => 'price', 'opts' => 'float NOT NULL DEFAULT 0'],
			]
		],

		[
			'name' => 'ndk_customization_field_cache',
			'primary' => 'id_cache',
			'cols' =>
			[
				['name' => 'id_cache', 'opts' => 'int(10) NOT NULL PRIMARY KEY AUTO_INCREMENT'],
				['name' => 'key_cache', 'opts' => 'varchar(255) NOT NULL'],
				['name' => 'content', 'opts' => 'LONGTEXT NOT NULL'],
				['name' => 'expire', 'opts' => 'int(10) NOT NULL'],
			]
		],

	];

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

	$sqlCheckRemove = 'SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS 
	 WHERE table_name = "' . $prefix . $table['name'] . '" 
	 AND table_schema = "' . _DB_NAME_ . '" 
	 AND column_name = "remove_me_after" ';

	$checkRemove = Db::getInstance()->executeS($sqlCheckRemove);

	if (sizeof($checkRemove) > 0)
		$sqlremover[] = "ALTER TABLE " . $prefix . $table['name'] . " DROP COLUMN remove_me_after";
}


foreach ($sqlIndexes as $query)
	if (Db::getInstance()->execute($query) == false)
		return false;

$sqlinsert[] = "INSERT INTO `" . $prefix . "cpa_customization_field_type` (name,class) VALUES ('Dimensões','cpatypedimensions')";
$sqlinsert[] = "INSERT INTO `" . $prefix . "cpa_customization_field_type` (name,class) VALUES ('Seletor por imagens','cpatypeselectorimages')";
$sqlinsert[] = "INSERT INTO `" . $prefix . "cpa_customization_field_type` (name,class) VALUES ('Seletor Radio Button ','cpatypeselectorradio')";
$sqlinsert[] = "INSERT INTO `" . $prefix . "cpa_customization_field_type` (name,class) VALUES ('Seletor Checkbox','cpatypeselectorcheckbox')";
$sqlinsert[] = "INSERT INTO `" . $prefix . "cpa_customization_field_type` (name,class) VALUES ('Acessórios Quantidade','cpatypeaccessquant')";
$sqlinsert[] = "INSERT INTO `" . $prefix . "cpa_customization_field_type` (name,class) VALUES ('Acessórios Sem Quantidade','cpatypeaccessnoquant')";


foreach ($sqlinsert as $query)
	if (Db::getInstance()->execute($query) == false)
		return false;
foreach ($sqlremover as $query)
	if (Db::getInstance()->execute($query) == false)
		return false;
