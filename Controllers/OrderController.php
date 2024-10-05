<?php

require_once __DIR__ . "/../Models/OrderModel.php";
require '../vendor/autoload.php';
class OrderController
{
    private $OrderModel;

    public function __construct()
    {
        $this->OrderModel = new OrderModel();
    }
}
