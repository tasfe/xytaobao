<?php


	include 'base.php';

	if(isset($_GET['act']) && $_GET['act'] == 'login'){
		//print_r($_POST);
		$_user = daddslashes($_POST['username']);
		$_ps = daddslashes($_POST['password']);
		$doc = $db->do_one('admin', 'priv, password', 'username="'.$_user.'" and status = 1');
	
		if(!empty($doc['password'])){
			if(validate_password($_ps, $doc['password'])){

				$_SESSION['s_priv'] = $doc['priv'];
				$_SESSION['s_user'] = $_user;

				$doc = array();
				$doc['logintime'] = $now;
				$doc['loginip'] = getip();
				
				$db->do_update('admin', $doc, 'username="'.$_user.'"');
				$logs = array('user'=>$_SESSION['s_user'],'addtime'=>date("Y-m-d H:i:s"),'logs'=>'管理员登陆,'.$doc['loginip'],'act'=>'index');
				$db->do_insert("system_log", $logs);

				dheader('admin.php');
			} else {
				//echo "password error!";
			}
		}
		dheader('index.php');

	} else {
		$tpl->show('index');
	}


?>