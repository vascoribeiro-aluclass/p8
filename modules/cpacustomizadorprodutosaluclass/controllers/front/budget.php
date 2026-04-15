<?php
class CpacustomizadorprodutosaluclassBudgetModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();

        $cart = $this->context->cart;
        $products = $cart->getProducts();


        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $company = Configuration::get('PS_SHOP_NAME');
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor($company);
        $pdf->SetTitle('Orçamento');

        // Remover header/footer automático
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $pdf->AddPage();

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
        $pdf->SetFont('helvetica', '', 10);
        $pdf->writeHTMLCell(100, '', 10, 10, $html_left, 0, 0, false, true, 'L', true);

        $pdf->Ln(25);

        // =========================
        // TÍTULO
        // =========================
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'ORÇAMENTO', 0, 1, 'C');

        $pdf->Ln(5);

        $contentbody = '';
        $price  = 0;
        foreach ($products as $product) {

            $cover = Image::getCover($product['id_product']);
            $idImageSource = (int)$cover['id_image'];
            $imageSource = new Image($idImageSource);
            $imgCover =  _PS_PROD_IMG_DIR_ . $imageSource->getExistingImgPath() . '.jpg';
            $price += $product['price_with_reduction'];
            $contentbody .= '<tr>
                            <td align="center">
                            <br><br>
                                <img src="' . $imgCover . '" width="100">
                            </td>
                            <td> <strong>' . $product['name'] . '</strong> 
                                ' . $product['description_short'] . '
                            </td>
                            <td>   <br><br>
                                ' . round($product['price_with_reduction'], 2) . ' €
                            </td>
                        </tr>';
        }


        $pdf->SetFont('helvetica', '', 10);

        $html = '<table border="1" cellpadding="5">
                        <tr>
                            <th width="20%"><strong>Produto</strong></th>
                            <th width="60%"><strong>Descrição</strong></th>
                            <th width="20%"><strong>Preços</strong></th>
                        </tr>

                       ' . $contentbody . '
                    </table>';

        $pdf->writeHTML($html, true, false, true, false, '');

        // =========================
        // TOTAL
        // =========================

        $pdf->Ln(5);

        $pdf->SetFont('helvetica', 'B', 12);
        $pdf->Cell(180, 10, 'Total: ' . round($price, 2) . '€', 0, 1, 'R');


        $pdf->Output('example_001.pdf', 'I');
        exit;
    }
}
