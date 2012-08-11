<?php

include 'base.php';

$tpl->assign('admin_file', 'admin.php');

if (empty ( $_SESSION['s_user'] )) {
	dheader ( 'index.php' );
}

if (empty ( $_GET['adm'] )) {
	$tpl->show('admin');
} else {
	$adm = dhtmlspecialchars($_GET['adm']);
	$admA = array('cpanel', 'category', 'articles', 'systems', 'members', 'games', 'orders', 'email', 'tbapi');
	if(in_array($adm, $admA)){
		$incscript =  'inc/'.$adm.'.php';
		if(file_exists($incscript)){
			include $incscript;
			$opt = isset($_GET['opt'])?dhtmlspecialchars($_GET['opt']):'view';
			$app = new $adm();
			if(method_exists($app, $opt)) $app->$opt();
			else echo "Call to undefined method $opt()";
		}
	}
}


class base {
	var $tpl = '';
	var $db = '';
	var $_now = '';
	function base() {
		global $db,$tpl,$conf,$tbc;
		$this->tpl = $tpl;
		$this->db = $db;
		$this->tbc = $tbc;
		$this->conf = $conf;
		
		$this->_now = time();
		$this->_date = date("Y-m-d H:i:s");
		
	}

	function showmsg($msg, $jumpurl='', $t=2)
	{
		include D_ROOT.'language/admin_msg.php';
		$ifjump = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />';
		if($jumpurl){
			$ifjump .= "<META HTTP-EQUIV='Refresh' CONTENT='$t; URL=$jumpurl'>";
		}

		$ifjump .= '<link href="images/admin.css" rel="stylesheet" type="text/css" />';
		$lang[$msg] && $msg=$lang[$msg];
		$outmsg="<div style='font-size:12px;font-family:verdana;line-height:180%;color:#666;border:dashed 1px #ccc;padding:1px;margin:20px;'>";
		$outmsg.="<div style=\"background: #eeedea;padding-left:10px;font-weight:bold;height:25px;\">{$lang['prompt']}</div>";
		$outmsg.="<div style='padding:20px;font-size:14px;'><img src='images/ok.gif' align='absmiddle' hspace=20 ><span>$msg</span></div>";
		if($jumpurl){
			$outmsg.="<div style=\"text-align:center;height:30px;\">$ifjump<a href=\"$jumpurl\">{$lang['back']}</a></div>";
		}
		$outmsg.="</div>";

		echo $outmsg;exit();
	}

	function syslogs($text,$act=''){
		$logs = array('user'=>$_SESSION['s_user'],'addtime'=>$this->_date,'logs'=>$text,'act'=>$act);
		$this->db->do_insert("system_log", $logs);
	}
	
	

}

?>