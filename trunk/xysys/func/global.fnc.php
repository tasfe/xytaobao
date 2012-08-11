<?php

function dsetcookie($name,$value,$expire=604800) {
	if($expire==0){
		setcookie($name, $value);
	} elseif($expire < 0) {
		setcookie($name, $value, time() -1);
	} else {
		setcookie($name, $value,time()+$expire,'/');
	}
}

function send_mail($subject, $body, $email, $username=''){
	include_once D_ROOT.'source/mailer/class.phpmailer.php';
	$mail = new PHPMailer ( );
	$mail->From = "info@avatarseeds.com";
	$mail->FromName = "avatarseeds.com";
	$mail->Subject = $subject;
	$mail->MsgHTML ( $body );
	$mail->AddAddress ( $email, $username );
	return $mail->Send ();
}

function getorderno($id, $length = 7,$letter=0){
	if ($length == 0)
        return '';
    $strlen = strlen($id);
    $l = $length - $strlen;
    if($l < 0) return '';

    $ad_strA = array('H', 'A', 'B', "C", "D", "E", "F", "G");

	$letter = intval($letter);
    $letter = $ad_strA[$letter];


    if($l){
    	return $letter.str_repeat('0',$l).$id;
    } else {
    	return $letter.$id;
    }
}

function Char_cv($msg){
	$msg = str_replace('&amp;','&',$msg);
	$msg = str_replace('&nbsp;',' ',$msg);
	$msg = str_replace('"','&quot;',$msg);
	$msg = str_replace("'",'&#039;',$msg);
	$msg = str_replace("<","&lt;",$msg);
	$msg = str_replace(">","&gt;",$msg);
	$msg = str_replace("\t","   &nbsp;  &nbsp;",$msg);
	$msg = str_replace("\r","",$msg);
	$msg = str_replace("   "," &nbsp; ",$msg);
	return $msg;
}


function getdirname($path){
	if(strpos($path,'\\')!==false){
		return substr($path,0,strrpos($path,'\\'));
	}elseif(strpos($path,'/')!==false){
		return substr($path,0,strrpos($path,'/'));
	}else{
		return '/';
	}
}

function dheader($url){
	global $gzip;
	if($gzip){
		header("Location: $url");exit;
	}else{
		ob_start();
		echo "<script language='javascript'>\n";
		echo "window.location='$url';\n";
		echo "</script>";
		exit;
	}
}

function getip()
{
	if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
		$onlineip = $_SERVER['HTTP_X_FORWARDED_FOR'];
	}elseif(isset($_SERVER['HTTP_CLIENT_IP'])){
		$onlineip = $_SERVER['HTTP_CLIENT_IP'];
	}else{
		$onlineip = $_SERVER['REMOTE_ADDR'];
	}
	return preg_match("/^[\d]([\d\.]){5,13}[\d]$/", $onlineip) ? $onlineip : 'unknown';
}



function numofpage($count,&$page,$numofpage,$url,$max=0){
	$total=$numofpage;
	$max && $numofpage > $max && $numofpage=$max;
	if($numofpage <= 1 || !is_numeric($page)){
		return '';
	}else{
		if($page<1){
			$page = 1;
		}elseif($page>$numofpage){
			$page = $numofpage;
		}
		$pages="<div class=\"pnum\"><a href=\"{$url}page=1\" style=\"font-weight:bold\">&laquo;</a>";
		$flag=0;
		for($i=$page-3;$i<=$page-1;$i++){
			if($i<1) continue;
			$pages.="<a href=\"{$url}page=$i\">$i</a>";
		}
		$pages.="<span>$page</span>";
		if($page<$numofpage){
			for($i=$page+1;$i<=$numofpage;$i++){
				$pages.="<a href=\"{$url}page=$i\">$i</a>";
				$flag++;
				if($flag==4) break;
			}
		}
		$pages.="&nbsp;&nbsp;<input type=\"text\" size=\"6\" onkeydown=\"javascript: if(event.keyCode==13){ location='{$url}page='+this.value;return false;}\"><a href=\"{$url}page=$numofpage\" style=\"font-weight:bold\">&raquo;</a> Total: $count , Pages: ( $page/$total ) </div>";
		return $pages;
	}
}

