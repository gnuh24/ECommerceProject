<?php

require_once __DIR__ . "/../Models/InventoryReportDetailModel.php";
require '../vendor/autoload.php';
class InventoryReportDetailController
{
    private $InventoryReportDetailModel;

    public function __construct()
    {
        $this->InventoryReportDetailModel = new InventoryReportDetailModel();
    }
}
