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
        } catch (\Guzzle\Http\Exception\ServerErrorResponseException $e) {
            throw new \Exception('認証情報を正しく設定してください');
        } catch (\Guzzle\Http\Exception\ClientErrorResponseException $e) {
            $response = $e->getResponse();
            if($response->getStatusCode() == 400) {
                return true;
            }
            if($response->getStatusCode() == 404) {
                throw new \Exception('有効なアプリIDではありません');
            }
            throw new \Exception('通信テストに失敗しました');
        } catch (\Exception $e) {
            throw new \Exception('通信テストに失敗しました');
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