function numofpage2($count,&$page,$numofpage,$url,$max=0){
	$total=$numofpage;
	$max && $numofpage > $max && $numofpage=$max;
	if($numofpage <= 1 || !is_numeric($page)){
		return '';
	}else{
		if($page<1){
			$page = 1;
		}elseif($page>$numofpage){
			$page = $numofpage;
		}
		$pages = "<table align='right'><tr>";
		if($page > 1) 
			$pages .= '<td class="style25"><a href="'.$url.($page-1).'"><img src="images/pre.gif" width="20" height="20" hspace="5" vspace="5" align="absmiddle" /></a></td>';
		$flag=0;
		for($i=$page-3;$i<=$page-1;$i++){
			if($i<1) continue;
			$pages .= '<td class="number0"><a href="'.$url.$i.'">'.$i.'</a></td>';
		}
		$pages .= '<td class="number1">'.$page.'</td>';
		if($page<$numofpage){
			for($i=$page+1;$i<=$numofpage;$i++){
				$pages .= '<td class="number0"><a href="'.$url.$i.'">'.$i.'</a></td>';
				$flag++;
				if($flag==4) break;
			}
		}
		
		if($page < $numofpage) $pages .= '<td class="style25"><a href="'.$url.($page+1).'"><img src="images/next.gif" width="20" height="20" hspace="5" vspace="5" align="absmiddle"/></a></td>';
		//$pages.="&nbsp;&nbsp;<input type=\"text\" size=\"6\" onkeydown=\"javascript: if(event.keyCode==13){ location='{$url}page='+this.value;return false;}\"><a href=\"{$url}$numofpage\" style=\"font-weight:bold\">&raquo;</a> Total: $count , Pages: ( $page/$total ) ";
		$pages .= "</tr></table>";
		return $pages;
	}
}

function daddslashes($string, $force = 0) {
	!defined('MAGIC_QUOTES_GPC') && define('MAGIC_QUOTES_GPC', get_magic_quotes_gpc());
	if(!MAGIC_QUOTES_GPC || $force) {
		if(is_array($string)) {
			foreach($string as $key => $val) {
				$string[$key] = daddslashes($val, $force);
			}
		} else {
			$string = addslashes($string);
		}
	}
	return trim($string);
}

function cutstr($string, $length, $dot = ' ...') {
	global $charset;

	if(strlen($string) <= $length) {
		return $string;
	}

	$string = str_replace(array('&amp;', '&quot;', '&lt;', '&gt;'), array('&', '"', '<', '>'), $string);

	$strcut = '';
	if(strtolower($charset) == 'utf-8') {

		$n = $tn = $noc = 0;
		while($n < strlen($string)) {

			$t = ord($string[$n]);
			if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
				$tn = 1; $n++; $noc++;
			} elseif(194 <= $t && $t <= 223) {
				$tn = 2; $n += 2; $noc += 2;
			} elseif(224 <= $t && $t < 239) {
				$tn = 3; $n += 3; $noc += 2;
			} elseif(240 <= $t && $t <= 247) {
				$tn = 4; $n += 4; $noc += 2;
			} elseif(248 <= $t && $t <= 251) {
				$tn = 5; $n += 5; $noc += 2;
			} elseif($t == 252 || $t == 253) {
				$tn = 6; $n += 6; $noc += 2;
			} else {
				$n++;
			}

			if($noc >= $length) {
				break;
			}

		}
		if($noc > $length) {
			$n -= $tn;
		}

		$strcut = substr($string, 0, $n);

	} else {
		for($i = 0; $i < $length; $i++) {
			$strcut .= ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
		}
	}

	$strcut = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $strcut);

	return $strcut.$dot;
}

function dhtmlspecialchars($string) {
	if(is_array($string)) {
		foreach($string as $key => $val) {
			$string[$key] = dhtmlspecialchars($val);
		}
	} else {
		$string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4})|[a-zA-Z][a-z0-9]{2,5});)/', '&\\1',
		str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string));
	}
	return $string;
}

