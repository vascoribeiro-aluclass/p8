<?php
if (!defined('_PS_VERSION_')) {
  exit;
}

require_once _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/models/CpaCf.php';
require_once _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/models/CpaCfv.php';

class CpaCustomizadorProdutosAluclass extends Module
{

  public function __construct()
  {
    $this->name = 'cpacustomizadorprodutosaluclass';
    $this->tab = 'front_office_features';
    $this->version = '1.0.0';
    $this->author = 'Aluclass';
    $this->ps_versions_compliancy = ['min' => '8.0.0', 'max' => _PS_VERSION_];
    $this->bootstrap = true;
    parent::__construct();
    $this->displayName = $this->l('CPA Customizador Produtos Aluclass');
    $this->description = $this->l('Criação de campos personalizados com preço.');
  }

  public function install()
  {
    require_once $this->local_path  . 'install/sql/uninstall.php';
    $id_tab = Tab::getIdFromClassName('AdminCpaCustomizadorProdutosAluclassNo');

    if ($id_tab > 0) {
      $this->uninstallModuleTab('AdminCpaPorduct', $id_tab);
      $this->uninstallModuleTab('AdminCpaCustomization', $id_tab);
      $this->uninstallModuleTab('AdminCpaCustomizationValue', -1);
      $this->uninstallModuleTab('AdminCpaCustomizadorProdutosAluclassNo', 0);
    }

    if (!is_dir(_PS_IMG_DIR_ . 'scenes/' . 'cpa/'))
        mkdir(_PS_IMG_DIR_ . 'scenes/' . 'cpa/', 0777);

    if (!is_dir(_PS_IMG_DIR_ . 'scenes/' . 'cpa/thumbs/'))
        mkdir(_PS_IMG_DIR_ . 'scenes/' . 'cpa/thumbs/', 0777);

    $this->installModuleTab('AdminCpaCustomizadorProdutosAluclassNo', array((int)$this->context->language->id => 'Gerir Campos Customizados'), 0);
     $id_tab = Tab::getIdFromClassName('AdminCpaCustomizadorProdutosAluclassNo');
    
    require_once $this->local_path  . 'install/sql/install.php';

    return parent::install()
      && $this->registerHook('Header')
      && $this->registerHook('displayRightColumnProduct')
      && $this->installModuleTab('AdminCpaPorduct', array((int)$this->context->language->id => 'Produtos Customizados'), $id_tab)
      && $this->installModuleTab('AdminCpaCustomization', array((int)$this->context->language->id => 'Gerir Campos Customizados'), $id_tab)
      && $this->installModuleTab('AdminCpaCustomizationValue', array((int)$this->context->language->id => 'Gerir Valores Campos Customizados'), -1);
  }

  public function uninstall()
  {
    $id_tab = Tab::getIdFromClassName('AdminCpaCustomizadorProdutosAluclassNo');
    if (!parent::uninstall() || !$this->uninstallModuleTab('AdminCpaCustomizadorProdutosAluclassNo', $id_tab) || !$this->uninstallModuleTab('AdminCpaPorduct', $id_tab) || !$this->uninstallModuleTab('AdminCpaCustomization', $id_tab) || !$this->uninstallModuleTab('AdminCpaCustomizationValue', -1))
      return false;
    else
      return true;
  }


  private function installModuleTab($tabClass, $tabName, $idTabParent)
  {
    $tab = new Tab();

    $languages = Language::getLanguages(false);
    // ensure name is provided for all languages
    foreach ($languages as $lang) {
      $tab->name[$lang['id_lang']] = isset($tabName[$lang['id_lang']])
        ? $tabName[$lang['id_lang']]
        : $tabName[(int)$this->context->language->id];
    }

    $tab->class_name = $tabClass;
    $tab->module = $this->name;
    $tab->id_parent = (int)$idTabParent;
    $tab->active = 1;

    // use add() to insert the tab and check if it succeeded
    if ($tab->add() && (int)$tab->id > 0) {
      return true;
    }

    return false;
  }

  private function uninstallModuleTab($tabClass, $idTabParent)
  {
    require_once $this->local_path  . 'install/sql/uninstall.php';
    $idTab = Tab::getIdFromClassName($tabClass);
    if ($idTab != 0) {
      $tab = new Tab($idTab);
      $tab->delete();
      return true;
    }
    return false;
  }


}
