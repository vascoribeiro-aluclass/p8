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
                'text' => $this->module->l('Excluir selecionado', 'AdminCpaPorduct'),
                'icon' => 'icon-trash',
                'confirm' => $this->module->l('Excluir itens selecionados?', 'AdminCpaPorduct')
            ]
        ];

        $this->_select = "pl.name AS pl_name, ";
        $this->_join   = "INNER JOIN `" . _DB_PREFIX_ . "product_lang` pl on  a.`id_product` = pl.`id_product` and pl.id_lang = " . (int)$this->context->language->id . " and pl.id_shop = 1";


        $this->fields_list = [
            'id_cpa_customization_product' => [
                'title' => $this->module->l('ID', 'AdminCpaPorduct'),
                'align' => 'center',
                'width' => 25,
            ],

            'pl_name' => [
                'title' => $this->module->l('Nome Produto ', 'AdminCpaPorduct'),
                'filter_key' => 'pl!name',
                'width' => 250,

            ],
            'filescript' => [
                'title' => $this->module->l('Ficheiro Javascript', 'AdminCpaPorduct'),
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
            'text_error_progress' => $this->module->l('Erro crítico na comunicação com o servidor.', 'AdminCpaPorduct'),
            'text_error_nothing' => $this->module->l('Falta o nome do ficheiro.', 'AdminCpaPorduct'),
            'text_error_filefbx' => $this->module->l('Só são permitidos ficheiros FBX..', 'AdminCpaPorduct')
        ]);


        $this->addJS($this->module->getPathUri() . 'views/js/admin/cpaproduct.js');
    }

    public function ajaxProcessCreateFileCPA()
    {
        $arrayrespond = ['success' => false, 'msn' => ''];
        $folder = _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/views/js/front/product';

        $name = preg_replace('/[^a-zA-Z0-9_-]/', '', Tools::getValue('name'));

        if (empty($name)) {
            $arrayrespond['msn'] = $this->module->l('Nome inválido.', 'AdminCpaPorduct');
            die(json_encode($arrayrespond));
        }

        $path = $folder . "/" . $name . ".js";

        if (!file_exists($path)) {
            $content = "// Ficheiro JS criado automaticamente\n";

            if (file_put_contents($path, $content)) {
                $arrayrespond['msn'] = sprintf($this->module->l('Ficheiro criado: %s.js.',  'AdminCpaPorduct'),$name);
                $arrayrespond['success'] = true;
            } else {
                $arrayrespond['msn'] = $this->module->l('Erro ao gravar o ficheiro no servidor.', 'AdminCpaPorduct');
            }
        } else {
            $arrayrespond['msn'] = sprintf($this->module->l('Ficheiro: %s.js já existe.', 'AdminCpaPorduct'),$name);
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
                $arrayrespond['msn'] = sprintf($this->module->l('Ficheiro %s enviado com sucesso.', 'AdminCpaPorduct'), $file['name']);
                $arrayrespond['success'] = true;
            } else {
                $arrayrespond['msn'] = sprintf($this->module->l('Erro ao enviar ficheiro %s.', 'AdminCpaPorduct'), $file['name']);
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
                'title' => $this->module->l('Gerir Campos Customizados', 'AdminCpaPorduct'),
            ],
            'submit' => [
                'title' => $this->module->l('Gravar', 'AdminCpaPorduct'),
            ],
            'input' => [
                [
                    'type' => 'file',
                    'label' => $this->module->l('Ficheiro FBX :', 'AdminCpaPorduct'),
                    'name' => 'fbx_file',
                    'desc' =>  $this->module->l('Adicione aqui os ficheiros FBX de modelos 3D', 'AdminCpaPorduct'),
                    'attr' => [
                        'id' => 'fbx_file'
                    ],
                ],
                [
                    'type' => 'html',
                    'name' => 'custom_js_input',
                    'label' => $this->module->l('Novo ficheiro JS :', 'AdminCpaPorduct'),
                    'html_content' => '
                        <div class="form-inline">
                            <input type="text" id="js_filename" name="js_filename" placeholder="' . $this->module->l('Nome do ficheiro...', 'AdminCpaPorduct') . '" class="form-control fixed-width-lg" />
                            <div id="create_js_file" class="btn btn-default">
                                <i class="icon-plus-sign"></i> Criar
                            </div>
                        </div>
                    ',
                    'desc' =>  $this->module->l('Adicione nome do ficheiro JS', 'AdminCpaPorduct'),
                ],
                [
                    'type' => 'select',
                    'label' => $this->module->l('Produto :', 'AdminCpaPorduct'),
                    'name' => 'id_product',
                    'required' => true,
                    'class' => 'chosen',
                    'desc' => $this->module->l('Produto.', 'AdminCpaPorduct'),
                    'options' => [
                        'query' => $products_array,
                        'id'    => 'id_product',
                        'name'  => 'product_name'
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->module->l('Ficheiro :', 'AdminCpaPorduct'),
                    'name' => 'filescript',
                    'class' => 'fixed-width-xs ',
                    'options' => [
                        'query' => $arrayfile,
                        'id' => 'filescript',
                        'name' => 'name'
                    ],
                    'desc' => $this->module->l('Escolha se o aumento do campo será em valor absoluto ou percentual.', 'AdminCpaPorduct')
                ],
                [
                    'type' => 'select',
                    'label' => $this->module->l('Ficheiro 3D:', 'AdminCpaPorduct'),
                    'name' => 'filesthreed',
                    'class' => 'fixed-width-xs ',
                    'options' => [
                        'query' => $arrayfilethreed,
                        'id' => 'filethreed',
                        'name' => 'name'
                    ],
                    'desc' => $this->module->l('Escolha se o aumento do campo será em valor absoluto ou percentual.', 'AdminCpaPorduct')
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
            $this->confirmations[] = $this->module->l('Campo gravado com sucesso', 'AdminCpaPorduct');
            $this->redirect_after = self::$currentIndex . '&token=' . $this->token . '&conf=3';
        } else {
            $this->errors[] = $this->module->l('Erro ao gravar o campo', 'AdminCpaPorduct');
        }
        return $object;
    }
}
