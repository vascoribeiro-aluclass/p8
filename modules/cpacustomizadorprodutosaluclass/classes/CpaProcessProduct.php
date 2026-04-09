<?php

use GuzzleHttp\Promise\Create;

class CpaProcessProduct
{
    private $id_product = 0;
    private $new_id_product = 0;
    private $new_id_customization = 0;
    private $datacustom = [];
    private $id_lang;
    private $id_shop;
    private $id_cart;
    private $cart;
    private $languages = [];
    private $addPrice = 0;
    private $description = '';
    private $product;
    private $context;
    private $arrayimg = [];

    public function __construct($id_product, $datacustom)
    {
        $this->context = Context::getContext();

        $this->id_product = $id_product;
        $this->datacustom = $datacustom;
        $this->id_lang    = (int)$this->context->language->id;
        $this->id_shop    = (int)$this->context->shop->id;
        $this->cart       = $this->context->cart;
        $this->arrayimg   = [];

        if (!$this->cart->id) {
            $this->cart->id_shop =  (int)$this->id_shop;
            $this->cart->id_lang = (int)$this->id_lang;
            $this->cart->id_currency = (int)$this->context->currency->id;
            $this->cart->id_customer = (int)$this->context->customer->id;
            $this->cart->add();
            //$context->cookie->id_cart = (int)$this->cart->id;
            $this->id_cart    = (int)$this->cart->id;
        } else {
            $this->id_cart    = (int)$this->cart->id;
        }

        $this->languages  = Language::getLanguages(true, $this->id_shop);
        $this->product = new Product($this->id_product, false, $this->id_lang, $this->id_shop);
    }


