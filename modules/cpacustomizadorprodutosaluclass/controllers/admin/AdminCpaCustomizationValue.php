<?php

class AdminCpaCustomizationValueController extends ModuleAdminController
{
    public $bootstrap = true;
    public $id_cpa_customization_field = false;

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


        $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Delete selected items?', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin')
            ]
        ];

        $this->_select = "cl.name AS cf_name ";
        $this->_join = "INNER JOIN `" . _DB_PREFIX_ . "cpa_customization_field_value_lang` cl on  a.`id_cpa_customization_field_value` = cl.`id_cpa_customization_field_value` and cl.id_lang = " . (int)$this->context->language->id;
        $this->_where = ' and a.id_cpa_customization_field = ' . $this->id_cpa_customization_field;

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
                ],

                [
                    'type' => 'text',
                    'label' => $this->trans('Preço :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'price',
                    'required' => true,
                    'size' => 8,

                ],
                [
                    'type' => 'text',
                    'label' => $this->trans('Preço de Custo :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'cost_price',
                    'required' => false,
                    'size' => 8,
                ],

                [
                    'type' => 'switch',
                    'label' => $this->trans('Visível :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'isvisivel',
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
                    'type' => 'text',
                    'label' => $this->trans('Posição :', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                    'name' => 'position',
                    'required' => true,
                    'attributes' => [
                        'type' => 'number',
                        'min' => 0,
                    ],
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

        $object->price = (float)Tools::getValue('price');
        $object->cost_price = (float)Tools::getValue('cost_price');
        $object->isvisivel = (int)Tools::getValue('isvisivel');
        $object->position = (int)Tools::getValue('position');

        if ($object->save()) {

            $this->confirmations[] = $this->trans('Valor guardado com sucesso', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin');

            $shops = Tools::getValue('checkBoxShopAsso_' . $this->table, []);
            Db::getInstance()->delete(
                $this->table . '_shop',
                'id_' . $this->table . ' = ' . (int)$object->id
            );
            $object->associateTo($shops);

            Tools::redirectAdmin($this->context->link->getAdminLink('AdminCpaCustomizationValue') . '&id_cpa_customization_field=' . (int)$object->id_cpa_customization_field);
        } else {

            $this->errors[] = $this->trans(
                'Erro ao guardar o valor',
                [],
                'Modules.Cpacustomizadorprodutosaluclass.Admin'
            );
        }
    }
}
