<?php

use GuzzleHttp\Promise\Create;

class CpaProcessProduct
{
    private $id_product = 0;
    private $new_id_product = 0;
    private $datacustom = [];
    private $id_lang;
    private $id_shop;
    private $id_cart;
    private $cart;
    private $languages = [];
    private $addPrice = 0;
    private $description = '';

    public function __construct($id_product, $datacustom)
    {
        $context = Context::getContext();

        $this->id_product = $id_product;
        $this->datacustom = $datacustom;
        $this->id_lang    = (int)$context->language->id;
        $this->id_shop    = (int)$context->shop->id;
        $this->cart       = $context->cart;

        if (!$this->cart->id) {
            $this->cart->id_shop =  (int)$this->id_shop;
            $this->cart->id_lang = (int)$this->id_lang;
            $this->cart->id_currency = (int)$context->currency->id;
            $this->cart->id_customer = (int)$context->customer->id;
            $this->cart->add();
            $context->cookie->id_cart = (int)$this->cart->id;
            $this->id_cart    = (int)$this->cart->id;
        } else {
            $this->id_cart    = (int)$this->cart->id;
        }

        $this->languages  = Language::getLanguages(true, $this->id_shop);
    }


    public function init()
    {
        $arrayFields = [];
        $arrayFieldsTemp = [];

        foreach ($this->datacustom as $custom) {
            $arrayCustom = explode('_', $custom);
            if (!count($arrayCustom) == 4) {
                return false;
            }
            $id_type       = $arrayCustom[0];
            $id_field       = $arrayCustom[1];
            $id_field_value = $arrayCustom[2];
            $field_qty      = $arrayCustom[3];

            if ($id_type < 1 || $id_field < 1 || $id_field_value < 1 || $field_qty < 1) {
                return false;
            }

            $resultInfField = $this->getInfField($id_type, $id_field, $id_field_value);

            if (!$resultInfField) {
                return false;
            }

            $arrayFieldsTemp[$resultInfField[0]['id_field']][] = [
                'id_type' => $resultInfField[0]['id_type'],
                'fieldname' => $resultInfField[0]['fieldname'],
                'fieldvaluename' => $resultInfField[0]['fieldvaluename'],
                'field_qty' => $field_qty,
                'price' => $resultInfField[0]['price']
            ];
        }



        foreach ($arrayFieldsTemp as $arrayfieldstemp) {
            $price = 0;
            $fieldname = '';
            $fieldvaluename = '';
            switch ($arrayfieldstemp[0]['id_type']) {
                case 1:
                    foreach ($arrayfieldstemp as $fieldstemp) {
                        $fieldvaluename = $fieldvaluename .  $fieldstemp['field_qty'] . " x ";
                    }
                    $fieldvaluename = substr($fieldvaluename, 0, -2) . 'mm';
                    $fieldname = $fieldstemp['fieldname'];
                    $price = $this->getPriceDimensions($arrayfieldstemp[0]['field_qty'], $arrayfieldstemp[1]['field_qty'], $arrayfieldstemp[2]['field_qty']);
                    break;
                case 5:
                    foreach ($arrayfieldstemp as $fieldstemp) {
                        $fieldname = $fieldstemp['fieldname'];
                        $fieldvaluename = $fieldvaluename . " " . $fieldstemp['fieldvaluename'] . " x " . $fieldstemp['field_qty'];
                        $price += ($fieldstemp['field_qty'] * $fieldstemp['price']);
                    }
                    break;
                default:
                    $fieldname = $arrayfieldstemp[0]['fieldname'];
                    $fieldvaluename = $fieldvaluename . " " . $arrayfieldstemp[0]['fieldvaluename'];
                    $price += $arrayfieldstemp[0]['price'];
                    break;
            }

            $arrayFields[] = [
                'fieldname' => $fieldname,
                'fieldvaluename' => $fieldvaluename,
                'price' => $price
            ];
        }

        $this->new_id_product = $this->createProduct();

        if (!$this->new_id_product) {
            return false;
        }

        foreach ($arrayFields as $field) {
            $this->addPrice += $field['price'];
            $cpaCustomValue[] = array('index' => $this->createLabel($this->new_id_product, $field['fieldname'], 1, 0), 'value' => $field['fieldvaluename'] . ($field['price'] > 0 ? ' + ' . $this->getIVAPrice($field['price']) . ' €' : ''));
        }

        $this->updatePriceProduct();


        $newCPACustomValue = array();
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
            $this->addTextFieldToProduct($this->new_id_product, $val['index'], 1, $val['value']);
            $fieldLabel = Db::getInstance()->getRow('SELECT name FROM `' . _DB_PREFIX_ . 'customization_field_lang` WHERE `id_customization_field` = ' . (int)$val['index'] . ' AND `id_lang`= ' . (int)$this->id_lang);
            $this->description .= '<p><b>' . $fieldLabel['name'] . ' : </b>' . $val['value'] . '</p>';
        }

