<?php

class AdminCpaCustomizationController extends ModuleAdminController
{
    public $bootstrap = true;

    public function __construct()
    {
        parent::__construct();

        $this->bootstrap = true;
        $this->table = 'cpa_customization_field';
        $this->className = 'CpaCf';
        $this->lang = true;

        $this->explicitSelect = true;
        $this->allow_export = true;
        $this->_defaultOrderBy = 'position';
        $this->_default_pagination = '50';
        $this->identifier = 'id_cpa_customization_field';

        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Excluir selecionado', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Excluir itens selecionados?', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin')
            ]
        ];

        $this->_select = "cl.name AS cf_name , ct.name AS type_name ";
        $this->_join = "INNER JOIN `" . _DB_PREFIX_ . "cpa_customization_field_type` ct on  ct.`id_cpa_customization_field_type` = a.`id_cpa_customization_field_type`  ";
        $this->_join .= "INNER JOIN `" . _DB_PREFIX_ . "cpa_customization_field_lang` cl on  a.`id_cpa_customization_field` = cl.`id_cpa_customization_field` and cl.id_lang = " . (int)$this->context->language->id;


        $this->fields_list = [
            'id_cpa_customization_field' => [
                'title' => $this->trans('ID', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'align' => 'center',
                'width' => 25,

            ],

            'admin_name' => [
                'title' => $this->trans('Nome administrativo', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'filter_key' => 'a!admin_name'
            ],

            'cf_name' => [
                'title' => $this->trans('Nome público', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'filter_key' => 'cl!name',

            ],
            'type_name' => [
                'title' => $this->trans('Tipo', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
            ],
            'required' => [
                'title' => $this->trans('Requisito', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'active' => 'required',
                'type' => 'bool',
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'orderby' => false
            ],

            'position' => [
                'title' => $this->trans('Posição', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'filter_key' => 'a!position',
                'align' => 'center',
                'class' => 'fixed-width-xs editable-value set_positionndk_customization_field',
            ],
            'order_position' => [
                'title' => $this->trans('Ordem Posição', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'filter_key' => 'a!order_position',
                'align' => 'center',
                'class' => 'fixed-width-xs editable-value set_ref_positionndk_customization_field',
            ],
            'zindex' => [
                'title' => $this->trans('Z-index', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'filter_key' => 'a!zindex',
                'align' => 'center',
                'class' => 'fixed-width-xs editable-value set_zindexndk_customization_field'
            ],
        ];
    }

    public function setMedia($isNewTheme = false)
    {
        parent::setMedia($isNewTheme);

        $this->addJqueryPlugin('select2');

        $cpaProducts = [];
        $cpafieldsInfluence = [];

        foreach ($this->getSavedProductsDetailed((int)Tools::getValue('id_cpa_customization_field')) as $row) {
            $cpaProducts[] = [
                'id' => (int)$row['id_product'],
                'text' => $row['name'] . ' (Ref: ' . $row['reference'] . ')'
            ];
        }

        foreach ($this->getInfluenceFieldCPA((int)Tools::getValue('id_cpa_customization_field')) as $row) {
            $cpafieldsInfluence[] = [
                'id' => (int)$row['id_cpa_customization_field'],
                'text' => $row['admin_name']
            ];
        }


        Media::addJsDef([
            'ajaxProductUrl' => $this->context->link->getAdminLink('AdminCpaCustomization', true, [], ['action' => 'SearchProductsCPA', 'ajax' => 1]),
            'ajaxFieldsUrl' => $this->context->link->getAdminLink('AdminCpaCustomization', true, [], ['action' => 'FieldsCPA', 'ajax' => 1]),
            'already_selected_products' => $cpaProducts,
            'already_selected_fields_influence' => $cpafieldsInfluence,
            'select2_translations' => [
                'inputTooShort' => $this->trans('Introduza pelo menos %d caracteres', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'noMatches' => $this->trans('Nenhum resultado encontrado', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'searching' => $this->trans('A pesquisar...', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'searchingProducts' => $this->trans('A pesquisar produtos...', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
            ]
        ]);

        $this->addJS($this->module->getPathUri() . 'views/js/admin/cpacustomization.js');
    }

    public function getInfluenceFieldCPA($id_cpa_customization_field)
    {
        $sql = new DbQuery();
        $sql->select('p.id_cpa_customization_field, p.admin_name');
        $sql->from('cpa_customization_field', 'p');

        $sql->innerJoin(
            'cpa_customization_field_influences',
            'cfv_inf',
            'p.id_cpa_customization_field = cfv_inf.id_cpa_customization_field_influence AND cfv_inf.id_cpa_customization_field = ' . $id_cpa_customization_field
        );

        return Db::getInstance()->executeS($sql);
    }

    public function getSavedProductsDetailed($id_cpa_customization_field)
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
            'cpa_customization_field_product',
            'cfp',
            'cfp.id_product = p.id_product AND cfp.id_cpa_customization_field = ' . (int)$id_cpa_customization_field
        );

        return Db::getInstance()->executeS($sql);
    }
    public function ajaxProcessFieldsCPA()
    {
        $q = Tools::getValue('q');

        $sql = new DbQuery();
        $sql->select('p.id_cpa_customization_field, p.admin_name');
        $sql->from('cpa_customization_field', 'p');
        $sql->where('p.admin_name LIKE "%' . pSQL($q) . '%"');
        $sql->limit(20);

        $products = Db::getInstance()->executeS($sql);


        die(json_encode($products));
    }

    public function ajaxProcessSearchProductsCPA()
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

        //     $q = Tools::getValue('q'); // O termo de pesquisa que vem do front-end
        //     $id_lang = (int)$this->context->language->id;

        //     if (empty($q) || strlen($q) < 3) {
        //         die(json_encode([]));
        //     }
        //     // O método find retorna um array com 'result' (os produtos) e 'total'
        //     $search_results = Search::find(
        //         $id_lang, 
        //         $q, 
        //         1,          // Página 1
        //         20,         // Limite de 20 resultados
        //         'name',     // Ordenar por nome
        //         'asc',      // Ordem ascendente
        //         false,      // Não precisamos do count total detalhado
        //         true,       // Apenas produtos ativos
        //         $this->context        // Contexto (usa o atual por defeito)
        //     );

        //     $products = (isset($search_results['result']) && is_array($search_results['result'])) 
        // ? $search_results['result'] 
        // : [];

        die(json_encode($products));
    }

    public function renderList()
    {

        if (Tools::getIsset($this->_filter) && trim($this->_filter) == '')
            $this->_filter = $this->original_filter;

        $this->addRowAction('edit');
        $this->addRowAction('delete');
        $this->addRowAction('details');
        return parent::renderList();
    }

    public function displayDetailsLink($token, $id)
    {
        $link = $this->context->link->getAdminLink('AdminCpaCustomizationValue')
            . '&id_cpa_customization_field=' . (int)$id;

        return '<a href="' . $link . '" title="' . $this->trans('Ver Valores', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin') . '">
                <i class="icon-eye"></i>' . $this->trans('Ver Valores', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin') . '
            </a>';
    }


    public function init()
    {
        CpaCf::setDefaultConfig((int)Tools::getValue('id_cpa_customization_field'));
        parent::init();
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

        $type_array = Db::getInstance(_PS_USE_SQL_SLAVE_)->ExecuteS('
                            SELECT ct.`id_cpa_customization_field_type`, ct.`name`
                            FROM `' . _DB_PREFIX_ . 'cpa_customization_field_type` ct
                            ORDER BY ct.`id_cpa_customization_field_type`');

        $empty_refc = ['id_cpa_customization_field_type' => 0, 'name' => '--'];
        array_push($type_array, $empty_refc);

        $arrayopenstatus = [
            ['id_open_status' => 0, 'op_name' => $this->trans('Normal', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin')],
            ['id_open_status' => 1, 'op_name' => $this->trans('Aberto', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin')],
            ['id_open_status' => 2, 'op_name' => $this->trans('Fechado', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin')],
        ];
        $arraytypeprice = [
            ['id_price_type' => 'amount', 'name' => $this->trans('Montante', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin')],
            ['id_price_type' => 'percent', 'name' => $this->trans('Percentagem', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin')]
        ];

        $fields_form = [
            'legend' => [
                'title' => $this->trans('Gerir Campos Customizados', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
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
                    'label' => $this->trans('Nome Administrativo :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'admin_name',
                    'desc' => $this->trans('Nome que será usado internamente no painel administrativo.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'required' => true,
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Nome Público :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'name',
                    'desc' => $this->trans('Nome que será exibido ao cliente.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'required' => true,
                    'lang' => true,
                ],

                [
                    'type' => 'textarea',
                    'label' => $this->trans('Nota :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'notice',
                    'desc' => $this->trans('Nota que será exibida ao cliente.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'autoload_rte' => true,
                    'lang' => true,
                ],
                [
                    'type' => 'textarea',
                    'label' => $this->trans('Dica de ajuda :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'tooltip',
                    'desc' => $this->trans('Dica de ajuda que será exibida ao cliente.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'autoload_rte' => true,
                    'lang' => true,
                ],

                [
                    'type' => 'select',
                    'label' => $this->trans('Estado do campo :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'open_status',
                    'desc' => $this->trans('Se o campo está aberto ou fechado.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'required' => true,
                    'class' => 'chosen',
                    'options' => [
                        'query' => $arrayopenstatus,
                        'id' => 'id_open_status',
                        'name' => 'op_name'
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Tipo :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'id_cpa_customization_field_type',
                    'required' => true,
                    'class' => 'chosen',
                    'desc' => $this->trans('Tipo do campo presonalizado.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'options' => [
                        'query' => $type_array,
                        'id' => 'id_cpa_customization_field_type',
                        'name' => 'name'
                    ],
                ],
                [
                    'type' => 'select',
                    'label' => $this->trans('Tipo de preço :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'price_type',
                    'class' => 'fixed-width-xs',
                    'required' => true,
                    'options' => [
                        'query' => $arraytypeprice,
                        'id' => 'id_price_type',
                        'name' => 'name'
                    ],
                    'desc' => $this->trans('Escolha se o aumento do campo será em valor absoluto ou percentual.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin')
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Influências com outros campos:', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'selected_cpa_fields',
                    'class' => 'ajax-cpa-fields-search',
                    'desc' => $this->trans('Você pode especificar um campo para influenciá-lo de acordo com os valores atuais do campo de criação.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin')

                ],

                [
                    'type' => 'switch',
                    'label' => $this->trans('Visual :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'is_visual',
                    'is_bool' => true,
                    'desc' => $this->trans('Se o campo tem um efeito visual (imagem aprecer sobre a iamgem do produto)', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'values' => [
                        [
                            'id' => 'is_visual_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', [], 'Admin.Global')
                        ],
                        [
                            'id' => 'is_visual_off',
                            'value' => 0,
                            'label' => $this->trans('No', [], 'Admin.Global')
                        ]
                    ],
                ],


                [
                    'type' => 'switch',
                    'label' => $this->trans('Visível :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'isvisivel',
                    'desc' => $this->trans('Se o campo é visível', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'is_bool' => true,
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
                    'type' => 'switch',
                    'label' => $this->trans('Requisito :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'required',
                    'is_bool' => true,
                    'desc' => $this->trans('Se o campo é obrigatório', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'values' => [
                        [
                            'id' => 'required_on',
                            'value' => 1,
                            'label' => $this->trans('Yes', [], 'Admin.Global')
                        ],
                        [
                            'id' => 'required_off',
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
                    'desc' => $this->trans('Ordem em que os campos personalizados são apresentados', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'class' => 'integer-field',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Posição da ordem :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'order_position',
                    'required' => true,
                    'desc' => $this->trans('Ordem em que os campos personalizados são desbloqueados.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'class' => 'integer-field',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Z-Index :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'zindex',
                    'required' => true,
                    'desc' => $this->trans('Z-Index da imagem vai ser apresentada da custimização se tiver uma imagem.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'class' => 'integer-field',
                ],
                [
                    'type' => 'file',
                    'label' => $this->trans('Ficheiro CVS:', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'csv',
                    'display_image' => true,
                    'desc' =>  $this->trans('Adicione aqui os ficheiros CSV de preços', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'class' => '',
                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Produtos associados:', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'selected_products',
                    'class' => 'ajax-product-search',
                    'desc' => $this->trans('Ative este campo para estes produtos.', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),

                ]
                // [

            ],
        ];
        $this->fields_form = $fields_form;
    }

    public function processSave()
    {
        $id = Tools::getValue('id_cpa_customization_field'); // Se for edição
        $object = $id ? new CpaCf($id) : new CpaCf();

        $languages = Language::getLanguages(false); // línguas ativas
        foreach ($languages as $lang) {
            $object->name[$lang['id_lang']]    = Tools::getValue('name_' . $lang['id_lang']);
            $object->tooltip[$lang['id_lang']] = Tools::getValue('tooltip_' . $lang['id_lang']);
            $object->notice[$lang['id_lang']]  = Tools::getValue('notice_' . $lang['id_lang']);
        }

        $selected_products      = Tools::getValue('selected_products');
        $selected_cpa_fields    = Tools::getValue('selected_cpa_fields');

        $object->admin_name     = Tools::getValue('admin_name');
        $object->open_status    = Tools::getValue('open_status');
        $object->id_cpa_customization_field_type = Tools::getValue('id_cpa_customization_field_type');
        $object->price_type     = Tools::getValue('price_type');
        $object->is_visual      = Tools::getValue('is_visual') ? 1 : 0;
        $object->isvisivel      = Tools::getValue('isvisivel') ? 1 : 0;
        $object->required       = Tools::getValue('required') ? 1 : 0;
        $object->position       = (int) Tools::getValue('position');
        $object->order_position = (int) Tools::getValue('order_position');
        $object->zindex         = (int) Tools::getValue('zindex');


        if ($object->save()) {
            $shops = Tools::getValue('checkBoxShopAsso_' . $this->table, []);

            Db::getInstance()->delete(
                $this->table . '_shop',
                'id_' . $this->table . ' = ' . (int)$object->id
            );

            $object->associateTo($shops);

            Db::getInstance()->delete(
                $this->table . '_product',
                'id_' . $this->table . ' = ' . (int)$object->id
            );

            if ($selected_products) {
                $selected_products = explode(',', $selected_products);
                foreach ($selected_products as $product_id) {
                    Db::getInstance()->insert(
                        $this->table . '_product',
                        [
                            'id_' . $this->table  => (int)$object->id,
                            'id_product' => (int)$product_id
                        ]
                    );
                }
            }

            Db::getInstance()->delete(
                $this->table . '_influences',
                'id_' . $this->table . ' = ' . (int)$object->id
            );

            if ($selected_cpa_fields) {
                $selected_cpa_fields = explode(',', $selected_cpa_fields);
                foreach ($selected_cpa_fields as $field_id) {
                    Db::getInstance()->insert(
                        $this->table . '_influences',
                        [
                            'id_' . $this->table  => (int)$object->id,
                            'id_cpa_customization_field_influence' => (int)$field_id
                        ]
                    );
                }
            }

            $this->confirmations[] = $this->trans('Campo gravado com sucesso', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminCpaCustomizationValue') . '&id_cpa_customization_field=' . (int)$object->id);
        } else {
            $this->errors[] = $this->trans('Erro ao gravar o campo', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
        }
    }
}
