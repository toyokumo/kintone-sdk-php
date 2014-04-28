<?php

namespace Cstap\Kintone;

use Guzzle\Service\Client as BaseClient;
use Guzzle\Common\Collection;
use Guzzle\Service\Description\ServiceDescription;
use Cstap\Kintone\Plugin\KintoneAuth;
use Cstap\Kintone\Plugin\KintoneError;
use Guzzle\Plugin\Log\LogPlugin;
//use Guzzle\Service\Builder\ServiceBuilder;
//use Guzzle\Service\Builder\ServiceBuilderLoader;

class KintoneClient extends BaseClient
{
    const API_PREFIX = "/k/v1";

    public static function factory($config = [])
    {
        $default = [
            'useClientCert' => false,
            'domain' => "cybozu.com"
        ];

        $required = [
            'domain',
            'subdomain',
            'login',
            'password'
        ];

        $config = Collection::fromConfig($config, $default, $required);
        $baseURL = self::getKintoneBaseURL($config);

        $client = new self($baseURL, $config);
        $client->addSubscriber(new KintoneAuth($config->toArray()));
        $client->addSubscriber(new KintoneError($config->toArray()));
        $client->setDescription(ServiceDescription::factory(__DIR__ . "/Resources/config/kintone.json"));

        $logPlugin = LogPlugin::getDebugPlugin(TRUE, fopen(__DIR__  . '/../../../app/logs/kintone.log', 'a'));
        $client->addSubscriber($logPlugin);
        
        return $client;
    }

    public static function getKintoneBaseURL($config)
    {
        $subdomain = $config['subdomain'];
        $useClientCert = $config['useClientCert'];

        $ret = "https://" . $subdomain;

        if (strpos($subdomain, '.') === false) {
            if ($useClientCert) {
                $ret .= ".s";
            }

            $ret .= "." . $config['domain'];
        }
        $ret .= static::API_PREFIX;

        return $ret;
    }

    /**
     * test connection
     * 認証テストは不明なアプリIDを指定
     * @return boolean
     * @throws \Exception
     */
    public function testConnection($appId = -1)
    {
        try {
            $response = $this->getFormFields(['app' => $appId]);
        } catch (\Exception $e) {
            return true;
        }
        
        if($response instanceof \Guzzle\Http\Message\Response) {
            $url = $response->getEffectiveUrl();
            if($url && strpos($url, $this->getBaseUrl()) !== 0) {
                throw new \Exception('指定されたURLが無効です');
            }
        }
        
        return true;
    }

}
