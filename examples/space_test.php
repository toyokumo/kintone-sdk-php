<?php

/**
 * getFormFieldsのテストScript
 */
use Cstap\Kintone\KintoneClient;

error_reporting(E_ALL | E_STRICT);
require __DIR__ . '/../vendor/autoload.php';

$config = array();
foreach (array("subdomain", "login", "password", "useBasic", "basicLogin", "basicPassword", "space_id") as $question) {
    $config[$question] = inputConfig($question);
}

$client = KintoneClient::factory([
            'subdomain' => $config["subdomain"],
            'login' => $config["login"],
            'password' => $config["password"],
            'useBasic' => $config["useBasic"] ? true : false,
            'basicLogin' => $config["basicLogin"],
            'basicPassword' => $config["basicPassword"],
        ]);

$fs = $client->getSpace(['id' => $config["space_id"]]);

var_dump($fs);

/**
 * inputConfig
 *
 * @param string $key
 * @return string
 */
function inputConfig($key)
{
    $key = (string) $key;
    echo sprintf("%s? ", $key);

    return trim(fgets(STDIN));
}
