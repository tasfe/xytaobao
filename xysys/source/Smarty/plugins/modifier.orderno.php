<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


function smarty_modifier_orderno($id, $length = 7, $letter)
{
    if ($length == 0)
        return '';
    $strlen = strlen($id);
    $l = $length - $strlen;
    if($l < 0) return '';
    
    $ad_strA = array('', 'A', 'B', "C", "D", "E", "F", "G");
    
    if(intval($letter) > 0){
    	$letter = $ad_strA[$letter];
    }
    
    if($l){
    	return $letter.str_repeat('0',$l).$id;
    } else {
    	return $letter.$id;
    }
}

/* vim: set expandtab: */

?>