    public function init()
    {
        $arrayFields = [];
        $arrayFieldsTemp = [];
        $cpaCustomValue  = [];

        $cover = Image::getCover($this->id_product);
        $idImageSource = (int)$cover['id_image'];
        $imageSource = new Image($idImageSource);
        $sourceImgProduct = _PS_PROD_IMG_DIR_ . $imageSource->getExistingImgPath() . '.jpg';
        $this->arrayimg[] = $sourceImgProduct;
        // Valida os campos e prepara para ser processados
        foreach ($this->datacustom as $custom) {
            $arrayCustom = explode('_', $custom);
            if (count($arrayCustom) != 4) {
                return false;
            }

            $id_type        = $arrayCustom[0];
            $id_field       = $arrayCustom[1];
            $id_field_value = $arrayCustom[2];
            $field_qty      = $arrayCustom[3];

            if ($id_type == 1 || $id_type == 7) {
                if ($id_type < 1 || $id_field < 1 || $id_field_value < 1 || $field_qty < 0) {
                    return false;
                }
            } else if ($id_type == 4) {
                if ($id_type < 1 || $id_field < 1 || $id_field_value < 1) {
                    return false;
                }
            } else {
                if ($id_type < 1 || $id_field < 1 || $id_field_value < 1 || $field_qty < 1) {
                    return false;
                }
            }


            $resultInfField = $this->getInfField($id_type, $id_field, $id_field_value);

            if (!$resultInfField) {
                return false;
            }


            if ($resultInfField[0]['is_visual'] == 1) {
                $resultimg = $this->getImg($resultInfField[0]['id_field_value']);
                if ($resultimg) {
                    foreach ($resultimg as $img) {
                        $imagem = _PS_ROOT_DIR_ . '/img/scenes/' . $img['type'] . $resultInfField[0]['id_field_value'] . '.' . $img['ext'];
                        $this->arrayimg[] = $imagem;
                    }
                }
            }

            $arrayFieldsTemp[$resultInfField[0]['id_field']][] = [
                'id_type' => $resultInfField[0]['id_type'],
                'fieldname' => $resultInfField[0]['fieldname'],
                'price_type' => $resultInfField[0]['price_type'],
                'fieldvaluename' => $resultInfField[0]['fieldvaluename'],
                'field_qty' => $field_qty,
                'percent' => ($resultInfField[0]['price_type'] == 'amount' ? 0 : $resultInfField[0]['price']),
                'price' => ($resultInfField[0]['price_type'] == 'amount' ? $resultInfField[0]['price'] : ($resultInfField[0]['price'] / 100) * $this->product->price)
            ];
        }


        // Reorganiza os campos e processa para ser inseridos no sistema de prestashop
        foreach ($arrayFieldsTemp as $key => $valuefieldstemp) {
            $price = 0;
            $percent = 0;
            $fieldname = '';
            $fieldvaluename = '';
            switch ($valuefieldstemp[0]['id_type']) {
                case 1:

                    if (count($valuefieldstemp) == 3) {
                        foreach ($valuefieldstemp as $fieldstemp) {
                            if ($fieldstemp['field_qty'] > 0) {
                                $fieldvaluename = $fieldvaluename .  $fieldstemp['field_qty'] . " x ";
                            }
                        }

                        $fieldvaluename = substr($fieldvaluename, 0, -2) . 'mm';
                        $fieldname = $fieldstemp['fieldname'];

                        $price = $this->getPriceDimensions($valuefieldstemp[0]['field_qty'], $valuefieldstemp[1]['field_qty'], $valuefieldstemp[2]['field_qty']);
                    } else {
                        break;
                    }

                    break;


                case 7:

                    if (count($valuefieldstemp) == 3) {
                        $countfield =0;
                        foreach ($valuefieldstemp as  $fieldstemp) {
                             $countfield ++;
                            if ($fieldstemp['field_qty'] > 0) {
                                $fieldvaluename = $fieldvaluename .   $this->getSelectDimensions($fieldstemp['field_qty'], $key, ($countfield == 1 ? 'height': ($countfield == 2 ? 'width':'depth') ) ). " x ";
                            }
                        }

                        $fieldvaluename = substr($fieldvaluename, 0, -2);
                        $fieldname = $fieldstemp['fieldname'];

                        $price = $this->getPriceDimensions($valuefieldstemp[0]['field_qty'], $valuefieldstemp[1]['field_qty'], $valuefieldstemp[2]['field_qty']);
                    } else {
                        break;
                    }

                    break;

                case 4:
                    foreach ($valuefieldstemp as $fieldstemp) {
                        $fieldname = $fieldstemp['fieldname'];
                        $fieldvaluename = $fieldvaluename . " " . $fieldstemp['fieldvaluename'] . " : " . $fieldstemp['field_qty'];
                    }
                    break;
                case 5:
                    foreach ($valuefieldstemp as $fieldstemp) {
                        $fieldname = $fieldstemp['fieldname'];
                        $fieldvaluename = $fieldvaluename . " " . $fieldstemp['fieldvaluename'] . " x " . $fieldstemp['field_qty'];
                        $price += ($fieldstemp['field_qty'] * $fieldstemp['price']);
                    }
                    $percent = $valuefieldstemp[0]['percent'];
                    break;
                case 6:
                    foreach ($valuefieldstemp as $fieldstemp) {
                        $fieldname = $fieldstemp['fieldname'];
                        $fieldvaluename = $fieldvaluename . " " . $fieldstemp['fieldvaluename'] . " x " . $fieldstemp['field_qty'];
                        $price += ($fieldstemp['field_qty'] * $fieldstemp['price']);
                    }
                    $percent = $valuefieldstemp[0]['percent'];
                    break;
                default:
                    $fieldname = $valuefieldstemp[0]['fieldname'];
                    $fieldvaluename = $fieldvaluename . " " . $valuefieldstemp[0]['fieldvaluename'];
                    $price += $valuefieldstemp[0]['price'];
                    $percent = $valuefieldstemp[0]['percent'];
                    break;
            }


            if (!empty($fieldname) && !empty($fieldvaluename)) {
                $arrayFields[$key] = [
                    'fieldname' => $fieldname,
                    'fieldvaluename' => $fieldvaluename,
                    'percent' => $percent,
                    'price' => $price
                ];
            }
        }

        // Aplica pergentagem de um campo em outro campo
        foreach ($arrayFields as $key => &$valuefields) {
            $valuefields['price'] = $this->getInfluencesPercentage($key, $valuefields['price'], $arrayFields);
        }

        // Cria o produto
        $this->new_id_product = $this->createProduct();

        if (!$this->new_id_product) {
            return false;
        }

        $newCPACustomValue = [];
        // Adiciona campos no sistema de custimização do prestashop
        foreach ($arrayFields as $field) {
            $this->addPrice += $field['price'];
            $cpaCustomValue[] = array('index' => $this->createLabel($this->new_id_product, $field['fieldname'], 1, 0), 'value' => $field['fieldvaluename'] . ($field['price'] > 0 ? ' + ' . $this->getIVAPrice(round($field['price']), 2) . ' €' : ''));
        }

        $this->updatePriceProduct();

        // Prepara a descição para inserir no produto no campo descrição curta.
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
            $this->new_id_customization = $this->addTextFieldToProduct($this->new_id_product, $val['index'], 1, $val['value']);
            $fieldLabel = Db::getInstance()->getRow('SELECT name FROM `' . _DB_PREFIX_ . 'customization_field_lang` WHERE `id_customization_field` = ' . (int)$val['index'] . ' AND `id_lang`= ' . (int)$this->id_lang);
            $this->description .= '<p><b>' . $fieldLabel['name'] . ' : </b>' . $val['value'] . '</p>';
        }