function isemail($email) {
	return strlen($email) > 6 && preg_match("/^[\w\-\.]+@[\w\-\.]+(\.\w+)+$/", $email);
}

function validation($value,$checkName)
{
	$regex = array(
	'require'=> '/.+/',
	'email' => '/^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$/',
	'phone' => '/^((\(\d{2,3}\))|(\d{3}\-))?(\(0\d{2,3}\)|0\d{2,3}-)?[1-9]\d{6,7}(\-\d{1,4})?$/',
	'mobile' => '/^((\(\d{2,3}\))|(\d{3}\-))?13\d{9}$/',
	'url' => '/^http:\/\/[A-Za-z0-9]+\.[A-Za-z0-9]+[\/=\?%\-&_~`@[\]\':+!]*([^<>\"\"])*$/',
	'currency' => '/^\d+(\.\d+)?$/',
	'number' => '/\d+$/',
	'zip' => '/^[1-9]\d{5}$/',
	'qq' => '/^[1-9]\d{4,12}$/',
	'integer' => '/^[-\+]?\d+$/',
	'double' => '/^[-\+]?\d+(\.\d+)?$/',
	'english' => '/^[A-Za-z]+$/',
	);

	if(isset($regex[strtolower($checkName)])) {
		$matchRegex = $regex[strtolower($checkName)];
	} else {
		return false;
	}
	return preg_match($matchRegex,trim($value));
}


function validate_password($plain, $encrypted) {
	if (not_null($plain) && not_null($encrypted)) {
		$stack = explode(':', $encrypted);

		if (sizeof($stack) != 2) return false;

		if (md5($stack[1] . $plain) == $stack[0]) {
			return true;
		}
	}

	return false;
}

function encrypt_password($plain) {
	$password = '';

	for ($i=0; $i<10; $i++) {
		$password .= random();
	}

	$salt = substr(md5($password), 0, 2);

	$password = md5($salt . $plain) . ':' . $salt;

	return $password;
}

function random($min = null, $max = null) {
	static $seeded;

	if (!isset($seeded)) {
		mt_srand((double)microtime()*1000000);
		$seeded = true;
	}

	if (isset($min) && isset($max)) {
		if ($min >= $max) {
			return $min;
		} else {
			return mt_rand($min, $max);
		}
	} else {
		return mt_rand();
	}
}

function not_null($value) {
	if (is_array($value)) {
		if (sizeof($value) > 0) {
			return true;
		} else {
			return false;
		}
	} else {
		if (($value != '') && (strtolower($value) != 'null') && (strlen(trim($value)) > 0)) {
			return true;
		} else {
			return false;
		}
	}
}

function gourlreferer()
{
	if(empty($_SESSION['gourl'])){
		$url = $_SERVER['HTTP_REFERER'];
		$scriptname = basename($url);
		$scriptnameA = explode('?', $scriptname);
		if(in_array($scriptnameA[0], array('account.php', 'members.php', 'flow.php'))){
			$url = 'index.php';
		}
	} else {
		$url = $_SESSION['gourl'];
	}
	unset($_SESSION['gourl']);
	//echo $url;
	header('Location:'.$url);
}

function upload_adimg($filename,$tmpfile,$i=1){
	$forbidden_ext = array('php','php3','asp','aspx','asa','jsp','cgi','exe','pl','htm','html');
	$file_ext = strtolower(substr(strrchr($filename,"."),1));//文件后缀
	if(in_array($file_ext,$forbidden_ext)){
		echo '415';
		die();
	}
	$url = D_P_A.date('Y');
	$savedir = D_P.$url;

	if(!is_dir($savedir)) {mkdir($savedir);}

	$randvar	= substr(md5(time()+$i),2,16);
	$upfilename = $randvar.'.'.$file_ext;
	$target = $savedir.'/'.$upfilename;
	$target_url = $url.'/'.$upfilename;

	if(!postupload($tmpfile,$target)){
		echo '500';
		die();
	}

	$img = $target_url;
	return $img;
}

