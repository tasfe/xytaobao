<?php
header("Content-type: text/html; charset=utf-8");

//config
include 'config.php';

//function
include D_ROOT.'func/global.fnc.php';

//db conn
include D_ROOT.'class/mysql.cls.php';
$db = new db_mysql();
$db->connect($dbhost, $dbuser, $dbpw, $dbname);


//language
$lang = 'zh';

//smarty
include D_ROOT.'source/Smarty/Smarty.class.php';
$tpl = new Smarty();
$tpldir = 'templates/'.$lang;
$tpl->template_dir = D_ROOT.'templates/default';
$tpl->tpl_dir = 'http://'.D_DOMAIN.D_DIR.'/templates/'.$lang;
$tpl->compile_dir = D_ROOT.'cache/templates_c/'.$lang;
$tpl->left_delimiter = '<!--{';
$tpl->right_delimiter = '}-->';
$tpl->assign('tpldir', $tpldir);

include_once D_ROOT.'language/'.$lang.'.php';
$tpl->assign('lang', $langs);


//url
$tpl->assign('s_url', 'http://'.D_DOMAIN.D_DIR);





//config
$conf = array();
$sql = "select * from config ";
$db->query($sql);
while($row = $db->fetch_array()){
	$conf[$row['skey']] = $row['sval'];
}
$tpl->assign('conf', $conf);

//所有分类
$sqlall = "select id,gname,istop,place from games where ( id in (". $conf['xygirl_cat_id'] .") or istop in (". $conf['xygirl_cat_id'] .")) and isshow=1 order by istop,place,id desc ";
$db->query($sqlall);
$all_cates = array();

while($row = $db->fetch_array()){
	if($row['istop'] == 0) {
		$all_cates[$row['id']] = $row;
	} else {
		$all_cates[$row['istop']]['sub'][] =  $row; 
	}
}

$tpl->assign('all_cates', $all_cates);




?>