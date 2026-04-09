<?php


class CpaCsvSelImporter
{
    private $errors = [];
    private $id_cpa_customization_field;
    private $countrow = 0;
    private $countheader = 0;
    private $isheader = false;
    private $isLang = false;
    private $langCurrent = null;
    private $depth = 0;
    private $height = 0;
    private $arraywidth = [];

    private $arraylang = [];
    private $arraySelectlangs = [];
    private $context;
    private $languages = [];


    public function __construct($id_cpa_customization_field = null)
    {
        $this->context = Context::getContext();
        $this->languages = Language::getLanguages(false);
        foreach ($this->languages as $lang) {
            $this->arraylang[] = $lang['iso_code'];
        }

        $this->id_cpa_customization_field = $id_cpa_customization_field;
    }

    public function importCSV($filePath)
    {
        $translator = $this->context->getTranslator();

        // if (!$this->checkCSV($filePath)) {
        //     $this->errors[] = $translator->trans('CSV seleção inválido', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
        //     return false;
        // }

        if (!file_exists($filePath)) {
            $this->errors[] = $translator->trans('Ficheiro não encontrado', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
            return false;
        }

        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            $this->errors[] = $translator->trans('Erro ao abrir o ficheiro CSV seleção', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
            return false;
        }

        if (!$this->deleteCSV()) {
            $this->errors[] = $translator->trans('Erro ao deletar registos na base de dados', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
            return false;
        }

        while (($row = fgetcsv($handle, 0, ';')) !== false) {

            if (in_array(trim(strtolower($row[0])), $this->arraylang) && !$this->isLang) {
                $this->isLang = true;
                $this->langCurrent = trim(strtolower($row[0]));
                continue;
            } else if ($this->isLang) {

                $arraylang = array_slice($row, 1);

                foreach ($arraylang as $key => $lang) {
                    if (!empty(trim($lang))) {
                        $this->arraySelectlangs[$this->langCurrent][trim(strtolower($row[0]))][] = $lang;
                    }
                }

                if (trim(strtolower($row[0])) == 'depth') {
                    $this->isLang = false;
                    $this->langCurrent = null;
                }

                continue;
            } else {
                $this->countrow++;
                if (empty(array_filter($row, fn($v) => trim($v) !== ''))) {
                    $this->countheader++;
                    $this->isheader = true;
                    continue;
                }

                if ($this->isheader) {
                    $this->setheightDepth($row);
                    $this->isheader = false;
                    continue;
                }


                $this->setheight($row[0]);
                $arrayprice = [];
                $arrayprice = array_slice($row, 1);

                foreach ($this->arraywidth as $key => $width) {
                    $price = 0;

                    if (is_numeric(str_replace(',', '.', $arrayprice[$key]))) {
                        $price  = round(str_replace(',', '.', $arrayprice[$key]), 6);
                        if (!$this->SetRowCSV($price, $width)) {
                            $this->errors[] = $translator->trans('Erro ao inserir na base de dados', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
                        }
                    }
                }
            }
        }

        fclose($handle);

        $this->dimensionsUpdate($this->countrow > 1 && $this->countheader > 0 ? 3 : ($this->countrow > 1 || $this->countheader > 0 ? 2 : 1));


        if (!$this->checkTableValueCustomizationField()) {
            $this->setCpaCustomizationValue(1, "Seleção Altura", true);
            $this->setCpaCustomizationValue(2, "Seleção Largura", ($this->countrow > 1 ? true : false));
            $this->setCpaCustomizationValue(3, "Seleção Profundidade", ($this->countheader > 0 ? true : false));
        }

        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function checkCSV($filePath)
    {
        $rows = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($rows as $numRow => $contentRow) {
            if (preg_match('/^;+$/', trim($contentRow))) continue;

            $fields = explode(';', $contentRow);

            foreach ($fields as $value) {
                if (trim($value) === "") {
                    return false;
                }

                if (!preg_match('/^\s*\d+(\.\d+)?\s*$/', $value)) {
                    return false;
                }
            }
        }
        return true;
    }

    public function checkTableValueCustomizationField()
    {
        $sql = 'SELECT COUNT(*) 
                FROM ' . _DB_PREFIX_ . 'cpa_customization_field_value 
                WHERE id_cpa_customization_field = ' . (int)$this->id_cpa_customization_field;

        return (Db::getInstance()->getValue($sql) > 0) ? true : false;
    }

    public function setCpaCustomizationValue($position, $name, $isvisivel)
    {
        $record = new CpaCfv();
        $record->id_cpa_customization_field = (int) $this->id_cpa_customization_field;
        $record->price = 0;
        $record->position = $position;
        $record->isvisivel = $isvisivel;

        foreach ($this->languages  as $lang) {
            $record->name[$lang['id_lang']] = $name;
        }

        $record->save();

        $shops = Shop::getShops(false, null, true);
        $record->associateTo($shops);
    }


    public function setheightDepth($row)
    {
        $this->depth = $row[0] ?? 0;

        $this->arraywidth = array_slice($row, 1);
        $this->setSelectLangsdepth();
    }

    public function setheight($height)
    {
        $this->height = $height;
        $this->setSelectLangsheight();
    }


    public function deleteCSV()
    {

        Db::getInstance()->delete(
            'cpa_customization_field_csv_selection_lang',
            'id_cpa_customization_field = ' . (int)$this->id_cpa_customization_field
        );


        return  Db::getInstance()->delete(
            'cpa_customization_field_csv',
            'id_cpa_customization_field = ' . (int)$this->id_cpa_customization_field
        );
    }

    public function dimensionsUpdate($dimensions)
    {
        return Db::getInstance()->update(
            'cpa_customization_field',
            [
                'dimensions' => (int)$dimensions,
            ],
            'id_cpa_customization_field = ' . (int)$this->id_cpa_customization_field

        );
    }
    public function setSelectLangsheight()
    {

        foreach ($this->languages as $lang) {
            if (array_key_exists($this->height - 1, $this->arraySelectlangs[$lang['iso_code']]['height'])) {
                Db::getInstance()->insert(
                    'cpa_customization_field_csv_selection_lang',
                    [
                        'id_lang' => (int)$lang['id_lang'],
                        'name'  => $this->arraySelectlangs[$lang['iso_code']]['height'][$this->height - 1],
                        'type'   => 'height',
                        'value'    => $this->height,
                        'id_cpa_customization_field' => (int)$this->id_cpa_customization_field
                    ],
                    false,
                    true,
                    Db::INSERT_IGNORE
                );
            }
        }
    }

    public function setSelectLangswidth($width)
    {
        foreach ($this->languages as $lang) {
            if (array_key_exists($width - 1, $this->arraySelectlangs[$lang['iso_code']]['width'])) {
                Db::getInstance()->insert(
                    'cpa_customization_field_csv_selection_lang',
                    [
                        'id_lang' => (int)$lang['id_lang'],
                        'name'  => $this->arraySelectlangs[$lang['iso_code']]['width'][$width - 1],
                        'type'   => 'width',
                        'value'    => $width,
                        'id_cpa_customization_field' => (int)$this->id_cpa_customization_field
                    ],
                    false,
                    true,
                    Db::INSERT_IGNORE
                );
            }
        }
    }

    public function setSelectLangsdepth()
    {
        foreach ($this->languages as $lang) {

            if (array_key_exists($this->depth - 1, $this->arraySelectlangs[$lang['iso_code']]['depth'])) {
                Db::getInstance()->insert(
                    'cpa_customization_field_csv_selection_lang',
                    [
                        'id_lang' => (int)$lang['id_lang'],
                        'name'  => $this->arraySelectlangs[$lang['iso_code']]['depth'][$this->depth - 1],
                        'type'   => 'depth',
                        'value'    => $this->depth,
                        'id_cpa_customization_field' => (int)$this->id_cpa_customization_field
                    ],
                    false,
                    true,
                    Db::INSERT_IGNORE
                );
            }
        }
    }


    public function SetRowCSV($price, $width)
    {

        Db::getInstance()->insert(
            'cpa_customization_field_csv',
            [
                'id_cpa_customization_field' => (int)$this->id_cpa_customization_field,
                'height'  => (int)$this->height,
                'width' => (int)$width,
                'depth'  => (int)$this->depth,
                'price'  => (float)$price,
            ]
        );

        $this->setSelectLangswidth((int)$width);


        return    true;
    }
}
