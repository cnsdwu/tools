<?php
	// 配置文件
	require_once('../config.php');
	// 类文件
	require_once('GetBing.class.php');
	// 动作
	$action = 'img';
	// 判断参数
	if(!empty(@$_POST['token'])){ // post提交
		$token = $_POST['token']; // token
		// 判断动作
		if(@$_POST['action'] == 'url'){
			$action = 'url';
		}else if(@$_POST['action'] == 'base64'){
			$action = 'base64';
		}
	}else if(!empty(@$_GET['token'])){ // get提交 同上
		$token = $_GET['token'];
		if(@$_GET['action'] == 'url'){
			$action = 'url';
		}else if(@$_GET['action'] == 'base64'){
			$action = 'base64';
		}
	}else{ // 没有token
		die(json_encode(array('status' => 0, 'code' => 3, 'message' => 'token不能为空'), JSON_UNESCAPED_UNICODE));
	}
	// 实例化类
	$getBing = new GetBing($servername, $username, $password, $dbname, $token);
	// 判断动作
	if($action == 'url'){
		echo $getBing->getUrl();
	}else if($action == 'base64'){
		echo $getBing->getImgBase64();
	}else{ // 直接输出图片
		$imgData = $getBing->getImg(); // 图片数据
		if($imgData){
			header('Content-type:image/jpg'); // 设置响应头
			echo $imgData;
		}else{
			echo false;
		}
	}
 ?>