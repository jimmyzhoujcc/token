<?php
require_once 'DingTalk.php';

	$dingTalk = new DingTalk($congfig = array('agentid' => '160468771','corpid' => 'ding4b485200f87d6b1735c2f4657eb6378f    ' ,'corpsecret' => 'V82q0eS177ApOH7wVNMhEBJjAs6AX7dDzTsROVOdhsAzBi2zEszr7gXZ86OXtKZo','ssosecret' => 'b-IDdaxMsSb24t9qEVoz3-JF3BUW2il6wfwl-rdVGhuwTlejTMol-6SMBkVLsGmU'));
	$init = $dingTalk->PCinit();
	$department = $dingTalk->lists(); //获取部门
	$users = $dingTalk->usersDetail('59013164');
	$admin = $dingTalk->admin(); //获取管理员
	$count = $dingTalk->count();
	$userByCode = $dingTalk->code('c65e81c0cb9b3602a9137dcea66231db');
	$ssotoken = $dingTalk->ssoToken();
	$accesstoken = $dingTalk->getToken();

	// return json([
	// 	'ddinit' => $ddinit,
	// 	'department' =>$department,
	// 	'admin' => $admin,
	// 	'users' => $users,
	// 	'count' => $count,
	// 	'userbycode' => $userByCode,
	// 	'ssotoken' => $ssotoken,
	// 	'accesstoken' => $accesstoken
	// ],202);
	// print_r($department);
	// print_r($ddinit['init']);
?>

