<?php
	/**
	 * 
	 */
	class Admin
	{
		private $servername = "localhost";
		private $username = "root";
		private $password = "123123";
		private $dbname = "api";
		protected $ip = '';
		protected $connect = null;
		private $nowDate = '';
		protected $token = '';
		function __construct($servername, $username, $password, $dbname)
		{
			$this->servername = $servername;
			$this->username = $username;
			$this->password = $password;
			$this->dbname = $dbname;
			$this->nowDate = date('Y-m-d H:i:s');
			$this->_connect();
		}
		// 添加文章
		public function add($table, $array){
			$strKey = '';
			$strVal = '';
			$i = 0;
			foreach ($array as $key => $value) {
				// $i = 0;
				if($i == 0){
					$strKey .= "`{$key}`";
					$strVal .= ":{$key}";
				}else{
					$strKey .= ", `{$key}`";
					$strVal .= ", :{$key}";
				}
				$i++;
			}
			try{
				$sql = "INSERT INTO {$table}({$strKey})
			    		VALUES ({$strVal})";
			    // 插入到数据库
			    $stmtInsert = $this->connect->prepare($sql);
			    foreach ($array as $key => $value) {
			    	$stmtInsert->bindValue(":{$key}", $value);
			    }
			    $stmtInsert->execute();
			    return json_encode(array('status' => 1, 'code' => 0, 'message' => '添加成功'), JSON_UNESCAPED_UNICODE);
			}catch(PDOException $e){ // 数据库操作出现异常
				// echo $e->getMessage();
			    return json_encode(array('status' => 0, 'code' => 1, 'message' => '程序异常'), JSON_UNESCAPED_UNICODE);
			}
		}
		// 获取所有文章
		public function get($table){
			try{
				$sql = "SELECT * FROM {$table}";
				// 查询
				$stmtQuery = $this->connect->prepare($sql);
				$stmtQuery->execute();
			    $arrResult = $stmtQuery->fetchAll(PDO::FETCH_ASSOC);
			    if($arrResult){
			    	return json_encode(array('status' => 1, 'code' => 0, 'value' => $arrResult), JSON_UNESCAPED_UNICODE);
			    }else{
			    	return json_encode(array('status' => 0, 'code' => 2, 'message' => '没有内容'), JSON_UNESCAPED_UNICODE);
			    }
			}catch(PDOException $e){
			    return json_encode(array('status' => 0, 'code' => 1, 'message' => '程序异常'), JSON_UNESCAPED_UNICODE);
			}
		}
		// 修改
		public function change($table, $array, $id){
			$str = '';
			$i = 0;
			foreach ($array as $key => $value) {
				// $i = 0;
				if($i == 0){
					$str .= "`{$key}`=:{$key}";
				}else{
					$str .= ", `{$key}`=:{$key}";
				}
				$i++;
			}
			try{
				$sql = "UPDATE {$table} SET {$str} WHERE `id`={$id}";
				$stmt = $this->connect->prepare($sql);
				foreach ($array as $key => $value) {
			    	$stmt->bindValue(":{$key}", $value);
			    }
			    // 使用 exec() ，没有结果返回 
			    $stmt->execute();
			    if($stmt->rowCount() > 0){
			    	return json_encode(array('status' => 1, 'code' => 0, 'message' => '修改成功'), JSON_UNESCAPED_UNICODE);
			    }else{
			    	return json_encode(array('status' => 0, 'code' => 2, 'message' => '修改失败'), JSON_UNESCAPED_UNICODE);
			    }
			}catch(PDOException $e){
			    return json_encode(array('status' => 0, 'code' => 1, 'message' => '程序异常'), JSON_UNESCAPED_UNICODE);
			}
		}

		public function del($table, $id){
			try{
				$sql = "DELETE FROM {$table} WHERE `id`={$id}";
				$stmt = $this->connect->prepare($sql);
			    // 使用 exec() ，没有结果返回 
			    $stmt->execute();
			    if($stmt->rowCount() > 0){
			    	return json_encode(array('status' => 1, 'code' => 0, 'message' => '删除成功'), JSON_UNESCAPED_UNICODE);
			    }else{
			    	return json_encode(array('status' => 0, 'code' => 2, 'message' => '删除失败'), JSON_UNESCAPED_UNICODE);
			    }
			}catch(PDOException $e){
			    return json_encode(array('status' => 0, 'code' => 1, 'message' => '程序异常'), JSON_UNESCAPED_UNICODE);
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
		// 析构
		function __destruct(){
			$this->connect = null;
		}
	}
?>