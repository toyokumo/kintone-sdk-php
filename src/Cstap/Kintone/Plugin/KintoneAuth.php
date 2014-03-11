<?php

namespace Cstap\Kintone\Plugin;

use Guzzle\Common\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Guzzle\Common\Collection;

class KintoneAuth implements EventSubscriberInterface
{
    const AUTH_HEADER = "X-Cybozu-Authorization";

    protected $config;

    public function __construct($config)
    {
        $default = [
            'useClientCert'  => false, /* @TODO */
            'useBasic'       => false,
            'basicLogin'    => false,
            'basicPassword' => false,
        ];

        $required = [
            'login',
            'password'
        ];

        $this->config = Collection::fromConfig($config, $default, $required);
    }

    public static function getSubscribedEvents()
    {
        return [
            'request.before_send' => ['onBeforeSendRequest', -1000]
        ];
    }

    public function onBeforeSendRequest(Event $event)
    {
        $request = $event['request'];

        $request->setHeader(
            self::AUTH_HEADER,
            $this->buildAuthorizationHeader()
        );
        
        // Basic Auth
        if ($this->config['useBasic']) {
            if ($this->config['basicLogin'] && $this->config['basicPassword']) {
                $request->setAuth($this->config['basicLogin'], $this->config['basicPassword']);   
            } else {
                throw new \RuntimeException("When 'useBasic' is true, you must set 'basicLogin' and 'basicPassword'.");
                
            }
        }
        
    }

    protected function buildAuthorizationHeader()
    {
        return base64_encode($this->config['login'] . ":" . $this->config['password']);
    }
}
