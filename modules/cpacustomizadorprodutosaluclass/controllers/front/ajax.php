<?php

require_once _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/classes/CpaProcessProduct.php';

class cpacustomizadorprodutosaluclassajaxModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        if ($this->ajax) {
            $this->displayAjax();
        }
    }

    public function displayAjax()
    {
        $action = Tools::getValue('action');
        $result = ['success' => false];
        switch ($action) {
            case 'ProcessCPADimensions':
                $dimensions  = Tools::getValue('dimensions');

                $price = Db::getInstance()->getValue("SELECT price
                        FROM " . _DB_PREFIX_ . "cpa_customization_field_csv
                        WHERE 
                            width  >= " . (int)$dimensions['width'] . " AND
                            height >= " . (int)$dimensions['height'] . " AND
                            depth  >= " . (int)$dimensions['depth'] . "
                        ORDER BY 
                            POW(width - " . (int)$dimensions['width'] . ",2) +
                            POW(height - " . (int)$dimensions['height'] . ",2) +
                            POW(depth - " . (int)$dimensions['depth'] . ",2)
                        ASC
                        ");

                $result = [
                    'success' => true,
                    'message' => 'Success',
                    'data' => $price
                ];
                break;

            case 'ProcessCPAProduct':
                $datacustom = Tools::getValue('datacustom');
                $resultproduct = [];

                $tokencpa = (array_key_exists('tokencpa', $datacustom) ? $datacustom['tokencpa'] : false);

                $cpaProcessProduct = new CpaProcessProduct($datacustom['id_product'], $datacustom['cpafields'], $tokencpa);
                $resultproduct = $cpaProcessProduct->init();

                $result = [
                    'success' => true,
                    'message' => 'Success',
                    'data' => $resultproduct
                ];
                break;

            case 'ProcessCPABudget':
                $datacustom = Tools::getValue('datacustom');
                $resultproduct = [];

                $cpaProcessBudget = new CpaProcessBudget($datacustom['id_product'], $datacustom['cpafields']);
                $resultproduct = $cpaProcessBudget->init();

                $result = [
                    'success' => true,
                    'message' => 'Success',
                    'data' => $resultproduct
                ];
                break;

                case 'ProcessCPAShare':
                $datacustom = Tools::getValue('datacustom');
                $resultproduct = [];

                $cpaProcessBudget = new CpaProcessBudget($datacustom['id_product'], $datacustom['cpafields']);
                $resultproduct = $cpaProcessBudget->init();

                $result = [
                    'success' => true,
                    'message' => 'Success',
                    'data' => $resultproduct
                ];
                break;
        }


        // Retorna o JSON e encerra a execução
        header('Content-Type: application/json');
        die(json_encode($result));
    }
}
