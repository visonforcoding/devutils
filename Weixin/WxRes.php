<?php

/**
 * Encoding     :   UTF-8
 * Created on   :   2015-10-25 17:20:06 by allen <blog.rc5j.cn> , caowenpeng1990@126.com
 */

namespace Utils\Weixin;

use Utils\Log\SimpleLog;

class WxRes {

    /**
     *  接收消息时的发送者   发送消息的对象
     * @var type 
     */
    protected $toUserName;

    /**
     * 开发者微信号  发送消息时的发送者
     * @var type 
     */
    protected $fromUserName;

    /**
     *  接收到消息的消息类型
     * @var type 
     */
    protected $postMsgType;

    /**
     * 事件消息的类型
     * @var type 
     */
    protected $event = null;

    /**
     *  微信服务器请求消息对象
     * @var type 
     */
    public $postObj;

    /**
     * 消息主体内容
     * @var type 
     */
    protected $content;

    /**
     * 
     * @param bool $debug 是否开启调试模式
     */
    public function __construct($debug = false) {
        if ($debug) {
            //调试模式
            $this->debug();
        }
        $postStr = $this->getPost();
        if ($postStr) {
            $this->postObj = $postStr;
            $this->toUserName = $postStr->FromUserName;
            $this->fromUserName = $postStr->ToUserName;
            $this->postMsgType = $postStr->MsgType;
            $this->content = trim($postStr->Content);
            if ($this->postMsgType == 'event') {
                $this->event = strtolower($postStr->Event); //官方文档有的写成大写，其实返回的是小写
            }
        }
    }

    /**
     * 返回发送对象
     * @return type
     */
    public function getToUserName() {
        return $this->toUserName;
    }

    /**
     * 返回发送者
     * @return type
     */
    public function getFromUserName() {
        return $this->fromUserName;
    }

    /**
     * 返回接收的消息类型
     * @return type
     */
    public function getPostMsgType() {
        return $this->postMsgType;
    }

    /**
     * 返回事件消息类型
     * @return type
     */
    public function getEvent() {
        return $this->event;
    }

    /**
     * 获取消息主体内容
     * @return type
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * 获取消息对象
     * @return boolean
     */
    public function getPost() {
        $postStr = file_get_contents("php://input");
        if (!empty($postStr)) {
            /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
              the best way is to check the validity of xml by yourself */
            libxml_disable_entity_loader(true);
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            return $postObj;
        } else {
            return false;
        }
    }

    /**
     * 记录所有提交信息
     */
    public function debug() {
        SimpleLog::log('调试开始', SimpleLog::DEBUG);
        SimpleLog::log(file_get_contents("php://input"), SimpleLog::DEBUG);
    }

    /**
     * 返回提交位置事件消息 获取的位置信息
     * @return array   Location_X 维度  Location_Y 经度   Label 精度
     */
    public function getLocation() {
        if ($this->postMsgType == 'location') {
            $location = array(
                'Location_X' => $this->postObj->Location_X, //维度
                'Location_Y' => $this->postObj->Location_Y, //经度
                'Label' => $this->postObj->Label  //精度
            );
            return $location;
        } else {
            return null;
        }
    }

    /**
     *  回复纯文本消息
     * @param type $content
     * @return xml
     */
    public function resText($content) {
        $xmlTpl = "<xml>
                                       <ToUserName><![CDATA[%s]]></ToUserName>
	                  <FromUserName><![CDATA[%s]]></FromUserName>
	                  <CreateTime>%d</CreateTime>
	                  <MsgType><![CDATA[text]]></MsgType>
	                  <Content><![CDATA[%s]]></Content>
	               </xml>";
        return sprintf($xmlTpl, $this->toUserName, $this->fromUserName, time(), $content);
    }

    public function resImg($content) {
        $xmlTpl = "<xml>
                        <ToUserName><![CDATA[toUser]]></ToUserName>
                        <FromUserName><![CDATA[fromUser]]></FromUserName>
                         <CreateTime>12345678</CreateTime>
                        <MsgType><![CDATA[image]]></MsgType>
                            <Image>
                            <MediaId><![CDATA[media_id]]></MediaId>
                            </Image>
                    </xml>";
        return sprintf($xmlTpl, $this->toUserName, $this->fromUserName, time(), $content);
    }

}
