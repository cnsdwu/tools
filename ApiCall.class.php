<?php
	/**
	* 
	*/
	class ApiCall
	{

		private $servername = "localhost";
		private $username = "root";
		private $password = "123123";
		private $dbname = "api";
		protected $ip = '';
		protected $connect = null;
		private $nowDate = '';
		protected $token = '';
		// 构造
		function __construct($servername, $username, $password, $dbname, $token)
		{
			$this->servername = $servername;
			$this->username = $username;
			$this->password = $password;
			$this->dbname = $dbname;
			$this->token = $token;
			$this->ip = $this->_ip();
			$this->nowDate = date('Y-m-d H:i:s');
			$this->_connect();
			if($this->_verifyToken() == false){
				die(json_encode(array('status' => 0, 'code' => 4, 'message' => 'token错误'), JSON_UNESCAPED_UNICODE));
			}
			// $this->_addTime();
		}
		// 添加总调用次数
		protected function _addTime(){
			$stmt = $this->connect->prepare("UPDATE users SET `total_time`=`total_time`+1, `last_date`=:lastDate, `ip`=:ip WHERE `token`=:token");
		   	// $stmt->bindParam(':time', $text);
		   	$stmt->bindParam(':lastDate', $this->nowDate);
		   	$stmt->bindParam(':ip', $this->ip);
		   	$stmt->bindParam(':token', $this->token);
		    // 使用 exec() ，没有结果返回 
		    $stmt->execute();
		    if($stmt->rowCount() > 0){
		    	return true;
		    }else{
		    	return false;
		    }
		}
		// 判断是调用量是否超过上限
		protected function _limitTodayTime(){
			$stmtQuery = $this->connect->prepare("SELECT `today_time`, `limit_today_time` FROM users WHERE `token`=:token");
			$stmtQuery->bindParam(':token', $this->token);
			$stmtQuery->execute();
		    $arrResult = $stmtQuery->fetch();
		    if($arrResult){
		    	if($arrResult['limit_today_time'] == 0){
		    		return true;
		    	}
		    	if($arrResult['today_time'] < $arrResult['limit_today_time']){
		    		return true;
		    	}else{
		    		return false;
		    	}
		    	
		    }else{
		    	return false;
		    }

		}
		// 验证token
		private function _verifyToken(){
			$stmtQuery = $this->connect->prepare("SELECT `id` FROM users WHERE `token`=:token");
			$stmtQuery->bindParam(':token', $this->token);
			$stmtQuery->execute();
		    $arrResult = $stmtQuery->fetch();
		    if($arrResult){
		    	return true;
		    }else{
		    	return false;
		    }
		}
		// 连接数据库
		private function _connect(){
			try{
				$this->connect = new PDO("mysql:host={$this->servername};dbname={$this->dbname};charset=utf8mb4", $this->username, $this->password);
				// 设置 PDO 错误模式，用于抛出异常
		    	$this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    	}catch(PDOException $e){
			    die(json_encode(array('status' => 0, 'code' => 1, 'message' => '程序异常'), JSON_UNESCAPED_UNICODE));
			}
		}
		// 获取ip
		private function _ip(){
			$ip = '1.0.0.1';
		    if(isset($_SERVER['HTTP_CLIENT_IP'])){
		    	$ip = $_SERVER['HTTP_CLIENT_IP'];
		    }elseif(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
		    	$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		    }elseif(isset($_SERVER['REMOTE_ADDR'])){
		    	$ip = $_SERVER['REMOTE_ADDR'];
		    }
		    return $ip;
		}
		// 析构
		function __destruct(){
			$this->connect = null;
		}
	}
?>