<?php

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminCpaPorduct extends ModuleAdminController
{
    public function __construct()
    {
        $this->table = 'product';
        $this->className = 'Product';
        $this->bootstrap = true;
        parent::__construct();
    }
}
