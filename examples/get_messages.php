<?php
/**
 * KintoneMessageFactoryのテストScript
 */
use Cstap\Kintone\KintoneMessageFactory;
error_reporting(E_ALL | E_STRICT);
require __DIR__ . '/../vendor/autoload.php';

echo sprintf("getAll: \n%s\n", var_export(KintoneMessageFactory::getInstance()->getAll(), true));
echo sprintf("getAll(JA): \n%s\n", var_export(KintoneMessageFactory::getInstance()->getAll("JA"), true));
echo sprintf("getAll(en): \n%s\n", var_export(KintoneMessageFactory::getInstance()->getAll("en"), true));
echo sprintf("getAll(hoge): \n%s\n", var_export(KintoneMessageFactory::getInstance()->getAll("hoge"), true));

echo sprintf("get(hoge): %s\n", KintoneMessageFactory::getInstance()->get("hoge"));
echo sprintf("get(kintone.unknown_url): %s\n", KintoneMessageFactory::getInstance()->get("kintone.unknown_url"));
echo sprintf("get(kintone.unknown_url): %s\n", KintoneMessageFactory::getInstance()->get("kintone.unknown_url", "en"));


