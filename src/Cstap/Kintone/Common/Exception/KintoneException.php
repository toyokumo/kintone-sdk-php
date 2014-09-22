<?php
namespace Cstap\Kintone\Common\Exception;
use Cstap\Kintone\KintoneMessageFactory;

class KintoneException extends \RuntimeException
{

    public function getLocaleMessage($locale = 'ja')
    {
        $message = KintoneMessageFactory::getInstance()->get(parent::getMessage(), $locale);
        
        return strlen($message) ? $message : "Unknown error";
    }
}
