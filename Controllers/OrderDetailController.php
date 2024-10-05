<?php

require_once __DIR__ . "/../Models/OrderDetailModel.php";
require '../vendor/autoload.php';
class OrderDetailController
{
    private $OrderDetailModel;

    public function __construct()
    {
        $this->OrderDetailModel = new BatchModel();
    }
}
