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

        if ($action === 'ProcessCPAProduct') {
            $datacustom  = Tools::getValue('datacustom');

            $cpaProcessProduct = new CpaProcessProduct($datacustom['id_product'], $datacustom['cpafields']);
            $resultproduct = $cpaProcessProduct->init();

            $result = [
                'success' => true,
                'message' => 'Pedido AJAX processado com sucesso!',
                'data' => $resultproduct
            ];
        }

        // Retorna o JSON e encerra a execução
        header('Content-Type: application/json');
        die(json_encode($result));
    }
}
