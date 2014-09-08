<?php

namespace Cstap\Kintone\Common\Exception;

use Symfony\Component\Yaml\Yaml;

class KintoneException extends \RuntimeException
{
    private static $messages = null;

    public function getLocaleMessage($locale = 'ja')
    {
        $this->loadLocaleMessages($locale);

        $message = parent::getMessage();

        if (isset(static::$messages[$locale][$message])) {
            $message = static::$messages[$locale][$message];
        }

        return $message;
    }

    private function loadLocaleMessages($locale = 'ja')
    {
        if (!isset(static::$messages[$locale])) {
            $dir = __DIR__ . "/../../Resources/translations/";
            $fname = "validators.{$locale}.yml";
            if (!file_exists($dir . $fname)) {
                $locale = 'ja';
                $fname = "validators.ja.yml";
            }
            if (!isset(static::$messages[$locale])) {
                static::$messages[$locale] = (new Yaml())->parse(file_get_contents($dir . $fname));
            }
        }
    }
}
