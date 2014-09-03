<?php
namespace Cstap\Kintone;

use Guzzle\Service\Client as BaseClient;
use Guzzle\Common\Collection;
use Guzzle\Service\Description\ServiceDescription;
use Cstap\Kintone\Plugin\KintoneAuth;
use Cstap\Kintone\Plugin\KintoneError;
use Guzzle\Plugin\Log\LogPlugin;

/**
 * Description of KintoneClientBase
 *
 * @author 管理者
 */
class KintoneClientBase extends BaseClient
{
    const KINTONE_PREFIX = "/k/";
    const KINTONE_API_VERSTION = "v1";
    
    /**
     * factory
     * 
     * @param array $config
     * @return \self
     */
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

        $client = new self(null, $config);
        $client->addSubscriber(new KintoneAuth($config->toArray()));
        $client->addSubscriber(new KintoneError($config->toArray()));
        $client->setDescription(ServiceDescription::factory(__DIR__ . "/Resources/config/kintone.json"));

        $logPlugin = LogPlugin::getDebugPlugin(TRUE, fopen(__DIR__  . '/../../../app/logs/kintone.log', 'a'));
        $client->addSubscriber($logPlugin);
        
        return $client;
    }

    /**
     * getPathBase
     * 
     * @param array $config
     * @param integer $guestSpaceId
     * @return string
     * ex) https://subdomain.cybozu.com/k/v1/
     */
    public static function getApiPathBase($config, $guestSpaceId = 0)
    {
        $guestSpaceId = (integer) $guestSpaceId;
        if ($guestSpaceId) {
            return sprintf("%sguest/%d/%s", self::getURLBase($config), $guestSpaceId, self::KINTONE_API_VERSTION);
        }
        
        return self::getURLBase($config).self::KINTONE_API_VERSTION;
    }
    
    /**
     * getKintoneBaseURL
     * 
     * @param array $config
     * @return string
     * ex) https://subdomain.cybozu.com/k/
     */
    public static function getURLBase($config)
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

        return $ret.self::KINTONE_PREFIX;
    }
    
    /**
     * Magic method used to retrieve a command
     *
     * @param string $method Name of the command object to instantiate
     * @param array  $args   Arguments to pass to the command
     *
     * @return mixed Returns the result of the command
     * @throws BadMethodCallException when a command is not found
     * @see Guzzle\Service\Client
     */
    public function __call($method, $args)
    {
        $guestSpaceId = !empty($args[0]["guestSpaceId"]) ? $args[0]["guestSpaceId"] : 0;
        $this->setBaseUrl(self::getApiPathBase(self::getConfig()->toArray(), $guestSpaceId));
        
        return $this->getCommand($method, isset($args[0]) ? $args[0] : array())->getResult();
    }
}