function upload_one($filename,$tmpfile,$i=1){
	$forbidden_ext = array('php','php3','asp','aspx','asa','jsp','cgi','exe','pl','htm','html');
	$file_ext = strtolower(substr(strrchr($filename,"."),1));//文件后缀
	if(in_array($file_ext,$forbidden_ext)){
		echo '415';
		die();
	}
	$url = D_ROOT_UPLOAD.date('Y-m');
	$savedir = D_ROOT.$url;
	if(!is_dir($savedir)){mkdir($savedir);}
	$randvar	= substr(md5(time()+$i),2,16);
	$upfilename = $randvar.'.'.$file_ext;
	$target		= $savedir.'/'.$upfilename;
	$target_url		= $url.'/'.$upfilename;
	if(!postupload($tmpfile,$target)){
		echo '500';
		die();
	}

	include_once D_ROOT.'class/image.cls.php';
	$gimg = new image();
	$gimg->run($target,60,60,'s', true);
	$gimg->run($target,115,115,'l', true);
	$gimg->run($target,225,225,'b', true);
	$img = $target_url;

	return $img;
}

function upload_multi()
{
	$imgs = array();
	if(isset($_FILES['upfile']) && is_array($_FILES['upfile']['name'])){
		foreach ($_FILES['upfile']['name'] as $key => $val){
			if($val){
				$size = $_FILES['upfile']['size'][$key];
				$imgs[] = upload_one($val,$_FILES['upfile']['tmp_name'][$key],$key);
			}
		}
	}
	krsort($imgs);
	return $imgs;
}

function postupload($tmp_name,$filename){
	if(strpos($filename,'..')!==false || eregi("\.php$",$filename)){
		exit('illegal file type!');
	}
	if(function_exists("move_uploaded_file") && @move_uploaded_file($tmp_name,$filename)){
		@chmod($filename,0777);
		return true;
	}elseif(@copy($tmp_name, $filename)){
		@chmod($filename,0777);
		return true;
	}
	return false;
}


function random2($length, $numeric = 0) {
	if($numeric) {
		$hash = sprintf('%0'.$length.'d', mt_rand(0, pow(10, $length) - 1));
	} else {
		$hash = '';
		$chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789abcdefghijklmnopqrstuvwxyz';
		$max = strlen($chars) - 1;
		for($i = 0; $i < $length; $i++) {
			$hash .= $chars[mt_rand(0, $max)];
		}
	}
	return $hash;
}

function removedir($dirname, $keepdir = FALSE) {

	$dirname = wipespecial($dirname);

	if(!is_dir($dirname)) {
		return FALSE;
	}
	$handle = opendir($dirname);
	while(($file = readdir($handle)) !== FALSE) {
		if($file != '.' && $file != '..') {
			$dir = $dirname . DIRECTORY_SEPARATOR . $file;
			is_dir($dir) ? removedir($dir) : unlink($dir);
		}
	}
	closedir($handle);
	return !$keepdir ? (@rmdir($dirname) ? TRUE : FALSE) : TRUE;
}



function dexit($message = '') {
	echo $message;
	exit();
}


function pre($arr){
	echo '<pre>';
	print_r($arr);
	exit();
}

function writelog($log) {
	$logdir = D_ROOT.'log/';
	$logfile = $logdir.date('Y-m-d').'.php';

	if($fp = @fopen($logfile, 'a+')) {
		@flock($fp, 2);
		fwrite($fp, date("H:i:s-",time()).$log."\n");
		fclose($fp);
	}
}



/*格式化生成定单号*/
function formatbsn($bsn,$len=10,$type=1)
{
	$slen = strlen($bsn);
	if($slen < $len){
		$bsn = str_repeat('0',$len-$slen).$bsn;
	}
	$str = '';
	$design = array(9,3,2,7,0,4,6,1,5,8);
	foreach ($design as $k) {
		$str .= $bsn[$k];
	}
	return $type.$str;
}


