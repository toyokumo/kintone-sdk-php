<?php
use Cstap\Kintone\KintoneClient;
error_reporting(E_ALL | E_STRICT);
require __DIR__ . '/../vendor/autoload.php';



$client = KintoneClient::factory([
        'domain'        => "dev-cstap",
        'login'         => "dev-cstap",
        'password'      => "**",
        'useBasic'      => true,
        'basicLogin'    => "dev-cstap",
        'basicPassword' => "**",
    ]);

$fs = $client->getFormFields([
        'app' => 78
    ]);

var_dump($fs);
