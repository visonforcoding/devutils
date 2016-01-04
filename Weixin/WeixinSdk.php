<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2015-6-5 15:22:53 by 曹文鹏 , caowenpeng1990@126.com
 */

namespace Utils\Weixin;

use Utils\Cache\Cache;
use Utils\HttpClient;

class WeixinSdk {

    const WEIXIN_API_URL = 'https://api.weixin.qq.com/cgi-bin/';
    const TOKEN_NAME = 'wxaccesstoken';

    protected $app_id;
    protected $app_secret;
    protected $access_token;
    public $http;

    public function __construct() {
        $this->http = new HttpClient();
    }

    /**
     * 设置 app_id 和 key
     * @param type $app_id
     * @param type $app_secret
     */
    public function setKey($app_id, $app_secret) {
        $this->app_id = $app_id;
        $this->app_secret = $app_secret;
    }

    /**
     * 
     * 获取access_token
     * @return boolean
     */
    public function getAccessToken() {
        $cache = new Cache();
        $access_token = $cache->fileCache(self::TOKEN_NAME);
        $url = self::WEIXIN_API_URL . 'token?grant_type=client_credential&appid=' . $this->app_id . '&secret=' . $this->app_secret;
        if (is_array($access_token)) {
            $isExpires = $access_token['expires_in'] <= time() ? true : false;
        }
        if ($access_token === false || $isExpires) {
            $response = $this->http->request($url);
            if ($response !== false) {
                $response = json_decode($response);
                if (property_exists($response, 'access_token')) {
                    $token = $response->access_token;
                    $expires = $response->expires_in;
                    $expires = time() + $expires;
                    $cache->fileCache(self::TOKEN_NAME, array('access_token' => $token,
                        'expires_in' => $expires,
                        'ctime' => date('Y-m-d H:i:s')
                            )
                    );
                    return $token;
                } else {
                    return $response;
                }
            } else {
                return FALSE;
            }
        } else {
            return $access_token['access_token'];
        }
    }

    /**
     * 验证服务器安全性
     * @param type $signature   微信返回的签名
     * @param type $timestamp   微信返回的时间戳
     * @param type $nonce   微信范围的nonce值 get获取
     * @param type $token   你在微信公众平台上定义的token值
     * @return boolean
     */
    public function checkSignature($signature, $timestamp, $nonce, $token) {
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * 自定义微信菜单
     * @param array $menu
     * @return type
     */
    public function initMenu(array $menu){
        $request_url =  self::WEIXIN_API_URL.'menu/create?access_token='.$this->getAccessToken();
        return $this->http->request($request_url, 'post',  json_encode($menu,JSON_UNESCAPED_UNICODE));
    }

}
