<?php
	// 配置文件
	require_once('../config.php');
	// 加密类文件
	require_once('Admin.class.php');

	$admin = new Admin($servername, $username, $password, $dbname);
	$array = array();
	if(@$_POST['do'] == 'add'){
		// $array = array('' => , );
		foreach ($_POST as $key => $value) {
			if($key == 'do'){
				continue;
			}
			$array[$key] = $value;
		}
		echo $admin->add('article', $array);
	}elseif (@$_POST['do'] == 'get') {
		echo $admin->get('article');
	}elseif (@$_POST['do'] == 'change') {
		foreach ($_POST as $key => $value) {
			if($key == 'do'){
				continue;
			}
			if($key == 'id'){
				$id = $value;
				continue;
			}
			$array[$key] = $value;
		}
		echo $admin->change('article', $array, $id);
	}elseif (@$_POST['do'] == 'del') {
		$id = @$_POST['id'];
		echo $admin->del('article', $id);
	}
?>