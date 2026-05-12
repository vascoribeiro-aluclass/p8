<?php

class CpaProcessShare extends CpaProcessProduct
{
    private $link;
    private $name;
    private $email;
    public function __construct($name = '', $email  = '')
    {
        $this->context = Context::getContext();
        $this->name = $name;
        $this->email = $email;
    }

    public function init()
    {
        $this->link = $this->getlinksahrecart();

        echo $this->link;

        // $templateVars = array(
        //     '{nome}' =>  $this->name,
        //     '{link}' => $this->links
        // );

        // return Mail::Send(
        //     $this->context->language->id,
        //     'share',
        //     'Assunto do Email',
        //     $templateVars,
        //     $this->email,
        //     null,
        //     null,
        //     null,
        //     null,
        //     null,
        //     _PS_MODULE_DIR_ . 'cpacustomizadorprodutosaluclass/mails/'
        // );
    }


    private function GetSql($row, $table)
    {
        $text_sql = "";
        foreach ($row as $key => $value) {
            $row[$key] = str_replace("'", "''", $row[$key]);
        }
        $string_fields_products = "'" . implode("','", $row) . "'";
        $array_fields_products_key = array_keys($row);

        $string_fields_products_key = "`" . implode("`,`", $array_fields_products_key) . "`";
        $text_sql = "INSERT INTO `" . $table . "` (	$string_fields_products_key) VALUES ($string_fields_products); ";

        return $text_sql;
    }

    public  function getlinksahrecart()
    {
        $idcart = $this->context->cart->id;
        $token = bin2hex(random_bytes(32));

        // inserir carrrinho
        // cpa_cart_share inicio
        $query = new DbQuery();
        $query->select('*');
        $query->from('cart');
        $query->where("id_cart= {$idcart}");

        $resultcart = Db::getInstance()->executeS($query);
        $cart = $resultcart[0];
        $cart['token'] = $token;
        $text_sql = $this->GetSql($cart, _DB_PREFIX_ . "cpa_cart_share");
        $text_sql_array = Db::getInstance()->execute($text_sql);
        $id_cart_share = (int)Db::getInstance()->Insert_ID();
        // cpa_cart_share Fim

        // cpa_cart_product_share inicio
        $query = new DbQuery();
        $query->select('*');
        $query->from('cart_product');
        $query->where("id_cart= {$idcart}");

        $resultProducts = Db::getInstance()->executeS($query);

        foreach ($resultProducts as $product) {

            $product['id_cart_share'] = $id_cart_share;
            $text_sql = $this->GetSql($product,  _DB_PREFIX_ . "cpa_cart_product_share");
            $text_sql_array = Db::getInstance()->execute($text_sql);
            // cpa_cart_product_share Fim
            if ($product['id_customization'] > 0) {
                $id_product =  $product['id_product'];

                // id_cpa_customization_field_configuration inicio
                $query = new DbQuery();
                $query->select('*');
                $query->from('cpa_customization_field_configuration');
                $query->where("id_product_customization = {$id_product}");
                $resultProducts = Db::getInstance()->executeS($query);
                $row = $resultProducts[0];
                $row['id_cart_share'] = $id_cart_share;
                $text_sql = $this->GetSql($row,  _DB_PREFIX_ . "cpa_customization_field_configuration_share");
                $text_sql_array = Db::getInstance()->execute($text_sql);
                $id_cpa_customization_field_configuration = $row['id_cpa_customization_field_configuration'];
                // id_cpa_customization_field_configuration end

                // cpa_customization_field_configuration_value_share inicio
                $query = new DbQuery();
                $query->select('*');
                $query->from('cpa_customization_field_configuration_value');
                $query->where("id_cpa_customization_field_configuration = {$id_cpa_customization_field_configuration}");
                $resultdatas = Db::getInstance()->executeS($query);
                foreach ($resultdatas as $data) {
                    $data['id_cpa_customization_field_configuration'] = $id_cpa_customization_field_configuration;
                    $data['id_cart_share'] = $id_cart_share;
                    $text_sql = $this->GetSql($data,  _DB_PREFIX_ . "cpa_customization_field_configuration_value_share");
                    $text_sql_array = Db::getInstance()->execute($text_sql);
                }
                // cpa_customization_field_configuration_value_share end
            }
        }

        $link = $this->context->link->getPageLink('cart', true, null, [
            'actioncpa' => 'sharecart',
            'tokencpa'  => $token,
        ]);

        return $link;
    }



