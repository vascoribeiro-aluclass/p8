<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminCpaPorductController extends ModuleAdminController
{
    public function __construct()
    {
         parent::__construct();

        $this->bootstrap = true;
        $this->table = 'cpa_customization_product';
        $this->className = 'CpaProduct';
        $this->_defaultOrderBy = 'id_cpa_customization_product';
        $this->_default_pagination = '50';
        $this->identifier = 'id_cpa_customization_product';
   
        $this->_select = "pl.name AS pl_name, ";
        $this->_join   = "INNER JOIN `" . _DB_PREFIX_ . "cpa_customization_field_product` cfp on  a.`id_product` = cfp.`id_product` ";
        $this->_join   = "INNER JOIN `" . _DB_PREFIX_ . "product_lang` pl on  a.`id_product` = pl.`id_product` and pl.id_lang = " . (int)$this->context->language->id ." and pl.id_shop = 1";
 

     $this->bulk_actions = [
            'delete' => [
                'text' => $this->trans('Delete selected', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin'),
                'icon' => 'icon-trash',
                'confirm' => $this->trans('Delete selected items?', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin')
            ]
        ];

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
}
