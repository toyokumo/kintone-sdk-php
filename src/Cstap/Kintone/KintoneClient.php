<?php

namespace Cstap\Kintone;

use Guzzle\Service\Client as BaseClient;
use Guzzle\Common\Collection;
use Guzzle\Service\Description\ServiceDescription;
use Cstap\Kintone\Plugin\KintoneAuth;
use Cstap\Kintone\Plugin\KintoneError;

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

}
