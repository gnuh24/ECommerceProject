<?php

require_once __DIR__ . "/../Models/BrandModel.php";
require '../vendor/autoload.php';
class BrandController
{
    private $BrandModel;

    public function __construct()
    {
        $this->BrandModel = new BrandModel();
    }
}