    public function getsahrecart($token)
    {
        $this->datacustom = [];
        $this->id_lang    = (int)$this->context->language->id;
        $this->id_shop    = (int)$this->context->shop->id;
        $this->cart       = $this->context->cart;
        $this->arrayimg   = [];

        if (!$this->cart->id) {
            $this->cart->id_shop = (int)$this->id_shop;
            $this->cart->id_lang = (int)$this->id_lang;
            $this->cart->id_currency = (int)$this->context->currency->id;
            $this->cart->id_customer = (int)$this->context->customer->id;
            $this->cart->add();

            $this->context->cookie->id_cart = (int)$this->cart->id;
            $this->context->cookie->write();

            $this->id_cart = (int)$this->cart->id;
        } else {
            $this->id_cart = (int)$this->cart->id;
        }

        $this->languages = Language::getLanguages(true);

        $query = "SELECT * FROM `" . _DB_PREFIX_ . "cpa_cart_share` where `token` =  '" . pSQL($token) . "'";
        $resultProducts = Db::getInstance()->executeS($query);

        if ($resultProducts && count($resultProducts) > 0) {

            $id_cart_share = $resultProducts[0]['id_cart_share'];

            // if (count($resultProducts) > 0) {
            //   $query = "UPDATE `" . _DB_PREFIX_ . "cart_share` set `num_show` = `num_show`-1 where `id_cart` =  " . $idcart . " and `id_cart_share` = " . $idcartshare;
            //   $resultProducts = Db::getInstance()->executeS($query);
            // } else {
            //   return false;
            // }

            // inserir carrrinho

            $query = "SELECT * FROM `" . _DB_PREFIX_ . "cpa_cart_product_share` where `id_cart_share` = " . $id_cart_share;

            $resultProducts = Db::getInstance()->executeS($query);

            foreach ($resultProducts as $product) {

                $this->id_product = $product['id_product'];

                $query = "SELECT cvs.`value` , cs.`id_product_main`
                      FROM  `" . _DB_PREFIX_ . "cpa_customization_field_configuration_share` cs 
                      INNER JOIN `" . _DB_PREFIX_ . "cpa_customization_field_configuration_value_share` cvs on cvs.id_cpa_customization_field_configuration = cs.id_cpa_customization_field_configuration
                    where cs.`id_product_customization` =  " . (int)$product['id_product'] . ' and cs.`id_cart_share` = ' . (int)$id_cart_share;

                $resultconfiguracao = Db::getInstance()->executeS($query);
                $this->id_product = $resultconfiguracao[0]['id_product_main'];

                foreach ($resultconfiguracao as $configuracao) {
                    $this->datacustom[] = $configuracao['value'];
                }

                $this->product = new Product($this->id_product, false, $this->id_lang, $this->id_shop);

                $arrayFields     = [];
                $arrayFieldsTemp = [];
                $cpaCustomValue  = [];

                $this->arrayimg[-1] = $this->getImageCover();
                $arrayFieldsTemp = $this->checkFields();


                if (!$arrayFieldsTemp) {
                    return false;
                }

                $arrayFields = $this->orderFields($arrayFieldsTemp);

                // Aplica pergentagem de um campo em outro campo
                foreach ($arrayFields as $key => &$valuefields) {
                    $valuefields['price'] = $this->getInfluencesPercentage($key, $valuefields['price'], $arrayFields);
                }

                // Cria o produto
                $this->new_id_product = $this->createProduct();

                if (!$this->new_id_product) {
                    return false;
                }

                $cpaCustomValue = $this->createLabelCustomFields($arrayFields);
                $this->updatePriceProduct();
                $this->customFieldsData($cpaCustomValue);
                $this->updateDescriptionProduct();

                // Criar imagem custimizada
                $this->addImage();

                $this->newCustomizationField();

                // Adicionar produto
                $this->cart->updateQty(
                    1,
                    $this->new_id_product,
                    0,
                     $this->new_id_customization
                );

                // Recalcular
                $this->cart->save();
                CartRule::autoRemoveFromCart($this->context);
                CartRule::autoAddToCart($this->context);

                $cartUrl =$this->context->link->getPageLink(
                    'cart',
                    true,
                    (int)$this->context->language->id,
                    ['action' => 'show']
                );

                Tools::redirect($cartUrl);
            }
        } else {
            return false;
        }

        // fim inserir carrrinho
        return true;
    }
}
