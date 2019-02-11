<?php
	// 继承父类件
	require_once('../ApiCall.class.php');
	/**
	* 加密解密类
	*/
	class Article extends ApiCall
	{
		private $userId = 0;
		// 构造函数，无其他需要运行的代码时可以省略
		function __construct($servername, $username, $password, $dbname, $token)
		{
			// 执行父类构造函数
			parent::__construct($servername, $username, $password, $dbname, $token);
		}
		// 获取文章内容
		public function get($time){
			if($time > 0 && $time <= 2147483647){
				$date = date('Y-m-d', $time);
			}else{
				$date = date('Y-m-d');
			}
			try{
				// 查询
				$stmtQuery = $this->connect->prepare("SELECT * FROM article WHERE `time`=:date");
				$stmtQuery->bindParam(':date', $date);
				$stmtQuery->execute();
			    $arrResult = $stmtQuery->fetch(PDO::FETCH_ASSOC);
			    if($arrResult){
			    	$this->_view($arrResult['id']);
			    	return json_encode(array('status' => 1, 'code' => 0, 'value' => $arrResult), JSON_UNESCAPED_UNICODE);
			    }else{
			    	return json_encode(array('status' => 0, 'code' => 2, 'message' => '暂无内容'), JSON_UNESCAPED_UNICODE);
			    }
			}catch(PDOException $e){
			    return json_encode(array('status' => 0, 'code' => 1, 'message' => '程序异常'), JSON_UNESCAPED_UNICODE);
			}
		}
		// 获取评论内容
		public function getComment($id){
			try{
				// 查询
				$stmtQuery = $this->connect->prepare("SELECT a.id, a.content, a.admire, b.nickName, b.avatarUrl FROM article_comment a INNER JOIN article_user b ON a.user_id = b.id WHERE a.article_id=:id ORDER BY admire DESC, id DESC");
				$stmtQuery->bindParam(':id', $id);
				$stmtQuery->execute();
			    $arrResult = $stmtQuery->fetchAll(PDO::FETCH_ASSOC);
			    if($arrResult){
			    	return json_encode(array('status' => 1, 'code' => 0, 'value' => $arrResult), JSON_UNESCAPED_UNICODE);
			    }else{
			    	return json_encode(array('status' => 0, 'code' => 0, 'message' => '暂无评论'), JSON_UNESCAPED_UNICODE);
			    }
			}catch(PDOException $e){
			    return json_encode(array('status' => 0, 'code' => 1, 'message' => '程序异常'), JSON_UNESCAPED_UNICODE);
			}
		}
		// 添加评论
		public function addComment($id, $content){
			try{
			    // 插入到数据库
			    $stmtInsert = $this->connect->prepare("INSERT INTO article_comment(`user_id`, `article_id`, `content`)
			    		VALUES (:userId, :id, :content)");
			    $stmtInsert->bindParam(':id', $id);
				$stmtInsert->bindParam(':userId', $this->userId);
			    $stmtInsert->bindParam(':content', $content);
			    $stmtInsert->execute();
			    if($stmtInsert->rowCount() > 0){
			    		return json_encode(array('status' => 1, 'code' => 0, 'message' => '评论成功'), JSON_UNESCAPED_UNICODE);
				    }else{
				    	return json_encode(array('status' => 0, 'code' => 1, 'message' => '评论失败'), JSON_UNESCAPED_UNICODE);
				    }
			}catch(PDOException $e){ // 数据库操作出现异常
				// echo $e->getMessage();
			    return json_encode(array('status' => 0, 'code' => 1, 'message' => '程序异常'), JSON_UNESCAPED_UNICODE);
			}
		}

		public function admire($commentId){
			try{
				// 查询
				$stmtQuery = $this->connect->prepare("SELECT 'id' FROM article_comment_admire WHERE `comment_id`=:commentId AND `user_id`=:userId");
				$stmtQuery->bindParam(':commentId', $commentId);
				$stmtQuery->bindParam(':userId', $this->userId);
				$stmtQuery->execute();
			    $arrResult = $stmtQuery->fetch(PDO::FETCH_ASSOC);
			    if($arrResult){
					$stmtDel = $this->connect->prepare("DELETE FROM article_comment_admire WHERE `comment_id`=:commentId AND `user_id`=:userId");
					$stmtDel->bindParam(':commentId', $commentId);
					$stmtDel->bindParam(':userId', $this->userId);
					$stmtDel->execute();
					if($stmtDel->rowCount() > 0){
						$this->_admire($commentId, 0);
			    		return json_encode(array('status' => 1, 'code' => 0, 'message' => '取消成功'), JSON_UNESCAPED_UNICODE);
				    }else{
				    	return json_encode(array('status' => 0, 'code' => 1, 'message' => '取消失败'), JSON_UNESCAPED_UNICODE);
				    }
			    }else{
			    	$stmtUpdate = $this->connect->prepare("INSERT INTO article_comment_admire(`user_id`, `comment_id`)
			    		VALUES (:userId, :commentId)");
				   	// $stmt->bindParam(':time', $text);
				   	$stmtUpdate->bindParam(':commentId', $commentId);
				   	$stmtUpdate->bindParam(':userId', $this->userId);
				    // 使用 exec() ，没有结果返回 
				    $stmtUpdate->execute();
				    if($stmtUpdate->rowCount() > 0){
				    	$this->_admire($commentId, 1);
				    	return json_encode(array('status' => 1, 'code' => 1, 'message' => '操作成功'), JSON_UNESCAPED_UNICODE);
				    }else{
				    	return json_encode(array('status' => 0, 'code' => 1, 'message' => '操作失败'), JSON_UNESCAPED_UNICODE);
				    }
			    }
			}catch(PDOException $e){
			    return json_encode(array('status' => 0, 'code' => 1, 'message' => '程序异常'), JSON_UNESCAPED_UNICODE);
			}
		}
		// 喜欢操作
		public function like($id){
			try{
				// 查询
				$stmtQuery = $this->connect->prepare("SELECT 'id' FROM article_like WHERE `article_id`=:id AND `user_id`=:userId");
				$stmtQuery->bindParam(':id', $id);
				$stmtQuery->bindParam(':userId', $this->userId);
				$stmtQuery->execute();
			    $arrResult = $stmtQuery->fetch(PDO::FETCH_ASSOC);
			    if($arrResult){
					$stmtDel = $this->connect->prepare("DELETE FROM article_like WHERE `article_id`=:id AND `user_id`=:userId");
					$stmtDel->bindParam(':id', $id);
					$stmtDel->bindParam(':userId', $this->userId);
					$stmtDel->execute();
					if($stmtDel->rowCount() > 0){
						$this->_like($id, 0);
			    		return json_encode(array('status' => 1, 'code' => 0, 'message' => '取消成功'), JSON_UNESCAPED_UNICODE);
				    }else{
				    	return json_encode(array('status' => 0, 'code' => 1, 'message' => '取消失败'), JSON_UNESCAPED_UNICODE);
				    }
			    }else{
			    	$stmtInsert = $this->connect->prepare("INSERT INTO article_like(`user_id`, `article_id`)
			    		VALUES (:userId, :id)");
				   	// $stmt->bindParam(':time', $text);
				   	$stmtInsert->bindParam(':id', $id);
				   	$stmtInsert->bindParam(':userId', $this->userId);
				    // 使用 exec() ，没有结果返回 
				    $stmtInsert->execute();
				    if($stmtInsert->rowCount() > 0){
				    	$this->_like($id, 1);
				    	return json_encode(array('status' => 1, 'code' => 1, 'message' => '操作成功'), JSON_UNESCAPED_UNICODE);
				    }else{
				    	return json_encode(array('status' => 0, 'code' => 1, 'message' => '操作失败'), JSON_UNESCAPED_UNICODE);
				    }
			    }
			}catch(PDOException $e){
			    return json_encode(array('status' => 0, 'code' => 1, 'message' => '程序异常'), JSON_UNESCAPED_UNICODE);
			}
		}
		// 增加浏览量
		private function _view($id){
			try{
				$stmt = $this->connect->prepare("UPDATE article SET `view`=`view`+1 WHERE `id`=:id");
			   	// $stmt->bindParam(':time', $text);
			   	$stmt->bindParam(':id', $id);
			    // 使用 exec() ，没有结果返回 
			    $stmt->execute();
			    if($stmt->rowCount() > 0){
			    	return true;
			    }else{
			    	return false;
			    }
			}catch(PDOException $e){
			    return false;
			}
		}
		// 增加喜欢量
		private function _like($id, $action=1){
			if($action == 1){
				$sql = "UPDATE article SET `like`=`like`+1 WHERE `id`=:id";
			}else{
				$sql = "UPDATE article SET `like`=`like`-1 WHERE `id`=:id";
			}
			try{
				$stmt = $this->connect->prepare($sql);
			   	// $stmt->bindParam(':time', $text);
			   	$stmt->bindParam(':id', $id);
			    // 使用 exec() ，没有结果返回 
			    $stmt->execute();
			    if($stmt->rowCount() > 0){
			    	return true;
			    }else{
			    	return false;
			    }
			}catch(PDOException $e){
			    return false;
			}
		}
		// 增加赞量
		private function _admire($id, $action=1){
			if($action == 1){
				$sql = "UPDATE article_comment SET `admire`=`admire`+1 WHERE `id`=:id";
			}else{
				$sql = "UPDATE article_comment SET `admire`=`admire`-1 WHERE `id`=:id";
			}
			try{
				$stmt = $this->connect->prepare($sql);
			   	// $stmt->bindParam(':time', $text);
			   	$stmt->bindParam(':id', $id);
			    // 使用 exec() ，没有结果返回 
			    $stmt->execute();
			    if($stmt->rowCount() > 0){
			    	return true;
			    }else{
			    	return false;
			    }
			}catch(PDOException $e){
			    return false;
			}
		}
		
		public function getUserId($array){
			try{
				// 查询
				$stmtQuery = $this->connect->prepare("SELECT `id` FROM article_user WHERE `openId`=:openId");
				$stmtQuery->bindParam(':openId', $array['openId']);
				$stmtQuery->execute();
			    $arrResult = $stmtQuery->fetch(PDO::FETCH_ASSOC);
			    if($arrResult){
			    	$this->userId = $arrResult['id'];
			    	return true;
			    }
			    $strKey = '';
				$strVal = '';
				$i = 0;
				foreach ($array as $key => $value) {
					// $i = 0;
					if($key == 'watermark'){
						continue;
					}
					if($i == 0){
						$strKey .= "`{$key}`";
						$strVal .= ":{$key}";
					}else{
						$strKey .= ", `{$key}`";
						$strVal .= ", :{$key}";
					}
					$i++;
				}
				$sql = "INSERT INTO article_user({$strKey})
			    		VALUES ({$strVal})";
			    // 插入到数据库
			    $stmtInsert = $this->connect->prepare($sql);
			    foreach ($array as $key => $value) {
			    	if($key == 'watermark'){
						continue;
					}
			    	$stmtInsert->bindValue(":{$key}", $value);
			    }
			    $stmtInsert->execute();
			    if($stmtInsert->rowCount() > 0){
			    	$this->userId = $this->connect->lastInsertId();
			    	return true;
			    }else{
			    	return false;
			    }
			}catch(PDOException $e){
			    return false;
			}
		}
	    /**
	     * 对密文进行解密
	     * @param string $aesCipher 需要解密的密文
	     * @param string $aesIV 解密的初始向量
	     * @return string 解密得到的明文
	     */
	    public function decrypt( $aesCipher, $aesIV )
	    {

	        try {
	            
	            // $module = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_CBC, '');
	            
	            // mcrypt_generic_init($module, $this->key, $aesIV);

	            // //解密
	            // $decrypted = mdecrypt_generic($module, $aesCipher);
	            // mcrypt_generic_deinit($module);
	            // mcrypt_module_close($module);
	            $decrypted = openssl_decrypt($aesCipher,'AES-128-CBC',$this->key,OPENSSL_ZERO_PADDING,$aesIV);
	            // var_dump($decrypted);
	        } catch (Exception $e) {
	            return false;
	        }


	        try {
	            //去除补位字符
	            $pkc_encoder = new PKCS7Encoder;
	            $result = $pkc_encoder->decode($decrypted);

	        } catch (Exception $e) {
	            //print $e;
	            return false;
	        }
	        return $result;
	    }
	}
?>