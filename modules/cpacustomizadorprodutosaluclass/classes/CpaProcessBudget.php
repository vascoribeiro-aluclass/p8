<?php

class CpaProcessBudget extends CpaProcessProduct
{
    public function __construct($id_product, $datacustom)
    {
        parent::__construct($id_product, $datacustom, false);
    }

    public function init()
    {

        $arrayFieldsTemp = $this->checkFields();
        if (!$arrayFieldsTemp) {
            return false;
        }

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


        $price =  round($this->product->price + $this->addPrice, 2);

        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $company = Configuration::get('PS_SHOP_NAME');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company);
        $pdf->SetTitle('Orçamento');

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
        $city = Configuration::get('PS_SHOP_CITY');
        $country = Configuration::get('PS_SHOP_COUNTRY');
        $vat = Configuration::get('PS_SHOP_DETAILS');
        $html_left = '<br><strong>' . $company . '</strong><br>
              ' . $address1 . ' ' . $address2 . '<br>
              ' . $postcode . ' ' . $city . '<br>
              ' . $country . '<br>
              NIF: ' . $vat;


        $logo_path = _PS_IMG_DIR_ . Configuration::get('PS_LOGO');
        // Logo (lado direito)
        $logo = $logo_path;

        $pdf->Image($logo, 150, 10, 40); // posição direita

        $pdf->writeHTMLCell(100, '', 10, 10, $html_left, 0, 0, false, true, 'L', true);

        $pdf->Ln(25);

        // =========================
        // TÍTULO
        // =========================
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'ORÇAMENTO', 0, 1, 'C');

        $pdf->Ln(5);

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(0, 10, $this->product->name, 0, 1, 'C');

        $pdf->Ln(5);

        // =========================
        // TABELA PRODUTOS
        // =========================

        $pdf->SetFont('helvetica', '', 10);

        $html = '<table border="0" cellpadding="5">
                        <tr>
                            <th width="30%"> </th>
                            <th width="70%"></th>
                        </tr>

                        <tr>
                            <td align="center">
                            <br><br><br>
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

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(180, 10, 'Total: ' . $price . '€', 0, 1, 'R');

        $content = $pdf->Output('', 'S');

        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $result = $letters[random_int(0, 25)] . $letters[random_int(0, 25)];

        $path = _PS_MODULE_DIR_ . '/cpacustomizadorprodutosaluclass/pdf/' . $result . '.pdf';

        file_put_contents($path, $content);

        $url = $this->getBaseUrlWithoutVirtual() . 'modules/cpacustomizadorprodutosaluclass/pdf/' . $result . '.pdf';

        return $url;
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
