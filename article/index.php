<?php
	// 配置文件
	require_once('../config.php');
	// 加密类文件
	require_once('Article.class.php');
	// 动作
	$action = 'get';
	// 时间
	$time = time(); // 时间戳
	$encryptedData = ''; // 密文
	$iv = ''; // 加密向量
	$code = ''; // 用来获取sessionKey
	$articleId = 0;
	$commentId = 0;
	$content = '';
	// 判断参数
	if(!empty(@$_POST['token'])){ // post提交
		$token = $_POST['token']; // token
		if(!empty(@$_POST['time'])){ // 时间不为空
			if($_POST['time'] > 0 && $_POST['time'] <= 2147483647){
				$time = $_POST['time'];
			}
		}
		if(!empty(@$_POST['action'])){ // 动作
			$action = $_POST['action'];
		}
		if(@$_POST['action'] != 'get' && @$_POST['action'] != 'getComment'){ // 需要用户信息
			if(empty(@$_POST['encryptedData']) || empty(@$_POST['code']) || empty(@$_POST['iv'])){ // 验证参数
				die(json_encode(array('status' => 0, 'code' => 3, 'message' => '参数缺失'), JSON_UNESCAPED_UNICODE));
			}
			// 得到参数
			$encryptedData = $_POST['encryptedData'];
			$code = $_POST['code'];
			$iv = $_POST['iv'];
		}
		// 需要文章id
		if($action == 'getComment' || $action == 'like' || $action == 'comment'){
			if(@$_POST['articleId'] < 1){
				die(json_encode(array('status' => 0, 'code' => 3, 'message' => '参数缺失'), JSON_UNESCAPED_UNICODE));
			}
			$articleId = $_POST['articleId'];
		}
		// 评论时
		if($action == 'comment'){
			// 内容长度
			if(strlen(@$_POST['content']) < 1 || strlen(@$_POST['content']) > 1000){
				die(json_encode(array('status' => 0, 'code' => 3, 'message' => '参数缺失'), JSON_UNESCAPED_UNICODE));
			}
			$content = $_POST['content'];
		}
		// 赞评论
		if($action == 'admire'){
			if(@$_POST['commentId'] < 1){
				die(json_encode(array('status' => 0, 'code' => 3, 'message' => '参数缺失'), JSON_UNESCAPED_UNICODE));
			}
			$commentId = $_POST['commentId'];
		}
	}else if(!empty(@$_GET['token'])){ // get提交 同上
		$token = $_GET['token'];
		if(!empty(@$_GET['time'])){
			if($_GET['time'] > 0 && $_GET['time'] <= 2147483647){
				$time = $_GET['time'];
			}
		}
		if(!empty(@$_GET['action'])){
			$action = $_GET['action'];
		}
		if(@$_GET['action'] != 'get' && @$_GET['action'] != 'getComment'){
			if(empty(@$_GET['encryptedData'] )|| empty(@$_GET['code']) || empty(@$_GET['iv'])){
				die(json_encode(array('status' => 0, 'code' => 3, 'message' => '参数缺失'), JSON_UNESCAPED_UNICODE));
			}
			$encryptedData = $_GET['encryptedData'];
			$code = $_GET['code'];
			$iv = $_GET['iv'];
		}
		// 需要文章id
		if($action == 'getComment' || $action == 'like' || $action == 'comment' || $action == 'admire'){
			if(@$_GET['articleId'] < 1){
				die(json_encode(array('status' => 0, 'code' => 3, 'message' => '参数缺失'), JSON_UNESCAPED_UNICODE));
			}
			$articleId = $_GET['articleId'];
		}
		if($action == 'comment'){
			if(strlen(@$_GET['content']) < 1 || strlen(@$_GET['content']) > 1000){
				die(json_encode(array('status' => 0, 'code' => 3, 'message' => '参数缺失'), JSON_UNESCAPED_UNICODE));
			}
			$content = $_GET['content'];
		}
		if($action == 'admire'){
			if(@$_GET['commentId'] < 1){
				die(json_encode(array('status' => 0, 'code' => 3, 'message' => '参数缺失'), JSON_UNESCAPED_UNICODE));
			}
			$commentId = $_GET['commentId'];
		}
	}else{ // 没有token
		die(json_encode(array('status' => 0, 'code' => 3, 'message' => 'token不能为空'), JSON_UNESCAPED_UNICODE));
	}


	// 实例化类
	$article = new Article($servername, $username, $password, $dbname, $token);
	// 获取文章和评论不需要用户信息
	if($action == 'get'){
		echo $article->get($time);
	}elseif ($action == 'getComment') {
		echo $article->getComment($articleId);
	}else{
	    include_once "wxBizDataCrypt.php";
	    $sessionKey = json_decode(file_get_contents("https://api.weixin.qq.com/sns/jscode2session?appid=wx97060377dcf9c031&secret=15e54738bae15156f79f65deb865f6cf&js_code={$code}&grant_type=authorization_code"))->session_key;
		$appid = 'wx97060377dcf9c031';
		$pc = new WXBizDataCrypt($appid, $sessionKey);
		$errCode = $pc->decryptData($encryptedData, $iv, $data);

		if ($errCode == 0) {
		    $array = (array)json_decode($data);
		} else {
		    die(json_encode(array('status' => 0, 'code' => 3, 'message' => '解密失败:'.$errCode), JSON_UNESCAPED_UNICODE));
		}
		$article->getUserId($array);
		if($action == 'like'){
			echo $article->like($articleId);
		}else if($action == 'comment'){
			echo $article->addComment($articleId, $content);
		}else if($action == 'admire'){
			echo $article->admire($commentId);
		}
	}
 ?>