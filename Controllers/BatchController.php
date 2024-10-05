<?php

require_once __DIR__ . "/../Models/BatchModel.php";
require '../vendor/autoload.php';
class BatchController
{
    private $BatchModel;

    public function __construct()
    {
        $this->BatchModel = new BatchModel();
    }
}