        $this->updateDescriptionProduct();
        $this->addImage();

        return (int)$this->new_id_product;
    }

    private function getPriceDimensions($width, $height, $depth): float
    {
        return  Db::getInstance()->getValue("SELECT price
                        FROM palu.ps_cpa_customization_field_csv
                        WHERE 
                            width  >= " . (int)$width . " AND
                            height >= " . (int)$height . " AND
                            depth  >= " . (int)$depth . "
                        ORDER BY 
                            POW(width - " . (int)$width . ",2) +
                            POW(height - " . (int)$height . ",2) +
                            POW(depth - " . (int)$depth . ",2)
                        ASC
                        ");
    }


    private function getIVAPrice($price)
    {
        $tax_rate = Tax::getProductTaxRate($this->id_product, null, Context::getContext());
        $price_with_iva = $price + ($price * $tax_rate / 100);

        return $price_with_iva;
    }
    // private function createImage($imagens)
    // {
    //     // $imagens = [
    //     //     'img1.png',
    //     //     'img2.png',
    //     //     'img3.png',
    //     //     'img4.png'
    //     // ];

    //     // primeira imagem será a base
    //     $base = imagecreatefrompng($imagens[0]);

    //     $width = imagesx($base);
    //     $height = imagesy($base);

    //     // sobrepor as restantes
    //     for ($i = 1; $i < count($imagens); $i++) {
    //         $overlay = imagecreatefrompng($imagens[$i]);
    //         imagecopy($base, $overlay, 0, 0, 0, 0, $width, $height);
    //         imagedestroy($overlay);
    //     }

    //     // guardar resultado
    //     imagepng($base, 'resultado.png');
    //     imagedestroy($base);
    // }
    // private function addImage()
    // {
    //     $imagePath = '/caminho/para/imagem.jpg';
    //     $image = new Image();
    //     $image->id_product = (int)$this->new_id_product;
    //     $image->position = Image::getHighestPosition($this->new_id_product) + 1;
    //     $image->cover = true; // se for a imagem principal
    //     $image->add();

    //     $path = $image->getPathForCreation();

    //     ImageManager::resize(
    //         $imagePath,
    //         _PS_PROD_IMG_DIR_ . $path . '.jpg'
    //     );

    //     // gerar thumbnails
    //     $imagesTypes = ImageType::getImagesTypes('products');
    //     foreach ($imagesTypes as $imageType) {
    //         ImageManager::resize(
    //             $imagePath,
    //             _PS_PROD_IMG_DIR_ . $path . '-' . stripslashes($imageType['name']) . '.jpg',
    //             $imageType['width'],
    //             $imageType['height']
    //         );
    //     }
    // }
    // codigo provisório ***************************************************
    private function addImage()
    {
        $id_product_source = $this->id_product;
        $id_product_dest = $this->new_id_product;

        $cover = Image::getCover($id_product_source);
        if ($cover) {
            $idImageSource = (int)$cover['id_image'];
            $imageSource = new Image($idImageSource);
            $sourceFile = _PS_PROD_IMG_DIR_ . $imageSource->getExistingImgPath() . '.jpg';

            if (file_exists($sourceFile)) {

                $newImage = new Image();
                $newImage->id_product = (int)$id_product_dest;
                $newImage->position = Image::getHighestPosition($id_product_dest) + 1;
                $newImage->cover = true; // Define como capa

                if ($newImage->add()) {
                    $targetPath = $newImage->getPathForCreation();
                    ImageManager::resize($sourceFile, $targetPath . '.jpg');

                    $types = ImageType::getImagesTypes('products');
                    foreach ($types as $type) {
                        $destination = $targetPath . '-' . stripslashes($type['name']) . '.jpg';

                        ImageManager::resize(
                            $sourceFile,
                            $destination,
                            (int)$type['width'],
                            (int)$type['height']
                        );
                    }

                    $newImage->legend = $imageSource->legend;
                    $newImage->update();
                }
            }
        }
    }
    // FIm codigo provisório ***************************************************

    private function getInfField($id_type, $id_field, $id_field_value)
    {
        $sqlfields = 'SELECT 
                        cf.id_cpa_customization_field_type as id_type, 
                        cf.id_cpa_customization_field as id_field, 
                        cfl.name as fieldname, 
                        cfvl.name as fieldvaluename, 
                        cfv.price
                    FROM ' . _DB_PREFIX_ . 'cpa_customization_field cf
                    INNER JOIN ' . _DB_PREFIX_ . 'cpa_customization_field_lang cfl on cfl.id_cpa_customization_field = cf.id_cpa_customization_field and cfl.id_lang = ' . (int)$this->id_lang . '
                    INNER JOIN ' . _DB_PREFIX_ . 'cpa_customization_field_shop cfs on cfs.id_cpa_customization_field = cf.id_cpa_customization_field and cfs.id_shop = ' . (int)$this->id_shop . '
                    INNER JOIN ' . _DB_PREFIX_ . 'cpa_customization_field_value cfv on cfv.id_cpa_customization_field = cf.id_cpa_customization_field
                    INNER JOIN ' . _DB_PREFIX_ . 'cpa_customization_field_product cfp on cfp.id_cpa_customization_field = cf.id_cpa_customization_field and id_product = ' . (int)$this->id_product . '
                    INNER JOIN ' . _DB_PREFIX_ . 'cpa_customization_field_value_shop cfvs on cfvs.id_cpa_customization_field_value = cfv.id_cpa_customization_field_value and cfvs.id_shop = ' . (int)$this->id_shop . '
                    INNER JOIN ' . _DB_PREFIX_ . 'cpa_customization_field_value_lang cfvl on cfv.id_cpa_customization_field_value = cfvl.id_cpa_customization_field_value and cfvl.id_lang = ' . (int)$this->id_lang . '
                    WHERE cfv.id_cpa_customization_field_value = ' . (int)$id_field_value . ' and cf.id_cpa_customization_field_type = ' . (int)$id_type . ' and cf.id_cpa_customization_field = ' . (int)$id_field . '
                            ';

        return Db::getInstance()->executeS($sqlfields);
    }

    private function updatePriceProduct()
    {
        $product = new Product($this->new_id_product, false, $this->id_lang, $this->id_shop);
        $product->price = $product->price + $this->addPrice;
        return $product->update();
    }

    private function updateDescriptionProduct()
    {
        $product = new Product($this->new_id_product, false, $this->id_lang, $this->id_shop);
        $product->description_short = $this->description;
        return $product->update();
    }

    private function createProduct()
    {
        $cusText = 'CPA PRODUCT ';

        $product = new Product($this->id_product, false, $this->id_lang, $this->id_shop);
        if (Validate::isLoadedObject($product)) {
            $customProd = new Product(null, false, null, $this->id_shop);
            $name = $product->name;

            $link_rewrite = preg_replace('/[\s\'\:\/\[\]\-\|]+/', ' ', $name);
            $link_rewrite = str_replace(array(' ', '/', '|'), '-', $link_rewrite);
            $link_rewrite = str_replace(array('--', '---', '----'), '-', $link_rewrite);
            $link_rewrite = Tools::truncateString($link_rewrite . ' ' . $name, 125);

            foreach ($this->languages as $lang) {
                $customProd->name[$lang['id_lang']] = $cusText . ' - ' . $name;
                $customProd->link_rewrite[$lang['id_lang']] = Tools::str2url($link_rewrite);
                $customProd->description_short[$lang['id_lang']] = '';
                $customProd->description[$lang['id_lang']] = '';
            }


            $customProd->reference = Tools::str2url('custom-' . $product->id);

            $customProd->id_category_default = (int)Configuration::get('CPA_CATEGORY');

            $customProd->customizable = 1;
            $customProd->id_supplier = (int)$product->id_supplier;
            $customProd->id_manufacturer = (int)$product->id_manufacturer;
            $customProd->indexed = 0;

            $customProd->is_virtual = $product->is_virtual;
            //forpack
            $customProd->cache_is_pack = 1;
            $customProd->pack_stock_type = 1;

            $customProd->visibility = 'none';
            $customProd->price =  $product->price;
            $customProd->uploadable_files = 99;
            $customProd->text_fields = 99;
            $customProd->width = $product->width;
            $customProd->height = $product->height;
            $customProd->depth = $product->depth;
            $customProd->weight = $product->weight;
            $customProd->ecotax = $product->ecotax;
            $customProd->tax_rate = $product->tax_rate;
            $customProd->id_tax_rules_group = (int)$product->id_tax_rules_group;
            $customProd->minimal_quantity = 1;
            $customProd->save();
            //$customProd->addToShop($this->id_shop);
            StockAvailable::setQuantity($customProd->id, 0, 1);
            $customProd->addToCategories([
                (int)Configuration::get('CPA_CATEGORY')
            ]);
            $this->new_id_product = $customProd->id;
        }

        return $this->new_id_product;
    }

    private function createLabel($id_product, $labels, $type = 0, $required = 0)
    {

        $id_customization_field = 0;

        // Label insertion
        if (
            !Db::getInstance()->execute('
            INSERT INTO `' . _DB_PREFIX_ . 'customization_field` (`id_product`, `type`, `required`)
            VALUES (' . (int)$id_product . ', ' . (int)$type . ', ' . (int)$required . ')') ||
            !$id_customization_field = (int)Db::getInstance()->Insert_ID()
        ) {
            return false;
        }


        // Multilingual label name creation
        $values = '';

        foreach ($this->languages as $language)
            $values .= '(' . (int)$id_customization_field . ', ' . (int) $language['id_lang'] . ', ' . (int)$this->id_shop . ', \'' . pSQL($labels) . '\'), ';

        $values = rtrim($values, ', ');
        if (!Db::getInstance()->execute('
                    INSERT INTO `' . _DB_PREFIX_ . 'customization_field_lang` (`id_customization_field` ,`id_lang`, `id_shop`, `name`)
                    VALUES ' . $values)) {
            return false;
        }

        return (int)$id_customization_field;
    }

    private function addTextFieldToProduct($id_product, $index, $type, $text_value)
    {
        return $this->_addCustomization($id_product, 0, $index, $type, $text_value, 0);
    }

    /**
     * Add customer's pictures
     *
     * @return bool Always true
     */
    private function addPictureToProduct($id_product, $index, $type, $file)
    {
        return $this->_addCustomization($id_product, 0, $index, $type, $file, 0);
    }


    private function _addCustomization($id_product, $id_product_attribute, $index, $type, $field, $quantity)
    {

        $exising_customization = Db::getInstance()->executeS(
            '
            SELECT cu.`id_customization`, cd.`index`, cd.`value`, cd.`type` FROM `' . _DB_PREFIX_ . 'customization` cu
            LEFT JOIN `' . _DB_PREFIX_ . 'customized_data` cd
            ON cu.`id_customization` = cd.`id_customization`
            WHERE cu.id_cart = ' . (int)$this->id_cart . '
            AND cu.id_product = ' . (int)$id_product . '
            AND in_cart = 0'
        );

        if ($exising_customization) {
            // If the customization field is alreay filled, delete it
            foreach ($exising_customization as $customization) {
                if ($customization['type'] == $type && $customization['index'] == $index) {
                    Db::getInstance()->execute('
                     DELETE FROM `' . _DB_PREFIX_ . 'customized_data`
                     WHERE id_customization = ' . (int)$customization['id_customization'] . '
                     AND type = ' . (int)$customization['type'] . '
                     AND `index` = ' . (int)$customization['index']);
                    if ($type == Product::CUSTOMIZE_FILE) {
                        @unlink(_PS_UPLOAD_DIR_ . $customization['value']);
                        @unlink(_PS_UPLOAD_DIR_ . $customization['value'] . '_small');
                    }
                    break;
                }
            }
            $id_customization = $exising_customization[0]['id_customization'];
        } else {
            Db::getInstance()->execute(
                'INSERT INTO `' . _DB_PREFIX_ . 'customization` (`id_cart`, `id_product`, `id_product_attribute`, `quantity`)
               VALUES (' . (int)$this->id_cart . ', ' . (int)$id_product . ', ' . (int)$id_product_attribute . ', ' . (int)$quantity . ')'
            );
            $id_customization = Db::getInstance()->Insert_ID();
        }

        /*$query = 'INSERT INTO `'._DB_PREFIX_.'customized_data` (`id_customization`, `type`, `index`, `value`)
            VALUES ('.(int)$id_customization.', '.(int)$type.', '.(int)$index.', \''.pSQL($field).'\')';*/

        $query = 'INSERT INTO `' . _DB_PREFIX_ . 'customized_data` (`id_customization`, `type`, `index`, `value`)
            VALUES (' . (int)$id_customization . ', ' . (int)$type . ', ' . (int)$index . ', \'' . addslashes(nl2br($field)) . '\')';

        if (!Db::getInstance()->execute($query))
            return false;
        return $id_customization;
    }
}
