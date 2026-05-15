<?php

class CpaProcessBudget extends CpaProcessProduct
{
    private $trans;
    public function __construct($id_product, $datacustom)
    {
        parent::__construct($id_product, $datacustom, false);
        $this->trans = Context::getContext()->getTranslator();
    }

    public function init()
    {

        $arrayFieldsTemp = $this->checkFields();
        if (!$arrayFieldsTemp) {
            return false;
        }

        $cpaCustomValue = [];

        $arrayFields = $this->orderFields($arrayFieldsTemp);

        // Aplica pergentagem de um campo em outro campo
        foreach ($arrayFields as $key => &$valuefields) {
            $valuefields['price'] = $this->getInfluencesPercentage($key, $valuefields['price'], $arrayFields);
        }

        foreach ($arrayFields as $field) {
            $this->addPrice += $field['price'];
            $cpaCustomValue[] = array('index' => $field['fieldname'], 'value' => $field['fieldvaluename']);
        }

        $newCPACustomValue = [];
        $indexed = [];
        foreach ($cpaCustomValue as $value) {

            if (in_array($value['index'], $indexed)) {
                $newCPACustomValue[$value['index']]['value']  = $newCPACustomValue[$value['index']]['value'] . '; ' . $value['value'];
            } else {
                $newCPACustomValue[$value['index']] = $value;
            }
            $indexed[] = $value['index'];
        }

        foreach ($newCPACustomValue as $val) {
            $this->description .= '<p><b>' . $val['index'] . ' : </b>' . $val['value'] . '</p>';
        }

        $pricewithreductiontax =  round($this->product->price + $this->addPrice, 2);
        $pricewithreduction =  round($this->getIVAPrice($this->product->price + $this->addPrice), 2);

        $token = $this->newCustomizationField();

        $link = $this->context->link->getProductLink(
            $this->id_product
        );

        $link .= '?actioncpa=edit&tokencpa=' . $token;

        return $this->generatePDF($pricewithreduction, $pricewithreductiontax, $link, $token);
    }

    private function generatePDF($pricewithreduction, $pricewithreductiontax, $link, $token)
    {
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $company = Configuration::get('PS_SHOP_NAME');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company);
        $pdf->SetTitle($this->trans->trans('Orçamento', [], 'Modules.Cpacustomizadorprodutosaluclass.Front'));

        // Remover header/footer automático
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();

        // =========================
        // HEADER CUSTOM
        // =========================

        $address1 = Configuration::get('PS_SHOP_ADDR1');
        $address2 = Configuration::get('PS_SHOP_ADDR2');
        $postcode = Configuration::get('PS_SHOP_CODE');
        $city     = Configuration::get('PS_SHOP_CITY');
        $country  = Configuration::get('PS_SHOP_COUNTRY');
        $vat      = Configuration::get('PS_SHOP_DETAILS');

        $html_left = '<br><strong>' . $company . '</strong><br>
              ' . $address1 . ' ' . $address2 . '<br>
              ' . $postcode . ' ' . $city . '<br>
              ' . $country . '<br>
              NIF: ' . $vat;

        $logo_path = _PS_IMG_DIR_ . Configuration::get('PS_LOGO');

        $pdf->Image($logo_path, 150, 10, 40); // posição direita
        $pdf->writeHTMLCell(100, '', 10, 10, $html_left, 0, 0, false, true, 'L', true);
        $pdf->Ln(25);

        // =========================
        // TÍTULO
        // =========================
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, $this->trans->trans('ORÇAMENTO', [], 'Modules.Cpacustomizadorprodutosaluclass.Front'), 0, 1, 'C');
        $pdf->Ln(5);
        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, $this->product->name, 0, 1, 'C');
        $pdf->Ln(5);

        // =========================
        // TABELA PRODUTOS
        // =========================

        $pdf->SetFont('helvetica', '', 10);

        $html = '<a href="' . $link . '" target="_blank" >'.$this->trans->trans('Editar/Adicionar personalização', [], 'Modules.Cpacustomizadorprodutosaluclass.Front').'</a><br><br>';
        $html .= '<table border="1" cellpadding="5">
                        <tr>
                            <th width="30%"><strong>' . $this->trans->trans('Imagem', [], 'Modules.Cpacustomizadorprodutosaluclass.Front') . '</strong></th>
                            <th width="70%"><strong>' . $this->trans->trans('Descrição', [], 'Modules.Cpacustomizadorprodutosaluclass.Front') . '</strong></th>
                        </tr>

                        <tr>
                            <td align="center">
                            <br><br>
                                <img src="' . $this->getImageCover() . '" width="200">
                            </td>
                            <td>
                                ' . $this->description . '
                            </td>
                        </tr>
                    </table>';

        $pdf->writeHTML($html, true, false, true, false, '');

        // =========================
        // TOTAL
        // =========================

        $pdf->Ln(5);
        $pdf->SetFont('helvetica', '', 10);
        $pdf->Cell(180, 10, $this->trans->trans('Total SEM IVA : %s €', [round($pricewithreductiontax, 2)], 'Modules.Cpacustomizadorprodutosaluclass.Front'), 0, 1, 'R');
        $pdf->Cell(180, 10, $this->trans->trans('IVA : %s €', [round($pricewithreduction - $pricewithreductiontax, 2)], 'Modules.Cpacustomizadorprodutosaluclass.Front'), 0, 1, 'R');

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(180, 10, $this->trans->trans('Total COM IVA : %s €', [round($pricewithreduction, 2)], 'Modules.Cpacustomizadorprodutosaluclass.Front'), 0, 1, 'R');

        $content = $pdf->Output('', 'S');

        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $namePDFtemp = $letters[random_int(0, 25)] . $letters[random_int(0, 25)];

        $idpdf = $this->savePDFBudget($namePDFtemp, $token);
        $namePDF = $namePDFtemp . $idpdf;
        $path = _PS_MODULE_DIR_ . '/cpacustomizadorprodutosaluclass/pdf/' . $namePDF . '.pdf';

        file_put_contents($path, $content);

        $url = $this->getBaseUrlWithoutVirtual() . 'modules/cpacustomizadorprodutosaluclass/pdf/' . $namePDF . '.pdf';
        return $url;
    }

    private function savePDFBudget($namePDFtemp, $token)
    {
        Db::getInstance()->execute("
            INSERT INTO `" . _DB_PREFIX_ . "cpa_customization_budget` (`id_lang`, `id_shop`, `name`, `token_configuration`)
            VALUES (" . (int)$this->id_lang . ", " . (int)$this->id_shop . ", '" . pSQL($namePDFtemp) . "', '" . pSQL($token) . "')");
        return  (int)Db::getInstance()->Insert_ID();
    }

    private function getBaseUrlWithoutVirtual()
    {
        $idShop = $this->id_shop;

        $row = Db::getInstance()->getRow('
                    SELECT domain, domain_ssl, physical_uri
                    FROM ' . _DB_PREFIX_ . 'shop_url
                    WHERE id_shop = ' . $idShop . ' AND main = 1
                ');

        $domain = Tools::usingSecureMode() ? $row['domain_ssl'] : $row['domain'];

        return (Tools::usingSecureMode() ? 'https://' : 'http://')
            . $domain
            . $row['physical_uri'];
    }
}
