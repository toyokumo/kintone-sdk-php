<?php

namespace Cstap\Kintone\Plugin;

use Guzzle\Common\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Guzzle\Common\Collection;
use Cstap\Kintone\Common\Exception\KintoneException;

class KintoneError implements EventSubscriberInterface
{
    protected $config;
    
    
    /**
     * @var \Guzzle\Http\Message\Request
     */
    protected $request;
    
    /**
     * @var \Guzzle\Http\Message\Response
     */
    protected $response;
    
    /**
     * @var \Guzzle\Http\Exception\CurlException
     */
    protected $exception;

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
            'request.error' => ['onRequestError', 1000],
            'request.exception' => ['onRequestException', 1000]
        ];
    }

    /**
     * request error
     * @param \Guzzle\Common\Event $event
     */
    public function onRequestError(Event $event)
    {
        $this->request = $event->offsetGet('request');
        $this->response = $event->offsetGet('response');
        $body = $this->response->getBody(true);
        
        switch ($this->response->getStatusCode()) {
            case 400:
                $this->error400($body);
                break;
                
            case 520:
                $this->error520($body);
                break;
            
            default:
                $this->commonError($body);
                break;
        }
    }
    
    /**
     * curl error
     * @param \Guzzle\Common\Event $event
     * @throws KintoneException
     */
    public function onRequestException(Event $event)
    {
        $this->request = $event->offsetGet('request');
        $this->response = $event->offsetGet('response');
        $this->exception = $event->offsetGet('exception');
        
        if ($this->exception instanceof \Guzzle\Http\Exception\CurlException) {
            $errorNumber = $this->exception->getErrorNo();
            switch ($errorNumber) {
                case 6:
                    throw new KintoneException('kintone.unknown_url');
                case 35:
                    throw new KintoneException('kintone.invalid_cert');
                default:
                    throw new KintoneException('kintone.invalid_auth');
            }
        }
        throw new \Exception($this->exception->getMessage());
    }

    /**
     * is test connection
     * @return boolean
     */
    private function isTestConnection()
    {
        $url = $this->request->getUrl(true);
        if ($url->getPath() == '/k/v1/form.json' && $url->getQuery()->get('app') == -1) {
            // 通信テスト成功
            return true;
        }
        return false;
    }
    
    /**
     * error
     * @param string $body
     * @throws KintoneException
     */
    private function commonError($body)
    {
        if (preg_match("/<[^<]+>/", $body) != 0) {
            throw new KintoneException('kintone.invalid_auth');
        }else{
            $body = json_decode($body, true);
            throw new \Exception($body['message']);
        }
    }
    
    /**
     * error
     * @param string $body
     * @throws KintoneException
     */
    private function error400($body)
    {
        if (preg_match("/<[^<]+>/", $body) != 0) {
            throw new KintoneException('kintone.invalid_auth');
        }else{
            $body = json_decode($body, true);
            if ($this->isTestConnection() && $body['code'] == 'CB_VA01') {
                throw new KintoneTestConnectionSuccessException('success'); // 通信テスト成功
            }
            throw new \Exception($body['message']);
        }
    }
    
    /**
     * error
     * @param string $body
     * @throws KintoneException
     */
    private function error520($body)
    {
        if (preg_match("/<[^<]+>/", $body) != 0) {
            throw new KintoneException('kintone.invalid_auth');
        }else{
            $body = json_decode($body, true);
            $code = $body['code'];
            switch ($code) {
                case 'CB_AU01':
                    throw new KintoneException('kintone.invalid_auth');
                default:
                    throw new \Exception($body['message']);
            }
        }
    }

}
