<?php

	
	include '../config.php';
	include D_ROOT.'func/global.fnc.php';


	include D_ROOT.'source/Smarty/Smarty.class.php';
	$tpl = new Smarty();
	$tpl->template_dir = D_ROOT.'templates/admin';
	$tpl->tpl_dir = '.';
	$tpl->compile_dir = D_ROOT.'cache/templates_c/admin';
	$tpl->left_delimiter = '<!--{';
	$tpl->right_delimiter = '}-->';

	include_once D_ROOT.'language/admin.php';
	$tpl->assign('lang', $langs);

	include D_ROOT.'class/mysql.cls.php';
	$db = new db_mysql();
	$db->connect($dbhost, $dbuser, $dbpw, $dbname);
	
	
	$sql = "select * from config ";
	$db->query($sql);
	while($row = $db->fetch_array()){
		$conf[$row['skey']] = $row['sval'];
	}

	
	include D_ROOT.'source/taobaosdk/TopSdk.php';
	
	$tbc = new TopClient;
	$tbc->appkey = $conf['appkey_id'];
	$tbc->secretKey = $conf['secret_id'];
	

?>