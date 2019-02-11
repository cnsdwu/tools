<?php
	// 继承父类件
	require_once('../ApiCall.class.php');
	/**
	* 加密解密类
	*/
	class EnText extends ApiCall
	{
		private $text = ''; // 原文本
		private $action = 'en'; // 动作
		private $entext = ''; // 加密后文本
		// 构造函数，无其他需要运行的代码时可以省略
		function __construct($servername, $username, $password, $dbname, $token)
		{
			// 执行父类构造函数
			parent::__construct($servername, $username, $password, $dbname, $token);
		}
		// 文本加密
		public function enText($text){
			// 执行加密函数
			$this->entext = $this->_encode($text);
			// 实体化特殊字符
			$text = htmlentities($text);
			try{
				// 查询是否以被加密
				$stmtQuery = $this->connect->prepare("SELECT `entext` FROM partyidea WHERE `text`=:text");
				$stmtQuery->bindParam(':text', $text);
				$stmtQuery->execute();
			    $arrResult = $stmtQuery->fetch();
			    // 加密过直接返回
			    if($arrResult){
			    	return json_encode(array('status' => 1, 'code' => 0, 'value' => $arrResult[0]), JSON_UNESCAPED_UNICODE);
			    }
			    // 判断调用总次数
			    if(!$this->_limitTodayTime()){
			    	return json_encode(array('status' => 0, 'code' => 5, 'value' => '调用次数已达上限'), JSON_UNESCAPED_UNICODE);
			    }
			    // 插入到数据库
			    $stmtInsert = $this->connect->prepare("INSERT INTO partyidea(`text`, `entext`, `ip`)
			    		VALUES (:text, :entext, :ip)");
			   	$stmtInsert->bindParam(':text', $text);
			   	$stmtInsert->bindParam(':entext', $this->entext);
			   	$stmtInsert->bindParam(':ip', $this->ip);
			    $stmtInsert->execute();
			    // 增加次数
			    $this->_addTime();
			    return json_encode(array('status' => 1, 'code' => 0, 'value' => $this->entext), JSON_UNESCAPED_UNICODE);
			}catch(PDOException $e){ // 数据库操作出现异常
				// echo $e->getMessage();
			    return json_encode(array('status' => 0, 'code' => 1, 'message' => '程序异常'), JSON_UNESCAPED_UNICODE);
			}
			
		}
		// 密文解密
		public function deText($text){
			try{
				// 查询
				$stmtQuery = $this->connect->prepare("SELECT `text` FROM partyidea WHERE `entext`=:text");
				$stmtQuery->bindParam(':text', $text);
				$stmtQuery->execute();
			    $arrResult = $stmtQuery->fetch();
			    if($arrResult){
			    	return json_encode(array('status' => 1, 'code' => 0, 'value' => html_entity_decode($arrResult[0])), JSON_UNESCAPED_UNICODE);
			    }else{
			    	return json_encode(array('status' => 0, 'code' => 2, 'message' => '无法解密'), JSON_UNESCAPED_UNICODE);
			    }
			}catch(PDOException $e){
			    return json_encode(array('status' => 0, 'code' => 1, 'message' => '程序异常'), JSON_UNESCAPED_UNICODE);
			}
			
		}
		// 加密算法
		private function _encode($str=''){
			$arr24 = ['富强', '民主', '文明', '和谐', '自由', '平等', '公正', '法治', '爱国', '敬业', '诚信', '友善'];
			$hash = hash('sha256', $str . '小呆先生'); // 加入常量混淆，结果可被预测
			// $hash = hash('sha256', $str . time()); // 加入动态混淆，结果不可被预测
			// 算法
			for ($i=0; $i < count($arr24); $i++) {
				$hash = str_replace($i, $arr24[$i], $hash);
				if($i == 9){
					$hash = str_replace('a', '10', $hash);
				}
				if($i == 10){
					$hash = str_replace('b', '11', $hash);
				}
			}
			return preg_replace('/\w/', '',$hash);
		}
	}
?>