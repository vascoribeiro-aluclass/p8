<?php

use PrestaShop\Module\PsxDesign\Vendor\ScssPhp\ScssPhp\Util\Path;

class AdminCpaCustomizationValueController extends ModuleAdminController
{
    public $bootstrap = true;
    public $id_cpa_customization_field = false;
    public $id_cpa_customization_field_type = false;

    public function __construct()
    {
        parent::__construct();

        $this->bootstrap = true;
        $this->table = 'cpa_customization_field_value';
        $this->className = 'CpaCfv';
        $this->lang = true;
        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->_defaultOrderBy = 'position';
        $this->_default_pagination = '50';
        $this->identifier = 'id_cpa_customization_field_value';
        $this->id_cpa_customization_field = (int)Tools::getValue('id_cpa_customization_field');

        if (!Tools::getValue('id_cpa_customization_field_type') && Tools::getValue('id_cpa_customization_field_value')) {

            $sql = 'SELECT p.id_cpa_customization_field_type 
            FROM ' . _DB_PREFIX_ . 'cpa_customization_field p 
            INNER JOIN ' . _DB_PREFIX_ . 'cpa_customization_field_value a 
            ON a.id_cpa_customization_field = p.id_cpa_customization_field
            WHERE a.id_cpa_customization_field_value = ' . (int)Tools::getValue('id_cpa_customization_field_value') . ' 
            LIMIT 1';

            $result = Db::getInstance()->executeS($sql);

            if ($result && count($result)) {
                $this->id_cpa_customization_field_type = (int)$result[0]['id_cpa_customization_field_type'];
            }
        } else if (!Tools::getValue('id_cpa_customization_field_type') && Tools::getValue('id_cpa_customization_field')) {

            $sql = 'SELECT p.id_cpa_customization_field_type 
            FROM ' . _DB_PREFIX_ . 'cpa_customization_field p 
            WHERE p.id_cpa_customization_field = ' . (int)Tools::getValue('id_cpa_customization_field') . ' 
            LIMIT 1';

            $result = Db::getInstance()->executeS($sql);

            if ($result && count($result)) {
                $this->id_cpa_customization_field_type = (int)$result[0]['id_cpa_customization_field_type'];
            }
        } else {

            $this->id_cpa_customization_field_type = (int)Tools::getValue('id_cpa_customization_field_type');
        }

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Delete selected items?', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin')
            ]
        ];

        $this->_select = "cl.name AS cf_name ";
        $this->_join   = "INNER JOIN `" . _DB_PREFIX_ . "cpa_customization_field_value_lang` cl on  a.`id_cpa_customization_field_value` = cl.`id_cpa_customization_field_value` and cl.id_lang = " . (int)$this->context->language->id;
        $this->_where  = ' and a.id_cpa_customization_field = ' . $this->id_cpa_customization_field;

        $this->fields_list = [
            'id_cpa_customization_field_value' => [
                'title' => $this->trans('ID', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'align' => 'center',
                'width' => 25,
            ],

            'cf_name' => [
                'title' => $this->trans('Nome ', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'filter_key' => 'cl!name',
                'width' => 250,

            ],
            'price' => [
                'title' => $this->trans('Preço', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'width' => 100,
            ],
            'isvisivel' => [
                'title' => $this->trans('Visível', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'active' => 'isvisivel',
                'type' => 'bool',
                'width' => 25,
                'align' => 'center',
                'orderby' => false
            ],

            'position' => [
                'title' => $this->trans('Posição', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'filter_key' => 'a!position',
                'align' => 'center',
                'width' => 25,
            ],
        ];
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJqueryPlugin('select2');

        $cpaProducts = [];

        foreach ($this->getSavedExcProductsDetailed((int)Tools::getValue('id_cpa_customization_field_value')) as $row) {
            $cpaProducts[] = [
                'id' => (int)$row['id_product'],
                'text' => $row['name'] . ' (' . $this->trans('Ref. :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin') . ' ' . $row['reference'] . ')'
            ];
        }

        Media::addJsDef([
            'ajaxExcProductUrl' => $this->context->link->getAdminLink('AdminCpaCustomizationValue', true, [], ['action' => 'SearchExcProductsCPA', 'ajax' => 1]),
            'ajaxRemoveImgUrl' => $this->context->link->getAdminLink('AdminCpaCustomizationValue', true, [], ['action' => 'RemoveImgCPA', 'ajax' => 1]),
            'already_selected_exc_products' => $cpaProducts,
            'id_cpa_customization_field_type' => $this->id_cpa_customization_field_type,
            'select2_translations' => [
                'inputTooShort' => $this->trans('Introduza pelo menos %d caracteres', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'noMatches' => $this->trans('Nenhum resultado encontrado', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'searching' => $this->trans('A pesquisar...', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'searchingProducts' => $this->trans('A pesquisar produtos...', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
            ],
            'icon_file_text_error' => $this->trans('Formato inválido. Use apenas PNG, JPG, JPEG ou WebP.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
            'img_file_text_error' => $this->trans('Formato inválido. Use apenas PNG, JPG, JPEG ou WebP.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
            'preview_file_text_error' => $this->trans('Formato inválido. Use apenas JPG, JPEG ou WebP.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
              'cpa_delete_img' => $this->trans('Deseja eliminar imagem?', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),

        ]);

        $this->addJS($this->module->getPathUri() . 'views/js/admin/cpacustomizationadmin.js');
        $this->addJS($this->module->getPathUri() . 'views/js/admin/cpacustomizationvalue.js');
    }

    public function getSavedExcProductsDetailed($id_cpa_customization_field_value)
    {
        $sql = new DbQuery();
        $sql->select('p.id_product, pl.name, p.reference');
        $sql->from('product', 'p');
        $sql->innerJoin(
            'product_lang',
            'pl',
            'p.id_product = pl.id_product AND pl.id_lang = ' . (int)$this->context->language->id . ' AND pl.id_shop = ' . (int)$this->context->shop->id
        );
        $sql->innerJoin(
            'cpa_customization_field_value_excludes_product',
            'cfp',
            'cfp.id_product = p.id_product AND cfp.id_cpa_customization_field_value = ' . (int)$id_cpa_customization_field_value
        );

        return Db::getInstance()->executeS($sql);
    }

    public function ajaxProcessRemoveImgCPA()
    {

        $id_cpa_customization_field_value = Tools::getValue('idfieldvalue');
        $path = Tools::getValue('path');

        $sql = new DbQuery();
        $sql->select('p.ext');
        $sql->from($this->table . "_img", 'p');
        $sql->where("p.id_" . $this->table . " = " . (int)$id_cpa_customization_field_value . " AND p.type = '" . pSQL($path) . "'");


        $arrayExt = Db::getInstance()->executeS($sql);

        Db::getInstance()->delete(
            $this->table . "_img",
            "id_" . $this->table . " = " . (int)$id_cpa_customization_field_value . " AND type = '" . pSQL($path) . "'"
        );

        foreach ($arrayExt as $ext) {
            $destination = _PS_IMG_DIR_ . 'scenes/' . $path . (int)$id_cpa_customization_field_value . '.' . $ext;

            if (is_file($destination) && file_exists($destination)) {
                unlink($destination);
            }
        }




        die(json_encode([
            'success' => true,
            'message' => 'Imagem removida com sucesso'
        ]));
    }

    public function ajaxProcessSearchExcProductsCPA()
    {
        $q = Tools::getValue('q');

        $sql = new DbQuery();
        $sql->select('p.id_product, pl.name, p.reference');
        $sql->from('product', 'p');
        $sql->innerJoin(
            'product_lang',
            'pl',
            'p.id_product = pl.id_product AND pl.id_lang = ' . (int)$this->context->language->id . ' AND pl.id_shop = ' . (int)$this->context->shop->id
        );
        $sql->where('pl.name LIKE "%' . pSQL($q) . '%"');
        $sql->limit(20);

        $products = Db::getInstance()->executeS($sql);

        die(json_encode($products));
    }

    public function renderList()
    {

        if (Tools::getIsset($this->_filter) && trim($this->_filter) == '')
            $this->_filter = $this->original_filter;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        return parent::renderList();
    }

    public function init()
    {
        CpaCfv::setDefaultConfig((int)Tools::getValue('id_cpa_customization_field_value'));
        parent::init();

        $link = $this->context->link->getAdminLink(
            'AdminCpaCustomizationValue',
            true,
            [],
            [
                'addcpa_customization_field_value' => 1,
                'id_cpa_customization_field' => (int)$this->id_cpa_customization_field,
                'id_cpa_customization_field_type' => (int)$this->id_cpa_customization_field_type,
            ]
        );

        $this->toolbar_btn['new'] = [
            'href' => $link,
            'desc' => $this->trans(
                'Adicionar novo',
                [],
                'Modules.Cpacustomizadorprodutosaluclass.Admin'
            ),
            'icon' => 'process-icon-new',

        ];
    }

    private function getAllImage()
    {
        $obj = $this->loadObject(true);
        if (!empty($obj->id)) {
            $sql = new DbQuery();
            $sql->select('p.ext, p.type');
            $sql->from($this->table . '_img', 'p');
            $sql->where('id_' . $this->table . " = " . $obj->id);
            return Db::getInstance()->executeS($sql);
        }
        return false;
    }

    private function getImageUrl($ext, $path)
    {
        $obj = $this->loadObject(true);

        if (!empty($obj->id)) {
            return 'http://localhost/p8/img/scenes/' . $path . $obj->id . '.' . $ext;
        }

        return false;
    }

    private function getHtmlImg($path, $arrayImg, $alt, $style)
    {

        $obj = $this->loadObject(true);

        if (key_exists($path, $arrayImg)) {
            $imghtml = '<div id="cpa_img_'.$obj->id.'"><picture>';

            foreach ($arrayImg[$path] as $imgIconValue) {
                if ($imgIconValue == 'webp') {
                    $imghtml .= '<img src="' . $this->getImageUrl($imgIconValue, $path) . '" alt="' . $alt . '" style="' . $style . '">';
                } else {
                    $imghtml .= '<source  src="' . $this->getImageUrl($imgIconValue, $path) . '" >';
                }
            }
            $imghtml .= '</picture><i class="material-icons " onclick="removeImgValueCPA(' . $obj->id . ',\''.$path.'\')"  title="" style="position: absolute; cursor: pointer;">
					disabled_by_default
				</i></div>';
            return $imghtml;
        }
        return '';
    }

    public function renderForm()
    {
        $obj = $this->loadObject(true);
        $id_shop = Context::getContext()->shop->id;
        $this->initFieldsForm();
        if (!($obj = $this->loadObject(true)))
            return;

        return parent::renderForm();
    }

    public function initFieldsForm()
    {
        $obj = $this->loadObject(true);

        $arrayImgTemp = $this->getAllImage();
        $arrayImg = [];
        $htmlImgIcon = '';
        $htmlImg = '';
        $htmlImgPreview = '';

        if (is_array($arrayImgTemp)) {
            foreach ($arrayImgTemp as $valueImg) {
                $arrayImg[$valueImg['type']][] = $valueImg['ext'];
            }
            $htmlImgIcon = $this->getHtmlImg('cpa/thumbs/', $arrayImg, $this->trans('Icon do campo', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'), 'max-width:100px;max-height:100px;');
            $htmlImg = $this->getHtmlImg('cpa/img/', $arrayImg, $this->trans('Imagem do campo', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'), 'max-width:250px;max-height:250px;');
            $htmlImgPreview = $this->getHtmlImg('cpa/preview/', $arrayImg, $this->trans('Preview campo', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'), 'max-width:250px;max-height:250px;');
        }



        $fields_form = [
            'legend' => [
                'title' => $this->trans('Gerir Valores Campos Customizados', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
            ],
            'submit' => [
                'title' => $this->trans('Gravar', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
            ],
            'input' => [
                [
                    'type' => 'shop',
                    'label' => $this->trans('Shop association', [], 'Admin.Global'),
                    'name' => 'checkBoxShopAsso',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Nome :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'name',
                    'required' => true,
                    'lang' => true,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->trans('Descrição :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'description',
                    'desc' => $this->trans('Descrição do valor do campo customizado.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'autoload_rte' => true,
                    'lang' => true,
                    'form_group_class' => 'visivel-2 visivel-3 visivel-4 visivel-5 visivel-6',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Produtos excluidos :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'selected_exc_products',
                    'class' => 'ajax-exc-product-search',
                    'desc' => $this->trans('Excluir este campo para estes produtos.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),

                ],

                [
                    'type' => 'color',
                    'label' => $this->trans('Cor :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'colorpicker',
                    'class' => 'color mColorPickerInput',
                    'form_group_class' => 'visivel-2 visivel-5 visivel-6',
                ],

                [
                    'type' => 'text',
                    'label' => $this->trans('Preço :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'price',
                    'required' => true,
                    'class' => 'float-field',
                    'form_group_class' => 'visivel-2 visivel-3 visivel-4 visivel-5 visivel-6',

                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Preço de Custo :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'cost_price',
                    'required' => false,
                    'class' => 'integer-field',
                    'class' => 'visivel-2 visivel-3 visivel-4 visivel-5 visivel-6',
                ],

                [
                    'type' => 'switch',
                    'label' => $this->trans('Visível :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'isvisivel',
                    'is_bool' => true,
                    'form_group_class' => 'visivel-2 visivel-3',
                    'values' => [
                        [
                            'id' => 'is_visivel_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', [], 'Admin.Global')
                        ],
                        [
                            'id' => 'is_visivel_off',
                            'value' => 0,
                            'label' => $this->trans('No', [], 'Admin.Global')
                        ]
                    ],
                ],


                [
                    'type' => 'text',
                    'label' => $this->trans('Posição :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'position',
                    'required' => true,
                    'class' => 'integer-field',
                ],

                [
                    'type' => 'file',
                    'label' => $this->trans('Ícon :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'icon_file',
                    'desc' =>  $this->trans('Adicione aqui o ícon do campo', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'class' => 'icon-file-input',
                    'form_group_class' => 'visivel-2 visivel-5 visivel-6',
                ],
                [
                    'type' => 'html',
                    'name' => 'icon_preview',
                    'label' => $this->trans('Pré-visualização do icon', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'html_content' => $htmlImgIcon,
                    'form_group_class' => 'visivel-2 visivel-5 visivel-6',
                ],

                [
                    'type' => 'file',
                    'label' => $this->trans('Imagem :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'img_file',
                    'desc' =>  $this->trans('Adicione aqui a imagem do campo', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'class' => 'img-file-input',
                    'form_group_class' => 'visivel-2 visivel-3',
                ],
                [
                    'type' => 'html',
                    'name' => 'img_preview',
                    'label' => $this->trans('Pré-visualização da imagem', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'html_content' => $htmlImg,
                    'form_group_class' => 'visivel-2 visivel-3',
                ],
                [
                    'type' => 'file',
                    'label' => $this->trans('Imagem de pré-visualização :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'preview_file',
                    'desc' =>  $this->trans('Adicione aqui a imagem de pré-visualização do campo', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'class' => 'preview-file-input',
                    'form_group_class' => 'visivel-2 visivel-3 visivel-5 visivel-6',
                ],
                [
                    'type' => 'html',
                    'name' => 'preview_preview',
                    'label' => $this->trans('Pré-visualização da imagem de pré-visualização do campo.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'html_content' =>  $htmlImgPreview,
                    'form_group_class' => 'visivel-2 visivel-3 visivel-5 visivel-6',
                ],
                [
                    'type' => 'hidden',
                    'name' => 'id_cpa_customization_field',
                    'value' => $this->id_cpa_customization_field,
                ],
            ],
        ];
        $this->fields_form = $fields_form;
    }

    public function processSave()
    {
        $id = Tools::getValue('id_cpa_customization_field_value');
        $object = $id ? new CpaCfv($id) : new CpaCfv();

        $object->id_cpa_customization_field  = (int)Tools::getValue('id_cpa_customization_field');

        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $object->name[$lang['id_lang']] = Tools::getValue('name_' . $lang['id_lang']);
            $object->description[$lang['id_lang']] = Tools::getValue('description_' . $lang['id_lang']);
        }

        $selected_exc_products = Tools::getValue('selected_exc_products');
        $object->colorpicker   = Tools::getValue('colorpicker');
        $object->price         = (float)Tools::getValue('price');
        $object->cost_price    = (float)Tools::getValue('cost_price');
        $object->isvisivel     = (int)Tools::getValue('isvisivel');
        $object->position      = (int)Tools::getValue('position');

        if ($object->save()) {

            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'cpa_customization_field_cache');
            $shops = Tools::getValue('checkBoxShopAsso_' . $this->table, []);

            Db::getInstance()->delete(
                $this->table . '_shop',
                'id_' . $this->table . ' = ' . (int)$object->id
            );

            $object->associateTo($shops);

            Db::getInstance()->delete(
                $this->table . '_excludes_product',
                'id_' . $this->table . ' = ' . (int)$object->id
            );

            if ($selected_exc_products) {
                $selected_exc_products = explode(',', $selected_exc_products);
                foreach ($selected_exc_products as $product_id) {
                    Db::getInstance()->insert(
                        $this->table . '_excludes_product',
                        [
                            'id_' . $this->table  => (int)$object->id,
                            'id_product' => (int)$product_id
                        ]
                    );
                }
            }

            $this->confirmations[] = $this->trans('Valor guardado com sucesso', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin');

            $this->updatefile((int)$object->id, 'icon_file', 'cpa/thumbs/');
            $this->updatefile((int)$object->id, 'img_file', 'cpa/img/');
            $this->updatefile((int)$object->id, 'preview_file', 'cpa/preview/');
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminCpaCustomizationValue') . '&id_cpa_customization_field=' . (int)$object->id_cpa_customization_field . '&id_cpa_customization_field_type=' . (int)$this->id_cpa_customization_field_type);
        } else {

            $this->errors[] = $this->trans(
                'Erro ao guardar o valor',
                [],
                'Modules.Cpacustomizadorprodutosaluclass.Admin'
            );
        }
    }

    public function updatefile($id_cpa_customization_field_value, $fieldsImgUpload, $path)
    {
        if (
            isset($_FILES[$fieldsImgUpload]['error']) &&
            $_FILES[$fieldsImgUpload]['error'] === UPLOAD_ERR_OK
        ) {

            // Apaga registo antigo
            Db::getInstance()->delete(
                $this->table . "_img",
                "id_" . $this->table . " = " . (int)$id_cpa_customization_field_value . " 
            AND type = '" . pSQL($path) . "'"
            );

            $file = $_FILES[$fieldsImgUpload];

            // Validar upload
            if ($error = ImageManager::validateUpload($file, 4000000)) {
                $this->errors[] = $error;
                return;
            }

            // Extensão do ficheiro
            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

            // Nome final
            $fileName = $id_cpa_customization_field_value . '.' . $ext;

            // Caminho destino
            $destination = _PS_IMG_DIR_ . 'scenes/' . $path . '/';

            // Criar pasta se não existir
            if (!file_exists($destination)) {
                mkdir($destination, 0755, true);
            }

            // Redimensionar e guardar
            if (ImageManager::resize($file['tmp_name'], $destination . $fileName)) {

                $webpFile = $destination . $id_cpa_customization_field_value . '.webp';

                if (!ImageManager::resize($file['tmp_name'], $webpFile)) {
                    $this->errors[] = $this->trans(
                        'Erro ao gerar WebP',
                        [],
                        'Modules.Cpacustomizadorprodutosaluclass.Admin'
                    );
                    return;
                }

                Db::getInstance()->insert(
                    $this->table . '_img',
                    [
                        'id_' . $this->table => (int)$id_cpa_customization_field_value,
                        'ext'  => 'webp',
                        'type' => $path,
                    ]
                );

                Db::getInstance()->insert(
                    $this->table . '_img',
                    [
                        'id_' . $this->table => (int)$id_cpa_customization_field_value,
                        'ext'  => $ext,
                        'type' => $path,
                    ]
                );

                $_POST[$fieldsImgUpload] = $fileName;
            }
        }
    }

    // public function updatefile($id_cpa_customization_field_value, $fieldsImgUpload, $path)
    // {

    //     if (isset($_FILES[$fieldsImgUpload]['error'][0]) && $_FILES[$fieldsImgUpload]['error'][0] === UPLOAD_ERR_OK) {

    //         Db::getInstance()->delete(
    //             $this->table . "_img",
    //             "id_" . $this->table . " = " . (int)$id_cpa_customization_field_value . " and  type = '" . $path . "' "
    //         );


    //         $files = $_FILES[$fieldsImgUpload];

    //         if (!is_array($files['name'])) {
    //             $files = [
    //                 'name'     => [$files['name']],
    //                 'type'     => [$files['type']],
    //                 'tmp_name' => [$files['tmp_name']],
    //                 'error'    => [$files['error']],
    //                 'size'     => [$files['size']],
    //             ];
    //         }

    //         foreach ($files['name'] as $key => $name) {

    //             if (empty($files['tmp_name'][$key])) {
    //                 continue;
    //             }

    //             $file = [
    //                 'name'     => $files['name'][$key],
    //                 'type'     => $files['type'][$key],
    //                 'tmp_name' => $files['tmp_name'][$key],
    //                 'error'    => $files['error'][$key],
    //                 'size'     => $files['size'][$key],
    //             ];

    //             if ($error = ImageManager::validateUpload($file, 4000000)) {
    //                 $this->errors[] = $error;
    //                 return;
    //             }

    //             $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    //             $fileName = $id_cpa_customization_field_value . '.' . $ext;
    //             $destination = _PS_IMG_DIR_ . 'scenes/' . $path;

    //             if (!file_exists($destination)) {
    //                 mkdir($destination, 0755, true);
    //             }

    //             if (ImageManager::resize($file['tmp_name'], $destination . $fileName)) {

    //                 Db::getInstance()->insert(
    //                     $this->table . '_img',
    //                     [
    //                         'id_' . $this->table  => (int)$id_cpa_customization_field_value,
    //                         'ext' => $ext,
    //                         'type' => $path,
    //                     ]
    //                 );

    //                 $_POST[$fieldsImgUpload] = $fileName;
    //             }
    //         }
    //     }
    // }
}
