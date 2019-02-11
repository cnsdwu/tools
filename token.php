<?php
	// 配置文件
	require_once('config.php');
	// 类文件
	require_once('User.class.php');
	// 开启session
	session_start();
	$email = @$_POST['email'];
	$vcode = @$_POST['vcode'];
	$vcodeSession = @$_SESSION["vcode_session"];
	// 判断
	if(@$_POST['action'] == 'register'){
		// 验证码是否正确
		if(empty($vcode) || $vcode != $vcodeSession){
			die(json_encode(array('status' => 0, 'code' => 7, 'message' => '验证码错误'), JSON_UNESCAPED_UNICODE));
		}
		// 邮箱地址是否正确
		if(!preg_match('/^[a-zA-Z0-9_-]+@[a-zA-Z0-9_-]+(\.[a-zA-Z0-9_-]+)+$/',$email) || strlen($email) > 100){
			die(json_encode(array('status' => 0, 'code' => 8, 'message' => '邮箱地址不合法'), JSON_UNESCAPED_UNICODE));
	    }
		$_SESSION['vcode_session'] = time(); // 使session失效
		$subject = '您的token信息';
		$content = '
			<h1>您的token信息</h1>
			<h3>网站地址：<a href="http://api.wwzc.cc" target="_blank">http://api.wwzc.cc</a></h3>
			<h3>token：%token%</h3>
			<hr>
			<strong>感谢您的使用!</strong>
		';
		// 总调用次数设置
		$limitTodayTime = 100000;
		$user = new User($servername, $username, $password, $dbname);
		// 添加
		echo $user->addUser($email, $subject, $content, $limitTodayTime);
	}else{
		die(json_encode(array('status' => 0, 'code' => 1, 'message' => '参数异常'), JSON_UNESCAPED_UNICODE));
	}
	
?>