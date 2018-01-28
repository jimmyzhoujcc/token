<?php
require_once("api/Auth.php");
require_once("api/User.php");

$auth = new Auth();
$user = new User();
$event = $_REQUEST["event"];
switch($event){
    case '':
        echo json_encode(array("error_code"=>"4000"));
        break;
    case 'getuserid':
        $accessToken = $auth->getAccessToken();
        $code = $_POST["code"];
        $userInfo = $user->getUserInfo($accessToken, $code);
        echo json_encode($userInfo, true);
        break;

    case 'get_userinfo':
        $accessToken = $auth->getAccessToken();
        $userid = $_POST["userid"];
        $userInfo = $user->get($accessToken, $userid);
        echo json_encode($userInfo, true);
        break;
    case 'jsapi-oauth':
        $href = $_GET["href"];
        $configs = $auth->getConfig($href);
        $configs['errcode'] = 0;
        echo json_encode($configs, JSON_UNESCAPED_SLASHES);
        break;
}
