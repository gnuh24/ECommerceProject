<?php

require_once __DIR__ . "/../Models/TokenModel.php";
require '../vendor/autoload.php';
class TokenController
{
    private $TokenModel;

    public function __construct()
    {
        $this->TokenModel = new TokenModel();
    }
}
