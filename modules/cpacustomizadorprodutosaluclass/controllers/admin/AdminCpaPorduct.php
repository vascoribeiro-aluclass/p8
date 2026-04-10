<?php

class AdminCpaPorductController extends ModuleAdminController
{
    public $bootstrap = true;

    public function __construct()
    {
        parent::__construct();

        $this->bootstrap = true;
        $this->table = 'cpa_customization_product';
        $this->className = 'CpaProduct';

        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->_defaultOrderBy = 'id_cpa_customization_product';
        $this->_default_pagination = '50';
        $this->identifier = 'id_cpa_customization_product';

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Excluir selecionado', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Excluir itens selecionados?', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin')
            ]
        ];

        $this->_select = "pl.name AS pl_name, ";
        $this->_join   = "INNER JOIN `" . _DB_PREFIX_ . "product_lang` pl on  a.`id_product` = pl.`id_product` and pl.id_lang = " . (int)$this->context->language->id . " and pl.id_shop = 1";


        $this->fields_list = [
            'id_cpa_customization_product' => [
                'title' => $this->trans('ID', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'align' => 'center',
                'width' => 25,
            ],

            'pl_name' => [
                'title' => $this->trans('Nome Produto ', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'filter_key' => 'pl!name',
                'width' => 250,

            ],
            'filescript' => [
                'title' => $this->trans('Ficheiro Javascript', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'width' => 100,
            ],

        ];
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        Media::addJsDef([
            'ajaxFileUrl' => $this->context->link->getAdminLink('AdminCpaPorduct', true, [], ['action' => 'CreateFileCPA', 'ajax' => 1]),
            'ajaxUploadFbxUrl' => $this->context->link->getAdminLink('AdminCpaPorduct', true, [], ['action' => 'UploadFbx', 'ajax' => 1]),
            'text_error_progress' => $this->trans('Erro crítico na comunicação com o servidor.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
            'text_error_nothing' => $this->trans('Falta o nome do ficheiro.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
            'text_error_filefbx' => $this->trans('Só são permitidos ficheiros FBX..', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin')
        ]);


        $this->addJS($this->module->getPathUri() . 'views/js/admin/cpaproduct.js');
    }

    public function ajaxProcessCreateFileCPA()
    {
        $arrayrespond = ['success' => false, 'msn' => ''];
        $folder = _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/views/js/front/product';

        $name = preg_replace('/[^a-zA-Z0-9_-]/', '', Tools::getValue('name'));

        if (empty($name)) {
            $arrayrespond['msn'] = $this->trans('Nome inválido.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
            die(json_encode($arrayrespond));
        }

        $path = $folder . "/" . $name . ".js";

        if (!file_exists($path)) {
            $content = "// Ficheiro JS criado automaticamente\n";

            if (file_put_contents($path, $content)) {
                $arrayrespond['msn'] = $this->trans('Ficheiro criado: %s.js.', [$name], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
                $arrayrespond['success'] = true;
            } else {
                $arrayrespond['msn'] = $this->trans('Erro ao gravar o ficheiro no servidor.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
            }
        } else {
            $arrayrespond['msn'] = $this->trans('Ficheiro: %s.js já existe.', [$name], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
        }

        die(json_encode($arrayrespond));
    }

    public function ajaxProcessUploadFbx()
    {
        $arrayrespond = ['success' => false, 'msn' => ''];
        if (isset($_FILES['fbx_file'])) {
            $file = $_FILES['fbx_file'];

            $targetDir = _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/views/js/front/3d/product/';
            $targetFile = $targetDir . basename($file['name']);

            if (move_uploaded_file($file['tmp_name'], $targetFile)) {
                $arrayrespond['msn'] = $this->trans('Ficheiro %s enviado com sucesso.', [$file['name']], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
                $arrayrespond['success'] = true;
            } else {
                $arrayrespond['msn'] = $this->trans('Erro ao enviar ficheiro %s.', [$file['name']], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
                $arrayrespond['success'] = false;
            }
        }

        die(json_encode($arrayrespond));
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
        CpaProduct::setDefaultConfig((int)Tools::getValue('id_cpa_customization_product'));
        parent::init();
    }

    public function renderForm()
    {

        $this->initFieldsForm();
        if (!($obj = $this->loadObject(true)))
            return;

        return parent::renderForm();
    }
    private function Getfiles($folder, $namefield)
    {
        $files = scandir($folder);
        $arrayfile = [];
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                if (is_file($folder . "/" . $file)) {
                    $inf = pathinfo($file);
                    $name = $inf['filename'];
                    $ext = $inf['extension'] ?? '';
                    $arrayfile[] = [$namefield => $name . '.' . $ext, 'name' => $name . '.' . $ext];
                }
            }
        }
        return $arrayfile;
    }

    public function initFieldsForm()
    {

        $arrayfile = [];
        $arrayfilethreed = [];

        $arrayfile = $this->Getfiles(_PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/views/js/front/product', 'filescript');
                $empty_refp = array('filescript' => '', 'name' => '--');
        array_push($arrayfile, $empty_refp);
        $arrayfilethreed = $this->Getfiles(_PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/views/js/front/3d/product', 'filethreed');
        $empty_refp = array('filethreed' => '', 'name' => '--');
        array_push($arrayfilethreed, $empty_refp);

        $products_array = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        SELECT p.id_product, CONCAT ( \'#\', p.id_product, \' - \',  pl.name, \' (ref:\', p.reference, \')\') AS product_name
        FROM `' . _DB_PREFIX_ . 'product` p
        INNER JOIN `' . _DB_PREFIX_ . 'product_lang` pl ON (p.`id_product` = pl.`id_product` AND pl.id_lang = ' . (int)$this->context->language->id . ' AND pl.id_shop = ' . (int)$this->context->shop->id . ')
        where p.`id_category_default` != ' . (int)Configuration::get('CPA_CATEGORY') . '
        ORDER BY pl.name ASC');

        $empty_refp = array('id_product' => 0, 'product_name' => '--');
        array_push($products_array, $empty_refp);

        // $type_array = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
        //                     SELECT ct.`id_cpa_customization_field_type`, ct.`name`
        //                     FROM `' . _DB_PREFIX_ . 'cpa_customization_field_type` ct
        //                     ORDER BY ct.`id_cpa_customization_field_type`');

        // $empty_refc = ['id_cpa_customization_field_type' => 0, 'name' => '--'];
        // array_push($type_array, $empty_refc);


        $fields_form = [
            'legend' => [
                'title' => $this->trans('Gerir Campos Customizados', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
            ],
            'submit' => [
                'title' => $this->trans('Gravar', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
            ],
            'input' => [
                [
                    'type' => 'file',
                    'label' => $this->trans('Ficheiro FBX :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'fbx_file',
                    'desc' =>  $this->trans('Adicione aqui os ficheiros FBX de modelos 3D', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'attr' => [
                        'id' => 'fbx_file'
                    ],
                ],
                [
                    'type' => 'html',
                    'name' => 'custom_js_input',
                    'label' => $this->trans('Novo ficheiro JS :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'html_content' => '
                        <div class="form-inline">
                            <input type="text" id="js_filename" name="js_filename" placeholder="' . $this->trans('Nome do ficheiro...', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin') . '" class="form-control fixed-width-lg" />
                            <div id="create_js_file" class="btn btn-default">
                                <i class="icon-plus-sign"></i> Criar
                            </div>
                        </div>
                    ',
                    'desc' =>  $this->trans('Adicione nome do ficheiro JS', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Produto :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'id_product',
                    'required' => true,
                    'class' => 'chosen',
                    'desc' => $this->trans('Produto.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'options' => [
                        'query' => $products_array,
                        'id'    => 'id_product',
                        'name'  => 'product_name'
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Ficheiro :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'filescript',
                    'class' => 'fixed-width-xs ',
                    'options' => [
                        'query' => $arrayfile,
                        'id' => 'filescript',
                        'name' => 'name'
                    ],
                    'desc' => $this->trans('Escolha se o aumento do campo será em valor absoluto ou percentual.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin')
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Ficheiro 3D:', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'filesthreed',
                    'class' => 'fixed-width-xs ',
                    'options' => [
                        'query' => $arrayfilethreed,
                        'id' => 'filethreed',
                        'name' => 'name'
                    ],
                    'desc' => $this->trans('Escolha se o aumento do campo será em valor absoluto ou percentual.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin')
                ],


            ],
        ];
        $this->fields_form = $fields_form;
    }

    public function processSave()
    {

        $id = (int)Tools::getValue('id_cpa_customization_product');
        $object = $id ? new CpaProduct($id) : new CpaProduct();
        $object->id_product     = Tools::getValue('id_product');
        $object->filescript     = Tools::getValue('filescript');
        $object->filesthreed     = Tools::getValue('filesthreed');
        if ($object->save()) {
            $this->confirmations[] = $this->trans('Campo gravado com sucesso', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
            $this->redirect_after = self::$currentIndex . '&token=' . $this->token . '&conf=3';
        } else {
            $this->errors[] = $this->trans('Erro ao gravar o campo', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
        }
        return $object;
    }
}
