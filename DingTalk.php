<?php
namespace app\index\controller;
class DingTalk
{
    /**
     * 接口调用地址
     * @var string
     */
    protected  $baseUrl = 'https://oapi.dingtalk.com/';
    protected  $taobaoUrl = 'https://eco.taobao.com/router/rest';
    /**
     * 请求头信息
     * @var array
     */
    protected  $headers = array(
        'Content-Type' => 'application/json',
    );

    /**
     * 错误信息
     * @var null
     */
    public  $error = null;

    /**
     * 钉钉的配置信息
     * @var array
     */
    protected  $config = array(
        'agentid'      => '',
        'corpid'       => '',
        'corpsecret'   => '',
        'ssosecret'    => '',       
   );

    /**
     * 实例化钉钉SDK
     * @param array $config 配置信息
     */
    public function __construct($config = array())
    {
        if (!empty($config) && is_array($config)) {
            $this->config = array_merge($this->config, $config);
        }
    }
    /**
     * 设置/获取 配置变量
     * @param  string $key
     * @param  string $value
     * @return string
     */
    public  function config($key, $value = null)
    {
        if (is_null($value)) {
            return $this->config[$key];
        } else {
            $this->config[$key] = $value;
        }
    }
    /**
     * JS-API权限验证参数生成
     * @return array
     */
    public  function jsApi()
    {
        $nonceStr  = uniqid();
        $timestamp = time();
        $config    = array(
            'agentId'   => $this->config['agentid'],
            'corpId'    => $this->config['corpid'],
            'timeStamp' => $timestamp,
            'nonceStr'  => $nonceStr,
        );
        $config['signature'] = $this->jsApiSign($nonceStr, $timestamp);
        return $config;
    }
    /**
     * JS-API权限验证参数生成
     * @return array
     */
    protected  function jsApiAuth($jsApiList='')
    {
        $nonceStr  = uniqid();
        $timestamp = time();
        $config    = array(
            'agentId'   => $this->config['agentid'],
            'corpId'    => $this->config['corpid'],
            'timeStamp' => $timestamp,
            'nonceStr'  => $nonceStr,
        );
        $config['signature'] = $this->jsApiSign($nonceStr, $timestamp);
        if($jsApiList == ''){
             $config['jsApiList'] = array(
                'runtime.info',
                'biz.contact.choose',
                'device.notification.confirm',
                'device.notification.alert',
                'device.notification.prompt',
                'biz.ding.post',
                'biz.util.uploadImage',
                'runtime.permission.requestOperateAuthCode'
                );
        }else{
             $config['jsApiList'] = $jsApiList;
        }
        return json_encode($config);
    }
     /**
     * 钉钉签名算法
     * @param  string $noncestr
     * @param  string $timestamp
     * @return string
     */
    public  function jsApiSign($noncestr, $timestamp)
    {
        $signArr = array(
            'jsapi_ticket' => $this->jsapi_ticket(),
            'noncestr'     => $noncestr,
            'timestamp'    => $timestamp,
            'url'          => 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], // 获取当前页面地址 有待优化
        );
        ksort($signArr);
        $signStr = urldecode(http_build_query($signArr));
        return sha1($signStr);
    }
    /**
     * 设置/获取 错误信息
     * @param  string $msg
     * @return string
     */
    public  function error($msg = null)
    {
        if (!is_null($msg)) {
            $this->error = $msg;
        } else {
            return $this->error;
        }
    }
    
    /**
     * 手机端初始化APP端钉钉配置
     * @param  string $agentId   agentId
     * @param  array  $jsApiList 需要使用的jsapi列表
     * @return array
     */
    public  function Minit($agentId='', $jsApiList='')
    {   
        $agentId = $agentId ==''?$this->config['agentid']:$agentId;
        $return ='<script src="http://g.alicdn.com/dingding/open-develop/1.5.1/dingtalk.js"></script>';
        $return .='<script>';
        $return .= 'dd.config(';
        $return .= $this->jsApiAuth();
        $return .= ');';
        $return .="</script>";
        return array(
            'init'   => $return,
            'corpid' => $this->config['corpid'],
        );
    }

    /**
     * PC端初始化APP端钉钉配置
     * @param  string $agentId   agentId
     * @param  array  $jsApiList 需要使用的jsapi列表
     * @return array
     */
    public  function PCinit($agentId='', $jsApiList='')
    {   
        $agentId = $agentId ==''?$this->config['agentid']:$agentId;
        $return ='<script src="https://g.alicdn.com/dingding/dingtalk-pc-api/2.7.0/index.js"></script>';
        $return .='<script>';
        $return .= 'DingTalkPC.config(';
        $return .= $this->jsApiAuth();
        $return .= ');';
        $return .="</script>";
        return array(
            'init'   => $return,
            'corpid' => $this->config['corpid'],
        );
    }

    /**
     * 获取部门列表
     * @return array|boolean
     */
    public  function lists()
    {
        $result = $this->geturl('department/list');

        if (false !== $result) {
            return $result['department'];
        } else {
            return false;
        }
    }

    /**
     * 获取部门详情
     * @param  integer $id 部门ID
     * @return array|boolean
     */
    public  function departmentInfo($id)
    {
        $params = array(
            'id' => $id,
        );

        $result = $this->geturl('department/get', $params);

        if (false !== $result) {
            return $result;
        } else {
            return false;
        }
    }
    /**
     * 获取部门用户USERID列表
     * @param  integer $departmentId
     * @return array|boolean
     */
    public  function users($departmentId)
    {
        $params = array(
            'department_id' => $departmentId,
        );

        $result = $this->geturl('user/simplelist', $params);

        if (false !== $result) {
            return $result['userlist'];
        } else {
            return false;
        }
    }

    /**
     * 获取部门用户详情列表
     * @param  [type] $departmentId [description]
     * @return [type]               [description]
     */
    public  function usersDetail($departmentId)
    {
        $params = array(
            'department_id' => $departmentId           
        );
        $result = $this->geturl('user/list', $params);
        if(false !== $result){          
            return $result['userlist'];
        }else{
            return false;
        }
    }
    /**
     * 获取企业员工人数
     * @param  integer $active  0:总数，1:已激活
     * @return array|boolean
     */
    public  function count($active = 0)
    {
        $params = array(
            'onlyActive' => $active,
        );

        $result = $this->geturl('user/get_org_user_count', $params);

        if (false !== $result) {
            return $result['count'];
        } else {
            return false;
        }
    }

    /**
     * 获取用户详情
     * @param  string $userid 员工在企业内的UserID
     * @return array|boolean
     */
    public  function user($userid)
    {
        $params = array(
            'userid' => $userid,
        );

        $result = $this->geturl('user/get', $params);

        if (false !== $result) {
            return $result;
        } else {
            return false;
        }
    }

    /**
     * 获取管理员列表
     * @return array|boolean
     */
    public  function admin()
    {
        $result = $this->geturl('user/get_admin');

        if (false !== $result) {
            return $result['adminList'];
        } else {
            return false;
        }
    }

    /**
     * 通过CODE换取用户身份
     * @param  string $code requestAuthCode接口中获取的CODE
     * @return string|boolean
     */
    public  function code($code)
    {
        $params = array(
            'code' => $code,
        );

        $result = $this->geturl('user/getuserinfo', $params);

        if (false !== $result) {
            return $result['userid'];
        } else {
            return false;
        }
    }
    /**
     * 获取ACCESS_TOKEN
     * @return string|boolean
     */
    public  function getToken()
    {
        
       if ($rs = $this->getCache('DINGTOKEN') != null)  {
			return $rs;
		}

        $params = array(
            'corpid'     => $this->config['corpid'],
            'corpsecret' => $this->config['corpsecret'],
        );

        $result = $this->geturl('gettoken', $params, false);

        if ($result) {
            $token = $result['access_token'];
            $this->setCache('DINGTOKEN',$token,7100);
            $this->setCache('DINGTOKEN'.'_time',time());
            return $token;
        }
        return false;
       
    }

    /**
     * 获取 免登SsoToken
     * @return string|boolean
     */
    public  function ssoToken()
    {
        $params = array(
            'corpid'     => $this->config['corpid'],
            'corpsecret' => $this->config['ssosecret'],
        );

        $result = $this->geturl('sso/gettoken', $params, false);

        if (false !== $result) {
            return $result['access_token'];
        } else {
            return false;
        }
    }
    /**
     * 企业会话消息异步发送
     * @param  string   $touser  接收会话用户[,分割]    非必填
     * @param  string   $toparty 接收会话部门[,分割]    非必填
     * @param  string   $msgtype 消息类型
     * @param  array    $msgtype 消息体
     *                          "text": {
     *                              "content": "张三的请假申请"
     *                          }
     *                          
     *                          "image": {
     *                              "media_id": "MEDIA_ID"
     *                          }
     *
     *                          "voice": {
     *                              "media_id": "MEDIA_ID",
     *                              "duration": "10" //整数小于60秒时长
     *                          }
     *
     *                         "file": {
     *                              "media_id": "MEDIA_ID"
     *                          }
     *
     *                          "link": {
     *                              "messageUrl": "http://s.dingtalk.com/market/dingtalk/error_code.php",
     *                              "picUrl":"@lALOACZwe2Rk",
     *                              "title": "测试",
     *                              "text": "测试" //消息描述
     *                          }
     *
     *                          "oa": {
                                    "message_url": "http://dingtalk.com",
                                    "head": {
                                        "bgcolor": "FFBBBBBB",
                                        "text": "头部标题"
                                    },
                                    "body": {
                                        "title": "正文标题",
                                        "form": [
                                            {
                                                "key": "姓名:",
                                                "value": "张三"
                                            },
                                            {
                                                "key": "年龄:",
                                                "value": "20"
                                            },
                                            {
                                                "key": "身高:",
                                                "value": "1.8米"
                                            },
                                            {
                                                "key": "体重:",
                                                "value": "130斤"
                                            },
                                            {
                                                "key": "学历:",
                                                "value": "本科"
                                            },
                                            {
                                                "key": "爱好:",
                                                "value": "打球、听音乐"
                                            }
                                        ],
                                        "rich": {
                                            "num": "15.6",
                                            "unit": "元"
                                        },
                                        "content": "大段文本大段文本大段文本大段文本大段文本大段文本大段文本大段文本大段文本大段文本大段文本大段文本",
                                        "image": "@lADOADmaWMzazQKA",
                                        "file_count": "3",
                                        "author": "李四 "
                                    }
                                }
     *
     *                          
     * @return string|boolean
     */
    public function taobaoMessage($touser='',$toparty='',$msgtype='text',$message ='' ){
       $data['method'] = 'dingtalk.corp.message.corpconversation.asyncsend';
       $data['format'] = 'json';
       $data['v']='2.0';
       $data['session'] = $this->getToken();
       $data['timestampv']=date('Y-m-d H:i:s',time());
       $params['agent_id']=$this->config['agentid'];
        $url = $this->taobaoUrl.'?';
        foreach ($data as $key => $value)
        {
            $url = $url . $key . "=" . $value . "&";
        }
        
        $url = substr($url, 0, $length - 1);
  
        dump($url);
       if($touser !=''){
            $params['userid_list']  =$touser;
       }
       if($toparty !=''){
            $params['dept_id_list']  =$toparty;
       }
       if($msgtype !=''){
            $params['msgtype']  =$msgtype;
       }
       switch ($msgtype) {
           case 'text':
               $params["msgcontent"]= $message;
               break;
           case 'image':
               $params["msgcontent"]= $message;
               break;
           case 'voice':
               $params["msgcontent"]= $message;
               break;
           case 'file':
               $params["msgcontent"]= $message;
               break;
          case 'link':
               $params["msgcontent"]= $message;
               break;
          case 'oa':
               $params["msgcontent"]= $message;
               break;
           
       }
       dump($url);
       dump(json_encode($params, JSON_UNESCAPED_UNICODE));
       $result = $this->http($url, 'POST', json_encode($params, JSON_UNESCAPED_UNICODE), $this->headers);
       
        return $result;
    }
    public function oaMessage($code = '',$touser='',$toparty='',$msgtype='text',$message ='' ){
       if($touser !=''){
            $params['touser']  =$touser;
       }
       if($toparty !=''){
            $params['toparty']  =$toparty;
       }
       if($msgtype !=''){
            $params['msgtype']  =$msgtype;
       }
       switch ($msgtype) {
           case 'text':
               $params["text"]= $message;
               break;
           case 'image':
               $params["image"]= $message;
               break;
           case 'voice':
               $params["voice"]= $message;
               break;
           case 'file':
               $params["file"]= $message;
               break;
          case 'link':
               $params["link"]= $message;
               break;
          case 'oa':
               $params["oa"]= $message;
               break;
           
       }

       $params['agentid'] = $this->config['agentid'];
       $params['code']= $code;
       dump($params);
        $result = $this->posturl('message/sendByCode', $params);
        if (false !== $result) {
            return $result;
        } else {
            return false;
        }      
        
    }
    /**
     * 获取jsapi_ticket
     * @return string|boolean
     */
    protected  function jsapi_ticket()
    {

        if ($rs = $this->getCache('JSAPITICKET') != null)  {
			return $rs;
		}
        
        $result = $this->geturl('get_jsapi_ticket');
        if (false !== $result) {
            $jsapiTicket =  $result['ticket'];
            $this->setCache('JSAPITICKET',$jsapiTicket,7100);
            $this->setCache('JSAPITICKET'.'_time',time());
            return $jsapiTicket;
        } 
        return false;
    }
    /**
     * GET 方式请求接口
     * @param  string  $api
     * @param  array   $params
     * @param  boolean $token
     * @return array|boolean
     */
    public  function geturl($api, $params = array(), $token = true)
    {
        $url = $this->baseUrl . $api;

        if ($token === true) {
                $access_token = $this->getToken();
                $params['access_token'] = $access_token;
            }
        

        $url .= '?' . http_build_query($params);

        $result = $this->http($url, 'GET', $params, $this->headers);

        if ($result !== false) {
            $result = json_decode($result, true);
            if ($result['errcode'] == 0) {
                return $result;
            } else {
                $this->error($result['errmsg']);
                return false;
            }
        } else {
            return false;
        }
    }

    /**
     * POST 方式请求接口
     * @param  string $api
     * @param  array  $params
     * @return array|boolean
     */
    public  function posturl($api, $params,$url='')
    {

        $access_token = $this->getToken();
        
        $url = $this->baseUrl . $api . '?access_token=' . $access_token;

        $result = $this->http($url, 'POST', json_encode($params, JSON_UNESCAPED_UNICODE), $this->headers);
        
        if ($result !== false) {
            $result = json_decode($result, true);
            if ($result['errcode'] == 0) {
                return $result;
            } else {
                $this->error($result['errmsg']);
                return false;
            }
        } else {
            return false;
        }
    }
    /**
     * curl操作函数
     * @param  string $url        请求地址
     * @param  string $method     提交方式
     * @param  array  $postFields 提交内容
     * @param  array  $header     请求头
     * @return mixed              返回数据
     */
    public  function http($url, $method = 'GET', $postFields = null, $headers = null)
    {
        $method = strtoupper($method);
        if (!in_array($method, array('GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'HEAD', 'OPTIONS'))) {
            return false;
        }

        $opts = array(
            CURLOPT_CUSTOMREQUEST  => $method,
            CURLOPT_URL            => $url,
            CURLOPT_FAILONERROR    => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_CONNECTTIMEOUT => 30,
        );

        if ($method == 'POST' && !is_null($postFields)) {
            $opts[CURLOPT_POSTFIELDS] = $postFields;
        }

        if (strlen($url) > 5 && strtolower(substr($url, 0, 5)) == 'https') {
            $opts[CURLOPT_SSL_VERIFYPEER] = false;
            $opts[CURLOPT_SSL_VERIFYHOST] = false;
        }

        if (!empty($headers) && is_array($headers)) {
            $httpHeaders = array();
            foreach ($headers as $key => $value) {
                array_push($httpHeaders, $key . ':' . $value);
            }
            $opts[CURLOPT_HTTPHEADER] = $httpHeaders;
        }

        $ch = curl_init();
        curl_setopt_array($ch, $opts);
        $data = curl_exec($ch);
        $err  = curl_errno($ch);
        curl_close($ch);
        if ($err > 0) {
            $this->error(curl_error($ch));
            return false;
        } else {
            return $data;
        }
    }
    /**
	 * 设置缓存，按需重载
	 * @param string $cachename
	 * @param mixed $value
	 * @param int $expired
	 * @return boolean
	 */
	protected function setCache($cachename,$value,$expired=''){
		//TODO: set cache implementation
		return false;
	}

	/**
	 * 获取缓存，按需重载
	 * @param string $cachename
	 * @return mixed
	 */
	protected function getCache($cachename){
		//TODO: get cache implementation
		return false;
	}

	/**
	 * 清除缓存，按需重载
	 * @param string $cachename
	 * @return boolean
	 */
	protected function removeCache($cachename){
		//TODO: remove cache implementation
		return false;
	}

}