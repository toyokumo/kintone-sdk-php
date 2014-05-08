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
     * @throws \Exception
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
                    throw new \Exception('URLもしくはサブドメインが不正です');
                case 35:
                    throw new \Exception('クライアント証明書もしくは証明書のパスワードが異なります');
                default:
                    throw new \Exception('認証情報を正しく設定してください');
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
     * @throws \Exception
     */
    private function commonError($body)
    {
        if (preg_match("/<[^<]+>/", $body) != 0) {
            throw new \Exception('認証情報を正しく設定してください');
        }else{
            $body = json_decode($body, true);
            throw new \Exception($body['message']);
        }
    }
    
    /**
     * error
     * @param string $body
     * @throws \Exception
     */
    private function error400($body)
    {
        if (preg_match("/<[^<]+>/", $body) != 0) {
            throw new \Exception('認証情報を正しく設定してください');
        }else{
            $body = json_decode($body, true);
            if ($this->isTestConnection() && $body['code'] == 'CB_VA01') {
                throw new \Exception('success'); // 通信テスト成功
            }
            throw new \Exception($body['message']);
        }
    }
    
    /**
     * error
     * @param string $body
     * @throws \Exception
     */
    private function error520($body)
    {
        if (preg_match("/<[^<]+>/", $body) != 0) {
            throw new \Exception('認証情報を正しく設定してください');
        }else{
            $body = json_decode($body, true);
            $code = $body['code'];
            switch ($code) {
                case 'CB_AU01':
                    throw new \Exception('認証情報を正しく設定してください');
                default:
                    throw new \Exception($body['message']);
            }
        }
    }

}
