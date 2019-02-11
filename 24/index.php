<?php
	// 配置文件
	require_once('../config.php');
	// 加密类文件
	require_once('EnText.class.php');
	// 动作
	$action = 'en';
	// 文本内容
	$text = trim(@$_POST['text']);
	// 判断参数
	if(!empty(@$_POST['token'])){ // post提交
		$token = $_POST['token']; // token
		if(empty($text)){ // 文本为空
			die(json_encode(array('status' => 0, 'code' => 3, 'message' => '内容不能为空'), JSON_UNESCAPED_UNICODE));
		}
		if(@$_POST['action'] == 'de'){ // 动作为解密
			$action = 'de';
		}
	}else if(!empty(@$_GET['token'])){ // get提交 同上
		$token = $_GET['token'];
		if(empty($_GET['text'])){
			die(json_encode(array('status' => 0, 'code' => 3, 'message' => '内容不能为空'), JSON_UNESCAPED_UNICODE));
		}
		if(@$_GET['action'] == 'de'){
			$action = 'de';
		}
	}else{ // 没有token
		die(json_encode(array('status' => 0, 'code' => 3, 'message' => 'token不能为空'), JSON_UNESCAPED_UNICODE));
	}
	// 判断文本长度
	if(strlen($text) > 30000){
		die(json_encode(array('status' => 0, 'code' => 9, 'message' => '内容过长'), JSON_UNESCAPED_UNICODE));
	}
	// 实例化类
	$entext = new EnText($servername, $username, $password, $dbname, $token);
	if($action == 'de'){ // 解密操作
		echo $entext->deText($text);
	}else{ // 加密操作
	    echo $entext->enText($text);
	}
 ?>