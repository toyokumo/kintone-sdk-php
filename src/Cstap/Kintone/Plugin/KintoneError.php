<?php

namespace Cstap\Kintone\Plugin;

use Guzzle\Common\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Guzzle\Common\Collection;

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

    public function onRequestError(Event $event)
    {
        $this->request = $event->offsetGet('request');
        $this->response = $event->offsetGet('response');
        
        switch ($this->response->getStatusCode()) {
            case 400:
                if ($this->isTestConnection()) {
                    throw new \Exception('success'); // 通信テスト成功
                }
                $body = json_decode($this->response->getBody(true), true);
                throw new \Exception($body['message']);
                
            case 403:
                $body = $this->response->getBody(true);
                if (preg_match("/<[^<]+>/",$body,$m) != 0) {
                    throw new \Exception('認証情報を正しく設定してください');
                }else{
                    $body = json_decode($body, true);
                    throw new \Exception($body['message']);
                }
                
            case 404:
                $body = json_decode($this->response->getBody(true), true);
                throw new \Exception($body['message']);
                
            case 520:
                $body = json_decode($this->response->getBody(true), true);
                $code = $body['code'];
                switch ($code) {
                    case 'CB_AU01':
                        throw new \Exception('認証情報を正しく設定してください');
                    default:
                        throw new \Exception($body['message']);
                }
                
            default:
                $body = json_decode($this->response->getBody(true), true);
                throw new \Exception($body['message']);
        }
    }
    
    public function onRequestException(Event $event)
    {
        $this->request = $event->offsetGet('request');
        $this->response = $event->offsetGet('response');
        $this->exception = $event->offsetGet('exception');
        
        if ($this->exception instanceof \Guzzle\Http\Exception\CurlException) {
            throw new \Exception('クライアント証明書もしくは証明書のパスワードが異なります');
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
}
