<?php

/* 跳转淘宝商品页面*/

include 'base.php';

if(!empty($_GET['id']) && intval($_GET['id'])){
	$item_id = intval($_GET['id']);	
} else {
	dheader('http://'.D_DOMAIN);
}
		
$doc = $db->do_one("equipment","click_url","id=".$item_id);

if(!empty($doc['click_url'])){
	dheader($doc['click_url']);
} else {
	dheader('http://'.D_DOMAIN);
}

?>