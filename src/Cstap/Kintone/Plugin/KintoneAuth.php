<?php

namespace Cstap\Kintone\Plugin;

use Guzzle\Common\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Guzzle\Common\Collection;
use Cstap\Kintone\Common\Exception\KintoneException;

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
                throw new KintoneException("kintone.empty_basic_password");
            }
        }

        //$opts->set(CURLOPT_VERBOSE, true);
        //$opts->set(CURLOPT_STDERR, fopen($this->config->get('logfile'), 'w'));

        // クライアント証明書
        if ($this->config['useClientCert']) {
            if ($this->config['certFile'] && $this->config['certPassword']) {
                $opts = $request->getCurlOptions();
                $opts->set(CURLOPT_SSL_VERIFYPEER, true);
                $opts->set(CURLOPT_SSL_VERIFYHOST, 2);
                $opts->set(CURLOPT_SSLCERT, $this->config['certFile']);
                $opts->set(CURLOPT_SSLCERTPASSWD, $this->config['certPassword']);
            } else {
                throw new KintoneException('kintone.empty_cert_password');
            }
        }
    }

    protected function buildAuthorizationHeader()
    {
        return base64_encode($this->config['login'] . ":" . $this->config['password']);
    }
}
