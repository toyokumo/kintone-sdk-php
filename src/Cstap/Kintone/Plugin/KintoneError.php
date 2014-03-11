<?php

namespace Cstap\Kintone\Plugin;

use Guzzle\Common\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Guzzle\Common\Collection;

class KintoneError implements EventSubscriberInterface
{
    protected $config;

    public function __construct($config)
    {
        $default = [
        ];

        $required = [
        ];

        $this->config = Collection::fromConfig($config, $default, $required);
    }

    public static function getSubscribedEvents()
    {
        return [
            'request.error' => ['onRequestError', 1000]
        ];
    }

    public function onRequestError(Event $event)
    {
        /** @TODO error handling */
        $response = $event['response'];
        /* echo $response->getStatusCode(); */
        
        /* $event->stopPropagation(); */
        

    }
}
