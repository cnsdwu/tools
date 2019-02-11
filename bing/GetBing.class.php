<?php
	// 继承父类件
	include_once('../ApiCall.class.php');
	/**
	* 获取必应每日图片类
	*/
	class GetBing extends ApiCall
	{
		private $url = ''; // 图片地址
		// 构造函数，无其他需要运行的代码时可以省略
		function __construct($servername, $username, $password, $dbname, $token)
		{
			// 执行父类构造函数
			parent::__construct($servername, $username, $password, $dbname, $token);
		}
		// 直接显示图片方式
		public function getImg(){
			// 得到原图地址
			if(!$this->_getUrl()){
				return json_encode(array('status' => 0, 'code' => 1, 'message' => '程序异常'), JSON_UNESCAPED_UNICODE);
			}
			try{
				// 读取到本地并返回
				return file_get_contents($this->url);
			}catch(Exception $e){
				return false;
			}
		}
		// base64数据
		public function getImgBase64(){
			// 得到原图地址
			if(!$this->_getUrl()){
				return json_encode(array('status' => 0, 'code' => 1, 'message' => '程序异常'), JSON_UNESCAPED_UNICODE);
			}
			try{
				// 读取到本地并进行base64编码
				$base64 = "data:image/jpeg;base64," . base64_encode(file_get_contents($this->url));
				return json_encode(array('status' => 1, 'code' => 0, 'message' => '成功', 'value' => $base64), JSON_UNESCAPED_UNICODE);
			}catch(Exception $e){
				return json_encode(array('status' => 0, 'code' => 1, 'message' => '程序异常'), JSON_UNESCAPED_UNICODE);
			}
		}
		// 图片url
		public function getUrl(){
			// 得到原图地址
			if(!$this->_getUrl()){
				return json_encode(array('status' => 0, 'code' => 1, 'message' => '程序异常'), JSON_UNESCAPED_UNICODE);
			}
			return json_encode(array('status' => 1, 'code' => 0, 'message' => '成功', 'value' => $this->url), JSON_UNESCAPED_UNICODE);
		}
		// 获取原图地址
		private function _getUrl(){
			try{
				$array = json_decode(file_get_contents('http://www.bing.com/HPImageArchive.aspx?format=js&idx=0&n=1&mkt=en-US'));
				$imgurl = $array->{"images"}[0]->{"url"};
				$this->url = 'http://cn.bing.com'.$imgurl; // 国内
				return true;
			}catch(Exception $e){
				return false;
			}
		}
	}   
?>