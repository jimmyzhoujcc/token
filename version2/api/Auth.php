<?php
require("Http.php");
require_once("config.php");

class Auth
{
    private $http;
    private $accessToken;
    private $jsticket;
    public function __construct() {
        $this->http = new Http();
    }

    public function getAccessToken()
    {
        /**
         * 缓存accessToken。accessToken有效期为两小时，需要在失效前请求新的accessToken（注意：以下代码没有在失效前刷新缓存的accessToken）。
         */
        $accessToken = $this->accessToken;
        if (!$accessToken)
        {
            $response = $this->http->get('/gettoken', array('corpid' => CORPID, 'corpsecret' => SECRET));
            $accessToken = $response->access_token;
            $this->accessToken  = $accessToken;
        }
        return $accessToken;
    }
    
     /**
      * 缓存jsTicket。jsTicket有效期为两小时，需要在失效前请求新的jsTicket（注意：以下代码没有在失效前刷新缓存的jsTicket）。
      */
    public function getTicket($accessToken)
    {
        $jsticket  = $this->jsticket;
        if (!$jsticket)
        {
            $response = $this->http->get('/get_jsapi_ticket', array('type' => 'jsapi', 'access_token' => $accessToken));
            $jsticket = $response->ticket;
            $this->jsticket = $jsticket;
        }
        return $jsticket;
    }


    function curPageURL()
    {
        $pageURL = 'http';

        if (array_key_exists('HTTPS',$_SERVER)&&$_SERVER["HTTPS"] == "on")
        {
            $pageURL .= "s";
        }
        $pageURL .= "://";

        if ($_SERVER["SERVER_PORT"] != "80")
        {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        }
        else
        {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    public function getConfig($href)
    {
        $corpId = CORPID;
        $agentId = AGENTID;
        $nonceStr = 'abcdefg';
        $timeStamp = time();
        $url = urldecode($href);
        $corpAccessToken = $this->getAccessToken();
        $ticket = $this->getTicket($corpAccessToken);
        $signature = $this->sign($ticket, $nonceStr, $timeStamp, $url);
        
        $config = array(
            'url' => $url,
            'nonceStr' => $nonceStr,
            'agentId' => $agentId,
            'timeStamp' => $timeStamp,
            'corpId' => $corpId,
            'signature' => $signature);
        return $config;
    }
    
    
    public function sign($ticket, $nonceStr, $timeStamp, $url)
    {
        $plain = 'jsapi_ticket=' . $ticket .
            '&noncestr=' . $nonceStr .
            '&timestamp=' . $timeStamp .
            '&url=' . $url;
        return sha1($plain);
    }
    
}
