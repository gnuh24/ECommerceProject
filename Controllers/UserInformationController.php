<?php

require_once __DIR__ . "/../Models/UserInformationModel.php";
require '../vendor/autoload.php';
class UserInformationController
{
    private $UserInformationModel;

    public function __construct()
    {
        $this->UserInformationModel = new UserInformationModel();
    }
}