/*隐藏订单key*/

function order_key($id,$key='gOOgle*baiDu_Game'){
	return  md5($id.$key);
}

function check_order_key($cert,$id,$key='gOOgle*baiDu_Game'){
	return  md5($id.$key)==$cert?true:false;
}



/**
 * 得到新订单号
 * @return  string
 */
function get_order_sn()
{
	/* 选择一个随机的方案 */
	mt_srand((double) microtime() * 1000000);

	return date('Ymd') . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
}



/*还原定单号*/
function unformatbsn($bsn)
{
	//if(strlen(floatval($bsn)) != 11) return false;
	$bsn = substr($bsn,1);
	$design = array(9,3,2,7,0,4,6,1,5,8);
	$strA = array();
	foreach ($design as $k=>$v) {
		$strA[$v] = $bsn[$k];
	}
	ksort($strA);
	$str = implode('',$strA);
	return intval($str);
}

 function unescape($str) {  
   if(function_exists('iconv'))  
    {  
    $str = rawurldecode($str);  
     preg_match_all("/%u.{4}|&#x.{4};|&#\d+;|&#\d+?|.+/U",$str,$r);  
      $ar = $r[0];  
      foreach($ar as $k=>$v)  
      {  
       if(substr($v,0,2) == "%u")  
       $ar[$k] = iconv("UCS-2","UTF-8",pack("H4",substr($v,-4)));  
     elseif(substr($v,0,3) == "&#x")  
       $ar[$k] = iconv("UCS-2","UTF-8",pack("H4",substr($v,3,-1)));  
      elseif(substr($v,0,2) == "&#")  
      {  
       $ar[$k] = iconv("UCS-2","UTF-8",pack("n",preg_replace("/[^\d]/","",$v)));  
      }  
    }  
     return join("",$ar);  
     }  
     else 
     {  
      return $str;  
     }  
    } 


function getdowns($current_path = 'download')
  {
  	$down = array();
  	$dir = dir($current_path);
  	while ($file = $dir->read()) {
  		if ( (substr($file,0,1) != '.') && ($file != 'CVS') && ($file != 'Thumbs.db') )  {
  			$file_size = filesize($current_path . '/' . $file) ;
  			if ($showuser) {
  				$user = @posix_getpwuid(fileowner($current_path . '/' . $file));
  				$group = @posix_getgrgid(filegroup($current_path . '/' . $file));
  			} else {
  				$user = $group = array();
  			}
  			$dir2 = $current_path . '/' . $file;
  			if(is_dir($dir2)){
  				$down[] = array(
	  				'name' => $file,
		  			'dir' => $dir2,
		  			'last_modified' => strftime('%Y-%m-%d', filemtime($current_path . '/' . $file))
	  			);
  			} else {

	  			$down[] = array(
	  				'name' => extend_2($file),
		  			'exte' => extend_1($file),
		  			'dir' => $dir2,
		  			'last_modified' => strftime('%Y-%m-%d', filemtime($current_path . '/' . $file)),
		  			'size' => byte_format($file_size)
	  			);
  			}
  		}
  	}
  	usort($down, 'tep_cmp');
  	return $down;
  }
    function tep_cmp($a, $b) {
      return strcmp( $b['last_modified'], $a['last_modified']);
    }


	function extend_1($file_name)
	{
		$retval="";
		$pt=strrpos($file_name, ".");
		if ($pt) $retval=substr($file_name, $pt+1, strlen($file_name) - $pt);
		return ($retval);
	}
	function extend_2($file_name)
	{
		$retval="";
		$pt=strrpos($file_name, ".");
		if ($pt) $retval=substr($file_name, 0, $pt);
		return ($retval);
	}


function byte_format($input, $dec=0)
{
	$prefix_arr = array('bytes', 'Kb', 'Mb', 'Gb', 'Tb');
	$value = round($input, $dec);
	$i=0;
	while ($value>1024)
	{
		$value /= 1024;
		$i++;
	}
	$return_str = round($value, $dec).$prefix_arr[$i];
	return $return_str;
}


?>