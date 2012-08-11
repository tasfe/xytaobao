<?php

include 'base.php';


$tpl->assign('isindex', 1);

//首页商品展示

$items = array();
foreach($all_cates as $val){
	$items[$val['id']] = $db->do_all_bysql("select id,ename,image,price,click_url,item_state,item_city from equipment where gid=".$val['id']." order by id desc limit 8");
}


$tpl->assign('items', $items);

$tpl->show("index");

?>