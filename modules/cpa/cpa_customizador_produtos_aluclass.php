<?php

class cpa_customizador_produtos_aluclass extends Module
{

  public function __construct()
  {
    $this->name = 'cpa_customizador_produtos_aluclass';
    $this->tab = 'front_office_features';
    $this->version = '1.0.0';
    $this->author = 'Aluclass';
    $this->displayName = $this->l('CPA Customizador Produtos Aluclass');
    $this->description = $this->l('Criação de campos personalizados com preço.');

    parent::__construct();
  }

  public function hookStandard()
  {
    return $this->registerHook('displayRightColumnProduct');
  }

  public function install()
  {
       
    $id_tab = Tab::getIdFromClassName('AdminCpaCustomizadorProdutosAluclassNo');
    if ($id_tab > 0) {
      $this->uninstallModuleTab('AdminCpaPorduct', $id_tab);
      $this->uninstallModuleTab('AdminCpaCustomization', $id_tab);
      $this->uninstallModuleTab('AdminCpaCustomizadorProdutosAluclassNo', 0);
    }


    $this->installModuleTab('AdminCpaCustomizadorProdutosAluclassNo', array((int)$this->context->language->id => 'Gerir Campos Customizados'), 0);

    require_once _PS_MODULE_DIR_ . 'install/sql/install.php';

    return parent::install()
      && $this->registerHook('Header')
      && $this->registerHook('displayRightColumnProduct')
      && $this->installModuleTab('AdminCpaPorduct', array((int)$this->context->language->id => 'Produtos Customizados'), $id_tab)
      && $this->installModuleTab('AdminCpaCustomization', array((int)$this->context->language->id => 'Gerir Campos Customizados'), $id_tab);
  }

  public function uninstall()
  {
    $id_tab = Tab::getIdFromClassName('AdminCpaCustomizadorProdutosAluclassNo');
    if (!parent::uninstall() || !$this->uninstallModuleTab('AdminCpaCustomizadorProdutosAluclassNo', $id_tab) || !$this->uninstallModuleTab('AdminCpaPorduct', $id_tab) || !$this->uninstallModuleTab('AdminCpaCustomization', $id_tab))
      return false; 
    else
      return true;
  }


  private function installModuleTab($tabClass, $tabName, $idTabParent)
  {
    $tab = new Tab();

    $langues = Language::getLanguages(false);
    foreach ($langues as $langue)
      $tabName[$langue['id_lang']] = $tabName[(int)$this->context->language->id];


    $tab->name = $tabName;
    $tab->class_name = $tabClass;
    $tab->module = $this->name;
    $tab->id_parent = $idTabParent;
    $id_tab = $tab->save();
    if (!$id_tab)
      return false;

    return true;
  }

  private function uninstallModuleTab($tabClass, $idTabParent)
  {
    require_once _PS_MODULE_DIR_ . 'install/sql/uninstall.php';
    $idTab = Tab::getIdFromClassName($tabClass);
    if ($idTab != 0) {
      $tab = new Tab($idTab);
      $tab->delete();
      return true;
    }
    return false;
  }
}
