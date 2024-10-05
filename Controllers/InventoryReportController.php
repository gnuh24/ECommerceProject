<?php

require_once __DIR__ . "/../Models/InventoryReportModel.php";
require '../vendor/autoload.php';
class InventoryReportController
{
    private $InventoryReportModel;

    public function __construct()
    {
        $this->InventoryReportModel = new InventoryReportModel();
    }
}
