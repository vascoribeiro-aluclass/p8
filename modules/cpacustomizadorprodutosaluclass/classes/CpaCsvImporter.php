<?php


class CpaCsvImporter
{
    private $errors = [];
    private $id_cpa_customization_field;
    private $countrow = 0;
    private $countheader = 0;
    private $isheader = false;

    private $depth = 0;
    private $width = 0;
    private $arrayheight = [];

    private $depthMax = 0;
    private $widthMax = 0;
    private $heightMax = 0;

    private $depthMin = 999999999;
    private $widthMin = 999999999;
    private $heightMin = 999999999;


    public function __construct($id_cpa_customization_field = null)
    {
        $this->id_cpa_customization_field = $id_cpa_customization_field;
    }

    public function importCSV($filePath)
    {
        $context = Context::getContext();
        $translator = $context->getTranslator();

        if (!$this->checkCSV($filePath)) {
            $this->errors[] = $translator->trans('CSV inválido', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
            return false;
        }

        if (!file_exists($filePath)) {
            $this->errors[] = $translator->trans('Ficheiro não encontrado', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
            return false;
        }

        $handle = fopen($filePath, 'r');

        if ($handle === false) {
            $this->errors[] = $translator->trans('Erro ao abrir o ficheiro CSV', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
            return false;
        }

        if (!$this->deleteCSV()) {
            $this->errors[] = $translator->trans('Erro ao deletar registos na base de dados', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
            return false;
        }

        $header = fgetcsv($handle, 0, ';');
        $this->setHeighDepth($header);

        while (($row = fgetcsv($handle, 0, ';')) !== false) {
            $this->countrow++;
            if (empty(array_filter($row, fn($v) => trim($v) !== ''))) {
                $this->countheader++;
                $this->isheader = true;
                continue;
            }

            if ($this->isheader) {
                $this->setHeighDepth($row);
                $this->isheader = false;
                continue;
            }

            $this->width = $row[0] ?? null;
            if ($this->width > $this->widthMax) {
                $this->widthMax = $this->width;
            }

            if ($this->widthMin > $this->width) {
                $this->widthMin = $this->width;
            }

            $arrayprice = [];
            $arrayprice = array_slice($row, 1);

            foreach ($this->arrayheight as $key => $width) {
                $price = 0;
                $price  = round(str_replace(',', '.', $arrayprice[$key]),6);
                if (!$this->SetRowCSV($price, $width)) {
                    $this->errors[] = $translator->trans('Erro ao inserir na base de dados', [], 'Modules.Cpacustomizadorprodutosaluclass.Admin');
                }
            }
        }

        fclose($handle);

        $this->dimensionsUpdate($this->countrow > 1 && $this->countheader > 0 ? 3 : ($this->countrow > 1 || $this->countheader > 0 ? 2 : 1));

        if (!$this->checkTableValueCustomizationField()) {
            $this->setCpaCustomizationValue(1, "Largura (min " . $this->widthMin . " mm - max " . $this->widthMax . " mm)", true);
            $this->setCpaCustomizationValue(2, "Altura (min " . $this->heightMin . " mm - max " . $this->heightMax . " mm)", ($this->countrow > 1 ? true : false));
            $this->setCpaCustomizationValue(3, "Profundidade (min " . $this->depthMin . " mm - max " . $this->depthMax . " mm)", ($this->countheader > 0 ? true : false));
        }

        return empty($this->errors);
    }

    public function getErrors()
    {
        return $this->errors;
    }

    function checkCSV($filePath)
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

        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $record->name[$lang['id_lang']] = $name;
        }

        $record->save();

        $shops = Shop::getShops(false, null, true);
        $record->associateTo($shops);
    }


    public function setHeighDepth($row)
    {
        $this->depth = $row[0] ?? 0;
        $this->depth = $row[0] ?? null;
        if ($this->depth > $this->depthMax) {
            $this->depthMax = $this->depth;
        }

        if ($this->depthMin > $this->depth) {
            $this->depthMin = $this->depth;
        }
        $this->arrayheight = array_slice($row, 1);
    }

    public function setWidth($width)
    {
        $this->width = $width;
    }


    public function deleteCSV()
    {
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

    public function SetRowCSV($price, $height)
    {

        if ($height > $this->heightMax) {
            $this->heightMax = $height;
        }

        if ($this->heightMin > $height) {
            $this->heightMin = $height;
        }
        return Db::getInstance()->insert(
            'cpa_customization_field_csv',
            [
                'id_cpa_customization_field' => (int)$this->id_cpa_customization_field,
                'width'  => (int)$this->width,
                'height' => (int)$height,
                'depth'  => (int)$this->depth,
                'price'  => (float)$price,
            ]
        );
    }
}
