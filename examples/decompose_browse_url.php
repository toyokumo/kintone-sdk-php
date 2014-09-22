<?php
/**
 * getFormFieldsのテストScript
 */
use Cstap\Kintone\KintoneClient;
error_reporting(E_ALL | E_STRICT);
require __DIR__ . '/../vendor/autoload.php';

$url = inputConfig("url");
try {
    $val = KintoneClient::decomposeBrowseUrl($url);
    var_dump($val);
} catch (Cstap\Kintone\Common\Exception\KintoneException $e) {
    echo sprintf("%s:message() %s\n", get_class($e), $e->getLocaleMessage());
    echo sprintf("%s:message(en) %s\n", get_class($e), $e->getLocaleMessage("en"));
} catch (Exception $e) {
    echo sprintf("%s:%s\n%s online %d\n%s", get_class($e), $e->getMessage(), $e->getFile(), $e->getLine(), $e->getTraceAsString());
}




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