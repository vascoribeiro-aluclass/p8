<?php
if (!defined('_PS_VERSION_')) {
  exit;
}

require_once _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/classes/CpaCsvImporter.php';
require_once _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/classes/CpaFields.php';
require_once _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/classes/CpaProcessFields.php';
require_once _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/classes/CpaProcessProduct.php';
require_once _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/classes/CpaTypeSelectorImages.php';
require_once _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/classes/CpaTypeSelectorRadio.php';
require_once _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/classes/CpaTypeAccessQty.php';
require_once _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/classes/CpaTypeDimensions.php';
require_once _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/models/CpaCf.php';
require_once _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/models/CpaCfv.php';
require_once _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/install/sql/install.php';
require_once _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/install/sql/uninstall.php';
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

    $id_tab = Tab::getIdFromClassName('AdminCpaCustomizadorProdutosAluclassNo');

    if ($id_tab > 0) {
      $this->uninstallModuleTab('AdminCpaPorduct', $id_tab);
      $this->uninstallModuleTab('AdminCpaCustomization', $id_tab);
      $this->uninstallModuleTab('AdminCpaCustomizationValue', -1);
      $this->uninstallModuleTab('AdminCpaCustomizadorProdutosAluclassNo', 0);
    }


    $this->installModuleTab('AdminCpaCustomizadorProdutosAluclassNo', array((int)$this->context->language->id => 'Gerir Campos Customizados'), 0);
    $id_tab = Tab::getIdFromClassName('AdminCpaCustomizadorProdutosAluclassNo');


    installCPASQL::init();
    return parent::install()
      && $this->registerHook('Header')
      && $this->registerHook('displayReassurance')
      && $this->registerHook('displayProductPriceBlock')
      && $this->registerHook('actionProductSave')
      && $this->registerHook('actionProductUpdate')
      && $this->installModuleTab('AdminCpaPorduct', array((int)$this->context->language->id => 'Produtos Customizados'), $id_tab)
      && $this->installModuleTab('AdminCpaCustomization', array((int)$this->context->language->id => 'Gerir Campos Customizados'), $id_tab)
      && $this->installModuleTab('AdminCpaCustomizationValue', array((int)$this->context->language->id => 'Gerir Valores Campos Customizados'), -1)
      && Configuration::updateValue('CPA_CATEGORY', '0')
      && $this->createCPACategory();
  }

  public function uninstall()
  {
    uninstallCPASQL::init();
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

    foreach ($languages as $lang) {
      $tab->name[$lang['id_lang']] = isset($tabName[$lang['id_lang']])
        ? $tabName[$lang['id_lang']]
        : $tabName[(int)$this->context->language->id];
    }

    $tab->class_name = $tabClass;
    $tab->module = $this->name;
    $tab->id_parent = (int)$idTabParent;
    $tab->active = 1;

    if ($tab->add() && (int)$tab->id > 0) {
      return true;
    }

    return false;
  }

  private function uninstallModuleTab($tabClass, $idTabParent)
  {

    $idTab = Tab::getIdFromClassName($tabClass);
    if ($idTab != 0) {
      $tab = new Tab($idTab);
      $tab->delete();
      return true;
    }
    return false;
  }

  public function hookHeader($params)
  {

    if (Tools::getValue('controller') == 'product') {
      $id_product = (int)Tools::getValue('id_product');

      if ($this->checkCPAProduct($id_product) == 0) {
        return '';
      }

      Media::addJsDef(array(
        'url_ajax_cpacustomizadorprodutosaluclass' => $this->context->link->getModuleLink('cpacustomizadorprodutosaluclass', 'ajax')
      ));

      $this->context->controller->registerStylesheet(
        'module-cpa-theme-style',
        'modules/' . $this->name . '/views/css/front/theme.css',
        [
          'media' => 'all',
          'priority' => 800,
        ]
      );

      $this->context->controller->registerJavascript(
        'module-cpa-theme-js',
        'modules/' . $this->name . '/views/js/front/theme.js',
        [
          'position' => 'bottom',
          'priority' => 800,
        ]
      );

      $product = new Product($id_product, false, (int)$this->context->language->id, (int)$this->context->shop->id);
      $tax_rate = Tax::getProductTaxRate($product->id);

      Media::addJsDef([
        'ivaProduct' => $tax_rate,
      ]);

      $this->context->controller->registerJavascript(
        'module-cpa-calculeprice-js',
        'modules/' . $this->name . '/views/js/front/calculeprice.js',
        [
          'position' => 'bottom',
          'priority' => 850,
        ]
      );
    }
  }

  public function hookDisplayProductPriceBlock($params)
  {
    if (Tools::getValue('controller') == 'product') {

      if ($params['type'] == 'custom_price') {
        $id_product = (int)Tools::getValue('id_product');

        $product = new Product($id_product, false, (int)$this->context->language->id, (int)$this->context->shop->id);

        $this->context->smarty->assign(
          [
            'price' => $product->getPrice(true),
            'price_tax_exc' => $product->getPrice(false),
          ]
        );

        return $this->display(__FILE__, 'views/hook/price.tpl');
      }
    }
  }

  public function hookDisplayReassurance($params)
  {
    if (Tools::getValue('controller') == 'product') {
      $id_product = (int)Tools::getValue('id_product');

      if ($this->checkCPAProduct($id_product) == 0) {
        return '';
      }

      $key_cache = $id_product . '_' . (int)$this->context->language->id . '_' . (int)$this->context->shop->id;
      $expire = time() + 43200;

      $search_cache = 'SELECT * FROM ' . _DB_PREFIX_ . 'cpa_customization_field_cache 
                        WHERE key_cache = "' . $key_cache . '" AND expire > ' . time();

      $cache_found =  Db::getInstance()->getRow($search_cache);
      if (is_array($cache_found)) {
        if (sizeof($cache_found) > 0 && $cache_found['key_cache'] == $key_cache && $cache_found['expire'] > time()) {
          $this->context->smarty->assign('template', $cache_found['content']);
          return $this->display(__FILE__, 'views/hook/cpa_cache.tpl');
        }
      }


      $resultsfields = CpaProcessFields::init($id_product);

      $htmlFields = '';

      foreach ($resultsfields as $field) {
        switch ($field['type_class']) {
          case "cpatypeselectorimages":
            $fieldObj = new CpaTypeSelectorImages($field, $id_product);

            $this->context->smarty->assign($fieldObj->getAssign());
            $htmlFields .= $this->display(__FILE__, $fieldObj->getTemplate());

            break;

          case "cpatypeselectorradio":
            $fieldObj = new CpaTypeSelectorRadio($field, $id_product);

            $this->context->smarty->assign($fieldObj->getAssign());
            $htmlFields .= $this->display(__FILE__, $fieldObj->getTemplate());

            break;
          case "cpatypeaccessquant":
            $fieldObj = new CpaTypeAccessQty($field, $id_product);

            $this->context->smarty->assign($fieldObj->getAssign());
            $htmlFields .= $this->display(__FILE__, $fieldObj->getTemplate());

            break;
          case "cpatypedimensions":
            $fieldObj = new CpaTypeDimensions($field, $id_product);

            $this->context->smarty->assign($fieldObj->getAssign());
            $htmlFields .= $this->display(__FILE__, $fieldObj->getTemplate());

            break;
        }
      }

      $this->context->smarty->assign(
        [
          'id_product' => $id_product,
          'htmlFields' => $htmlFields
        ]
      );
      $template = $this->display(__FILE__, 'views/hook/cpa.tpl');


      Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'cpa_customization_field_cache WHERE key_cache = "' . $key_cache . '"');

      $setCache = 'INSERT INTO ' . _DB_PREFIX_ . 'cpa_customization_field_cache (key_cache, content, expire) VALUES ("' . pSQL($key_cache) . '", "' . pSQL($template, true) . '", ' . $expire . ')';
      Db::getInstance()->execute($setCache);

      return $template;
    }
  }

  private function checkCPAProduct($idProduct)
  {

    return Db::getInstance()->getValue(
      (new DbQuery())
        ->select('COUNT(id_cpa_customization_field)')
        ->from('cpa_customization_field_product')
        ->where('id_product = ' . (int)$idProduct)
    );
  }

  private function createCPACategory(): bool
  {
    $db = Db::getInstance();

    $query = new DbQuery();
    $query->select('id_category');
    $query->from('category_lang');
    $query->where('name = "' . pSQL($this->name) . '"');

    $results = $db->executeS($query);

    $idCategoryRoot = (int) $db->getValue(
      (new DbQuery())
        ->select('id_category')
        ->from('category')
        ->where('is_root_category = 1')
    );

    if (count($results) === 0) {

      $languages = Language::getLanguages(false);

      $category = new Category();
      $category->active = 0;
      $category->id_parent = $idCategoryRoot;

      foreach ($languages as $lang) {
        $idLang = (int) $lang['id_lang'];

        $category->name[$idLang] = $this->name;
        $category->link_rewrite[$idLang] = Tools::link_rewrite($this->name);
      }

      $category->add();

      Configuration::updateValue('CPA_CATEGORY', (int) $category->id);
    } else {

      $idCategory = (int) $results[0]['id_category'];

      Configuration::updateValue('CPA_CATEGORY', $idCategory);

      $category = new Category($idCategory);
      $category->id_parent = $idCategoryRoot;
      $category->update();
    }

    return true;
  }
}
