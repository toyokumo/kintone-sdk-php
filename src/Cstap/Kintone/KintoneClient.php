<?php

namespace Cstap\Kintone;

use Cstap\Kintone\KintoneClientBase as ClientBase;
use Cstap\Kintone\Common\Exception\KintoneTestConnectionSuccessException;

class KintoneClient extends ClientBase
{
    // @todo: 下記const定数はゲストスペース非対応のため相応しくないが以下に影響
    // * printCreator:
    //  - src\App\Pdf\Populator\QrPopulator.php
    const API_PREFIX = "/k/v1";

    /**
     * getKintoneBaseURL
     * 
     * @param array $config
     * @param integer $guestSpaceId
     * @return string
     * @todo: 本メソッドはゲストスペース非対応時以下メソッドのためのエイリアス
     * * printCreator:
     *  - src\App\Pdf\Populator\QrPopulator.php
     */
    public static function getKintoneBaseURL($config, $guestSpaceId = 0)
    {
        return parent::getApiPathBase($config, $guestSpaceId);
    }

    /**
     * getKintoneBrowseUrl
     * ブラウジングする際のURL
     * 
     * @param array $config
     * @param integer $appId
     * @param integer $guestSpaceId
     * @return string
     * ex) https://subdomain.cybozu.com/k/[appId]
     */
    public static function getKintoneBrowseUrl($config, $appId, $guestSpaceId = 0)
    {
        $appId = (integer) $appId;
        $guestSpaceId = (integer) $guestSpaceId;
        
        $base = self::getURLBase($config);
        if ($guestSpaceId) {
            return $base.sprintf("guest/%d/%d/", $guestSpaceId, $appId);
        }
        
        return $base.$appId;
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
        } catch (KintoneTestConnectionSuccessException $e) {
            return true;
        }
        
        if($response instanceof \Guzzle\Http\Message\Response) {
            $url = $response->getEffectiveUrl();
            if($url && strpos($url, $this->getBaseUrl()) !== 0) {
                throw new \Exception('kintone.unknown_url');
            }
        }
        
        return true;
    }
}
