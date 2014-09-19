<?php
/**
 * getFormFieldsのテストScript
 */
use Cstap\Kintone\KintoneClient;
error_reporting(E_ALL | E_STRICT);
require __DIR__ . '/../vendor/autoload.php';

$url = inputConfig("url");
$val = KintoneClient::decomposeBrowseUrl($url);

var_dump($val);

/**
 * inputConfig
 * 
 * @param string $key
 */
function inputConfig($key)
{
    $key = (string) $key;
    echo sprintf("%s? ", $key);
    
    return trim(fgets(STDIN));
}