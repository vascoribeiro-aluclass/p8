<?php
class CpaProcessFields
{


    public static function  init($id_product)
    {
        $context = Context::getContext();
        
        // Verifica se existe cache para o produto, se existir e não tiver expirado, exibe o cache.
        // $key_cache = $id_product . '_' . (int)$context->language->id . '_' . (int)$context->shop->id;
        // $expire = time() + 43200;

        // $search_cache = 'SELECT * FROM ' . _DB_PREFIX_ . 'cpa_customization_field_cache 
        //                 WHERE key_cache = "' . $key_cache . '" AND expire > ' . time();

        // $cache_found =  Db::getInstance()->getRow($search_cache);

        // if (sizeof($cache_found) > 0 && $cache_found['key_cache'] == $key_cache && $cache_found['expire'] > time()) {
        //   $this->context->smarty->assign('template', $cache_found['content']);
        //   return $this->display(__FILE__, 'cpa_cached.tpl');
        // }



        $sqlfields = 'SELECT 
                      cf.id_cpa_customization_field,
                      cf.position,
                      cf.order_position,
                      cf.required,
                      cf.dimensions,
                      cf.is_visual,
                      cf.price_type,
                      cf.zindex,
                      cf.open_status,
                      cf.isvisivel,
                      cfl.name,
                      cfl.notice,
                      cfl.tooltip,
                      cft.name as type_name,
                      cft.class as type_class,
                      cft.id_cpa_customization_field_type as type_id
                    FROM ' . _DB_PREFIX_ . 'cpa_customization_field cf
                    INNER JOIN ' . _DB_PREFIX_ . 'cpa_customization_field_lang  cfl ON (cf.id_cpa_customization_field = cfl.id_cpa_customization_field) and cfl.id_lang = ' . (int)$context->language->id . '
                    INNER JOIN ' . _DB_PREFIX_ . 'cpa_customization_field_product  cfp ON (cf.id_cpa_customization_field = cfp.id_cpa_customization_field) and cfp.id_product = ' . (int)Tools::getValue('id_product') . '
                    INNER JOIN ' . _DB_PREFIX_ . 'cpa_customization_field_shop  cfs ON (cf.id_cpa_customization_field = cfs.id_cpa_customization_field) and cfs.id_shop = ' . (int)$context->shop->id . ' 
                    INNER JOIN ' . _DB_PREFIX_ . 'cpa_customization_field_type  cft ON (cf.id_cpa_customization_field_type = cft.id_cpa_customization_field_type)
                    
                    WHERE cfl.id_lang = ' . (int)$context->language->id . ' AND cfp.id_product = ' . (int)$id_product . ' and cfs.id_shop = ' . (int)$context->shop->id . '
                
                    ORDER BY cf.position ASC';

        $resultsfields = Db::getInstance()->executeS($sqlfields);


        return $resultsfields;
    }
}
