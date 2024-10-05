<?php

require_once __DIR__ . "/../Models/CategoryModel.php";
require '../vendor/autoload.php';
class CategoryController
{
    private $CategoryModel;

    public function __construct()
    {
        $this->CategoryModel = new CategoryModel();
    }
}
