<?php

! defined ( 'IN_SITE' ) && die ( 'Forbidden' );

class email extends base {

	function view() {
		die();
	}

	function message(){

		if(isset($_GET['act'])){
			$act = $_GET['act'];
			$this->tpl->assign ( 'act', $act );
			if($act == 'add'){
				
			} elseif($act == 'edit'){
				$doc = $this->db->do_one('email', '*', 'id='.(int)$_GET['id']);
				$this->tpl->assign ( 'doc', $doc );
			} elseif ($act == 'del'){
				$doc = array('status' => 1);
				$this->db->do_update ( 'email', $doc, 'id="' . $_GET ['id'] . '"' );
				$this->showmsg ( 'info_delok', 'admin.php?adm=email&opt=message' );
			} elseif ($act == 'send'){
				$doc = $this->db->do_one('email', '*', 'id='.(int)$_GET['id']);
				$this->tpl->assign ( 'doc', $doc );
			}
		} else {
			$where = " a.status = 0 ";

			$page = ! empty ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
			$pagesize = DISPLAY_NUM;
			$strat = ($page - 1) * $pagesize;
			$total = $this->db->do_count ( 'email a', $where );
			
			$sql = "select a.*  from email a   " . " where  $where order by a.id desc limit $strat,$pagesize";
			$docs = $this->db->do_all_bysql ( $sql );
			$this->tpl->assign ( 'docs', $docs );
			
		}
		$this->tpl->show ( 'email' );

	}
	
	function message_do(){
		$step = isset ( $_POST ['step'] ) ? $_POST ['step'] : '';

		$reurl = "?adm=email&opt=message";

		$tbl = "email";

		if ($step == 'add') {
			$doc = array ( );
			$doc ['title'] = daddslashes($_POST['title']);
			$doc ['message'] = daddslashes($_POST['summary']);
			$doc ['dateline'] = $this->_now;
			$doc ['editer'] = $_SESSION['s_user'];

			$this->db->do_insert ( $tbl, $doc );
			$this->showmsg ( 'info_addok', $reurl );
		} elseif ($step == 'edit') {
			$doc = array ( );
			$doc = array ( );
			$doc ['title'] = daddslashes($_POST['title']);
			$doc ['message'] = daddslashes($_POST['summary']);
			$doc ['dateline'] = $this->_now;
			$doc ['editer'] = $_SESSION['s_user'];
			
			$this->db->do_update ( $tbl, $doc, 'id="' . $_POST ['id'] . '"' );
			$this->showmsg ( 'info_editok', $reurl );
		}
	}

	function message1(){
		$this->tpl->show ( 'email' );
		
	}
	
	

	function sendmail(){
		$conf = $this->db->do_all('config', 'skey,sval', 'skey like "smtp%"');
		$sc = array();
		foreach($conf as $val){
			$sc[$val['skey']] = $val['sval'];
		}

		//检查邮箱
		//$to = array("ccajax@qq.com","ccajax@gmail.com");
		if(!empty($_POST['text']))  $to = explode("\r\n",$_POST['text']);
		else $to = array();

		//注册用户
		if(!empty($_POST['membersd'])){
			$sql = "select email  from customers";
			$this->db->query($sql);
			while($row = $this->db->fetch_array()){
				$to[] = $row['email'];
			}
		}

		//pre($to);

		//邮件内容
		$doc = $this->db->do_one('email', '*', 'id='.(int)$_POST['id']);


require_once ("Mail.php");
require_once ("Mail/mime.php");



		$from = $sc["smtpemail"];
		
		$subject = $doc["title"];
		$body = $doc["message"];

		$host = $sc["smtphost"];
		$username = $sc["smtpuser"];
		$password = $sc["smtppass"];


		$crlf = "\n";
$mime = new Mail_mime($crlf);



		$headers = array (
		 'From' => $from,
		 'To' => $to,
		 'Subject' =>"=?UTF-8?B?".base64_encode($subject)."?="
		);
		$smtp = Mail::factory(
		 'smtp',
		 array (
		  'host' => $host,
		  'auth' => true,
		  'username' => $username,
		  'password' => $password
		 )
		);

$param["html_charset"] ='utf-8'; //内容乱码
$mime->setHTMLBody($body);
$message = $mime->get($param);
$headers = $mime->headers($headers);


		$mail = $smtp->send($to, $headers, $message);

		if (PEAR::isError($mail)) {
		 echo("<p>" . $mail->getMessage() . "</p>");
		} else {
		 echo("<p>送信成功!</p>");
		}

		//send_mail("test", "ok100", array("ccajax@qq.com",'chenyf@5gzl.com','bjgame001@126.com'), "smtp", $sc);
		//pre($_POST);
		//echo 1;
	}

}

?>