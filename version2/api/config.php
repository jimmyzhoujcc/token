<?php
// define('DIR_ROOT', dirname(__FILE__).'/');
define("OAPI_HOST", "https://oapi.dingtalk.com");

define("CORPID", "ding4b485200f87d6b1735c2f4657eb6378f");
define("SECRET", "V82q0eS177ApOH7wVNMhEBJjAs6AX7dDzTsROVOdhsAzBi2zEszr7gXZ86OXtKZo");
define("AGENTID", "160468771");//必填，在创建微应用的时候会分配
define("ENCODING_AES_KEY", "123456"); //加解密需要用到的token，普通企业可以随机填写,例如:123456
define("TOKEN", "r4ZJEVKmj6SOMYZ4Fa4adssi1S7OMFsadIDVUb23uO5"); //数据加密密钥。用于回调数据的加密，长度固定为43个字符，从a-z, A-Z, 0-9共62个字符中选取,您可以随机生成