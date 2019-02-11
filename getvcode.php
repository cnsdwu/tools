<?php
	// 引入类文件
	require_once('ValidateCode.class.php');
	// 开启session
	session_start();
	$vcode = new ValidateCode();  //实例化一个对象
	// 输出图片
	$vcode->doimg(); 
	// 验证码保存到SESSION中
	$_SESSION['vcode_session'] = $vcode->getCode();
?>