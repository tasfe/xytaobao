<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


function smarty_modifier_money($mount,  $d='$', $length = 3)
{
	/*$numfix = '';
	if(strpos($mount,'.')){
		$numfix = substr($mount,strpos($mount,'.'));
		$strlen = strpos($mount,'.');
	} else {
		$strlen = strlen($mount);
	}*/
	$strlen = strlen($mount);
	if($strlen >3){
    	$str=number_format($mount, $length, "", ",");
		$str = substr($str, 0, (strlen($str)-$length));
	} else {
		if($mount == 0) return $mount.'';
		return $d.$mount;
	}


	return $d.$str;//.$numfix;
}

/* vim: set expandtab: */

?>
