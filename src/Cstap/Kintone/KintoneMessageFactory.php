<?php

namespace Cstap\Kintone;
use Symfony\Component\Yaml\Yaml;

/**
 * Description of KintoneMessageFactory
 *
 * @author T.Ohuchi
 */
class KintoneMessageFactory
{
    private static $instance;
    private $messages = array();
    private $locales = array("ja", "en");

    /**
     * __construct
     */
    private function __construct()
    {
        $this->loadFiles();
    }
    
    /**
     * getInstance
     * 
     * @return $this
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new KintoneMessageFactory();
        }
        
        return self::$instance;
    }
    
    /**
     * loadFiles
     */
    public function loadFiles()
    {
        $dir = __DIR__."/Resources/translations";
        $this->messages = array();
        
        foreach ($this->locales as $locale) {
            $fpath = sprintf("%s/validators.%s.yml", $dir, $locale);
            if (!file_exists($fpath)) {
                continue;
            }
            $this->messages[$locale] = (new Yaml())->parse(file_get_contents($fpath));
        }
    }
    
    /**
     * getAll
     * 
     * @param string $locale
     * @return array
     */
    public function getAll($locale = null)
    {
        $locale = strtolower(trim((string) $locale));
        if (!strlen($locale)) {
            return $this->messages;
        }
        
        return !empty($this->messages[$locale]) ? $this->messages[$locale] : array();
    }
    
    /**
     * get
     * 
     * @param string $key
     * @param string $locale
     * @return string
     */
    public function get($key, $locale = "ja")
    {
        $key = strtolower(trim((string) $key));
        $locale = strtolower(trim((string) $locale));
        
        return !empty($this->messages[$locale][$key]) ? $this->messages[$locale][$key] : null;
    }
}
