<?php
	use PHPMailer\PHPMailer\PHPMailer;
	use PHPMailer\PHPMailer\Exception;
	// 加载邮件发送类
	require 'PHPMailer.php';
	require 'SMTP.php';
	/**
	* 用户类
	*/
	class User
	{
		private $servername = "localhost";
		private $username = "root";
		private $password = "123123";
		private $dbname = "api";
		private $token = '';
		protected $ip = '';
		protected $connect = null;
		
		function __construct($servername, $username, $password, $dbname)
		{
			$this->servername = $servername;
			$this->username = $username;
			$this->password = $password;
			$this->dbname = $dbname;
			$this->ip = $this->_ip();
			$this->_connect();
		}
		// 添加token
		public function addUser($email, $subject, $content, $limitTodayTime=0){
			// 生成token
			$this->_createToken($email);
			// 替换内容中token
			$content = str_ireplace('%token%', $this->token, $content);
			// 插入数据
			$stmt = $this->connect->prepare("INSERT INTO users(`token`, `ip`, `limit_today_time`, `email`)
		    		VALUES (:token, :ip, :limitTodayTime, :email)");
		   	$stmt->bindParam(':token', $this->token);
		   	$stmt->bindParam(':ip', $this->ip);
		   	$stmt->bindParam(':limitTodayTime', $limitTodayTime); // 设置每日调用上限，默认0无限制
		   	$stmt->bindParam(':email', $email);
		    // 使用 exec() ，没有结果返回 
		    $stmt->execute();

		    if($stmt->rowCount() > 0){ // 插入成功
		    	if($this->_sendEmail($email, $subject, $content)){ // 发送邮件且成功
		    		return json_encode(array('status' => 1, 'code' => 0, 'message' => '邮件发送成功'), JSON_UNESCAPED_UNICODE);
		    	}else{ // 发送邮件失败
		    		return json_encode(array('status' => 0, 'code' => 6, 'message' => '邮件发送失败'), JSON_UNESCAPED_UNICODE);
		    	}
		    }else{ // 插入失败
		    	return json_encode(array('status' => 0, 'code' => 1, 'message' => '程序异常'), JSON_UNESCAPED_UNICODE);
		    }
		}
		// 生成token
		private function _createToken($email){
			$this->token = hash('sha256', $email.$this->ip.time());
		}
		// 发邮件方法
		private function _sendEmail($emailTo, $subject, $content){
			$mail = new PHPMailer(true);                              // Passing `true` enables exceptions
			try {
			    // $mail->SMTPDebug = 2;                                 // Enable verbose debug output
			    $mail->isSMTP();                                      // Set mailer to use SMTP
			    $mail->CharSet='utf-8';
			    $mail->Host = 'smtp.qq.com';  // Specify main and backup SMTP servers
			    $mail->SMTPAuth = true;                               // Enable SMTP authentication
			    $mail->Username = 'admin@wwzc.cc';                 // SMTP username
			    $mail->Password = 'vseewawh';                           // SMTP password
			    $mail->SMTPSecure = 'tls';                            // Enable TLS encryption, `ssl` also accepted
			    $mail->Port = 587;                                    // TCP port to connect to

			    $mail->setFrom('admin@wwzc.cc');
			    $mail->addAddress('907553043@qq.com');     // Add a recipient
			    //Content
			    $mail->isHTML(true);                                  // Set email format to HTML
			    $mail->Subject = $subject;
			    $mail->Body    = $content;

			    $mail->send();
			    return true;
			} catch (Exception $e) {
				return false;
			    // echo 'Message could not be sent. Mailer Error: ', $mail->ErrorInfo;
			}
		}
		// 连接数据库
		private function _connect(){
			try{
				$this->connect = new PDO("mysql:host={$this->servername};dbname={$this->dbname}", $this->username, $this->password);
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
	}
?>