        $this->updateDescriptionProduct();

        // Criar imagem custimizada
        $this->addImage();

        $arraynewproduct = [];
        $arraynewproduct['new_id_product'] = $this->new_id_product;
        $arraynewproduct['new_id_customization'] =  $this->new_id_customization;

        return $arraynewproduct;
    }


    private function getInfluencesPercentage($id_cpa_customization_field, $price, $arrayFields)
    {
        $priceAdd = 0;
        $sqlfieldvalues = 'SELECT 
                                cfi.id_cpa_customization_field
                            FROM ' . _DB_PREFIX_ . 'cpa_customization_field_influences_percentage cfi
                            WHERE cfi.id_cpa_customization_field_percentage = ' . (int)$id_cpa_customization_field . '';

        $result = Db::getInstance()->executeS($sqlfieldvalues);

        if (is_array($result)) {
            foreach ($result as $value) {
                if (array_key_exists($value['id_cpa_customization_field'], $arrayFields)) {
                    $priceAdd += ($arrayFields[$value['id_cpa_customization_field']]['percent'] / 100) * $price;
                }
            }
        }
        return $priceAdd + $price;
    }

    private function getSelectDimensions($value, $id_cpa_customization_field, $type)
    {

        return  Db::getInstance()->getValue("SELECT name
                        FROM " . _DB_PREFIX_ . "cpa_customization_field_csv_selection_lang
        
                        WHERE 
                            value  = " . (int)$value . " AND
                            id_cpa_customization_field = " . (int)$id_cpa_customization_field . " AND
                            type  = '" . $type . "' AND
                            id_lang = " . (int)$this->id_lang . "
                        ");
    }

    private function getPriceDimensions($height, $width, $depth): float
    {

        return  Db::getInstance()->getValue("SELECT price
                        FROM " . _DB_PREFIX_ . "cpa_customization_field_csv
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
        $tax_rate = Tax::getProductTaxRate($this->id_product, null, $this->context);
        $price_with_iva = $price + ($price * $tax_rate / 100);

        return $price_with_iva;
    }


    private function createImage($img)
    {
        $fileType = exif_imagetype($img);

        switch ($fileType) {
            case 2:
                $sourceImage = imagecreatefromjpeg($img);
                break;
            case 3:
                $sourceImage = imagecreatefrompng($img);
                break;
            case 18:
                $sourceImage = imagecreatefromwebp($img);
                break;
            default:
                return false;
        }

        return $sourceImage;
    }

    private function joinImage()
    {
        $path = _PS_ROOT_DIR_ . '/img/scenes/cpa/';

        $mainImg = imagecreatetruecolor(400, 400);
        $white = imagecolorallocate($mainImg, 255, 255, 255);
        imagefill($mainImg, 0, 0, $white);

        foreach ($this->arrayimg as $img) {

            $sourceImage = $this->createImage($img);
            if ($sourceImage) {
                $secImgX = imagesx($sourceImage);
                $secImgY = imagesy($sourceImage);
                imagecopyresized($mainImg, $sourceImage, 0, 0, 0, 0, 400, 400, $secImgX, $secImgY);
            }
        }
        $filemainImg = $path . $this->new_id_product . '.jpg';

        imagejpeg($mainImg, $filemainImg);

        $mainImg = null;
        $sourceImage = null;
        return $filemainImg;
    }

    private function addImage()
    {
        $filemainImg = $this->joinImage();
        $newImage = new Image();
        $newImage->id_product = (int)$this->new_id_product;
        $newImage->position = Image::getHighestPosition($this->new_id_product) + 1;
        $newImage->cover = true;

        if ($newImage->add()) {
            $targetPath = $newImage->getPathForCreation();
            ImageManager::resize($filemainImg, $targetPath . '.jpg');

            $types = ImageType::getImagesTypes('products');
            foreach ($types as $type) {
                $destination = $targetPath . '-' . stripslashes($type['name']) . '.jpg';

                ImageManager::resize(
                    $filemainImg,
                    $destination,
                    (int)$type['width'],
                    (int)$type['height']
                );
            }
            $newImage->legend =  ' CPA Customization Image ';
            $newImage->update();
            unlink($filemainImg);
        }
    }

    private function getImg($id_field_value, $type = 'cpa/img/')
    {
        $sqlimg = "SELECT 
                        fvi.ext,
                        fvi.type
                    FROM " . _DB_PREFIX_ . "cpa_customization_field_value_img fvi
                    WHERE fvi.id_cpa_customization_field_value = " . (int)$id_field_value . " and fvi.ext != 'webp' and fvi.type = '" . $type . "'";

        return Db::getInstance()->executeS($sqlimg);
    }

    private function getInfField($id_type, $id_field, $id_field_value)
    {
        $sqlfields = 'SELECT 
                        cf.id_cpa_customization_field_type as id_type, 
                        cf.id_cpa_customization_field as id_field, 
                        cf.price_type as price_type, 
                        cfl.name as fieldname, 
                        cfvl.name as fieldvaluename, 
                        cfv.price,
                        cf.is_visual,
                        cfv.id_cpa_customization_field_value as id_field_value
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

        if (Validate::isLoadedObject($this->product)) {
            $customProd = new Product(null, false, null, $this->id_shop);
            $name = $this->product->name;

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


            $customProd->reference = Tools::str2url('custom-' . $this->product->id);

            $customProd->id_category_default = (int)Configuration::get('CPA_CATEGORY');

            $customProd->customizable = 1;
            $customProd->id_supplier = (int)$this->product->id_supplier;
            $customProd->id_manufacturer = (int)$this->product->id_manufacturer;
            $customProd->indexed = 0;

            $customProd->is_virtual = $this->product->is_virtual;
            //forpack
            $customProd->cache_is_pack = 1;
            $customProd->pack_stock_type = 1;

            $customProd->visibility = 'none';
            $customProd->price =  $this->product->price;
            $customProd->uploadable_files = 99;
            $customProd->text_fields = 99;
            $customProd->width = $this->product->width;
            $customProd->height = $this->product->height;
            $customProd->depth = $this->product->depth;
            $customProd->weight = $this->product->weight;
            $customProd->ecotax = $this->product->ecotax;
            $customProd->tax_rate = $this->product->tax_rate;
            $customProd->id_tax_rules_group = (int)$this->product->id_tax_rules_group;
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

        $query = 'INSERT INTO `' . _DB_PREFIX_ . 'customized_data` (`id_customization`, `type`, `index`, `value`)
            VALUES (' . (int)$id_customization . ', ' . (int)$type . ', ' . (int)$index . ', \'' . pSQL($field) . '\')';

        if (!Db::getInstance()->execute($query))
            return false;
        return $id_customization;
    }
}
