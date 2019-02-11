<?php 
	// 继承父类件
	require_once('../ApiCall.class.php');
	/**
	* 用户功能类
	*/
	class User extends ApiCall
	{
		private $dbType = 'mysql';
		private $servername = "localhost";
		private $username = "root";
		private $password = "123123";
		private $dbname = "api";
		protected $ip = '';
		protected $connect = null;
		private $nowDate = '';
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
		}
		// 连接数据库
		private function _connect(){
			try{
				$this->connect = new PDO("{$this->dbType}:host={$this->servername};dbname={$this->dbname};charset=utf8mb4", $this->username, $this->password);
				// 设置 PDO  
		    	$this->connect->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	    	}catch(PDOException $e){
			    die(json_encode(array('status' => 0, 'code' => 1, 'message' => '程序异常'), JSON_UNESCAPED_UNICODE));
			}
		}

		public function register(){

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