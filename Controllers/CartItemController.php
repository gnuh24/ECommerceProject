<?php

require_once __DIR__ . "/../Models/CartItemModel.php";
require '../vendor/autoload.php';
class CartItemController
{
    private $CartItemModel;

    public function __construct()
    {
        $this->CartItemModel = new CartItemModel();
    }
}
