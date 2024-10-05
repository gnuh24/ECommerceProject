<?php

require_once __DIR__ . "/../Models/OrderStatusModel.php";
require '../vendor/autoload.php';
class OrderStatusController
{
    private $OrderStatusModel;

    public function __construct()
    {
        $this->OrderStatusModel = new OrderStatusModel();
    }
}
