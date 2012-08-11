<?php

! defined ( 'IN_SITE' ) && die ( 'Forbidden' );
class members extends base {

	function view() {
		die ();
	}
	
	
	function contact() {


		$where = '1 = 1 ';

		$page = ! empty ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
		$pagesize = 30;
		$strat = ($page - 1) * $pagesize;


		$url = '?adm=members&opt=contact&';


		if(isset($_GET['s'])){
			$s = daddslashes($_GET['s']);
			$where .= ' and username like "%'.$s.'%"';
			$url .= 's='.$s.'&';
		}


		$total = $this->db->do_count ( 'contactus', $where );


		$this->db->do_select ( 'contactus', '*', $where, 'id desc',$strat.','.$pagesize);
		$docs = array ( );
		while ( $row = $this->db->fetch_array () ) {
			$docs [] = $row;
		}
		$this->tpl->assign ( 'docs', $docs );
		$this->tpl->assign ( 'act', 'contact' );
		global $country;
		$this->tpl->assign ( 'country', $country );

		$this->tpl->assign ( 'pages', numofpage ( $total, $page, ceil ( $total / $pagesize ), $url ) );

		$this->tpl->show ( 'members' );
	}



	function orders() {


		$where = '1 = 1 ';

		$page = ! empty ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
		$pagesize = 30;
		$strat = ($page - 1) * $pagesize;


		$url = '?adm=members&opt=orders&';




		$total = $this->db->do_count ( 'orders', $where );


		$this->db->do_select ( 'orders', '*', $where, 'orders_id desc',$strat.','.$pagesize);
		$docs = array ( );
		while ( $row = $this->db->fetch_array () ) {
			$docs [] = $row;
		}
		$this->tpl->assign ( 'docs', $docs );
		$this->tpl->assign ( 'act', 'orders' );
		global $country;
		$this->tpl->assign ( 'country', $country );

		$this->tpl->assign ( 'pages', numofpage ( $total, $page, ceil ( $total / $pagesize ), $url ) );

		$this->tpl->show ( 'members' );
	}
	
	
	function all() {

	//rank 0默認  4禁用
		$where = '1 = 1 ';

		$page = ! empty ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
		$pagesize = 30;
		$strat = ($page - 1) * $pagesize;


		$url = '?adm=members&opt=all&';

		if(isset($_GET['rank']) && intval($_GET['rank']) > 0){
			$rank = intval($_GET['rank']);
		} else {
			$rank = 0;
		}
		$where .= ' and rank='.$rank;
		$url .= 'rank='.$rank.'&';
		$this->tpl->assign ( 'rank', $rank );

		if(isset($_GET['s'])){
			$s = daddslashes($_GET['s']);
			$where .= ' and username like "%'.$s.'%"';
			$url .= 's='.$s.'&';
		}

		//取記錄總數
		$total = $this->db->do_count ( 'customers', $where );


		$this->db->do_select ( 'customers', '*', $where, 'id desc',$strat.','.$pagesize);
		$docs = array ( );
		while ( $row = $this->db->fetch_array () ) {
			$docs [] = $row;
		}
		$this->tpl->assign ( 'docs', $docs );
		global $country;
		$this->tpl->assign ( 'country', $country );

		$this->tpl->assign ( 'pages', numofpage ( $total, $page, ceil ( $total / $pagesize ), $url ) );

		$this->tpl->show ( 'members' );
	}
	
	
	
	
	

	function forbidden() {
		//状态改为4
		$id = isset ( $_GET ['id'] ) ? intval ( $_GET ['id'] ) : 0;
		if($id){
			$this->db->do_update('customers',array('rank'=>4),'id='.$id);
		}
		$this->syslogs("禁止用户:".$id."rank=4",'members,forbidden');
		//返回
		dheader ( $_SERVER ['HTTP_REFERER'] );
	}
	
	
	function addnew(){
		
		$d = dir("../download/sheet/");
		$dd = array();
		while (false !== ($file = $d->read())) {
		   if ( (substr($file,0,1) != '.') && ($file != 'CVS') && ($file != 'Thumbs.db') )  {
			   $dd[] = $file;
		   }
		}
		$d->close();
		$this->tpl->assign ( 'dd', $dd );
		$this->tpl->assign ( 'act', "add" );
		$this->tpl->show ( 'members' );
	}
	
	function edit(){
		$d = dir("../download/sheet/");
		$dd = array();
		while (false !== ($file = $d->read())) {
		   if ( (substr($file,0,1) != '.') && ($file != 'CVS') && ($file != 'Thumbs.db') )  {
			   $dd[] = $file;
		   }
		}
		$d->close();
		$this->tpl->assign ( 'dd', $dd );
		
		$doc = $this->db->do_one('customers', '*', 'id='.(int)$_GET['id']);
		$this->tpl->assign ( 'doc', $doc );
				
				
		$this->tpl->assign ( 'act', "edit" );
		$this->tpl->show ( 'members' );
	}
	
	
	function user_do(){
		$step = !empty ( $_POST ['step'] ) ? trim($_POST ['step']) : '';
		$reurl = "?adm=members&opt=all";
		$tbl = "customers";
		$doc = $_POST['doc'];
		
		$ps = $doc ['password'];
		if($step == 'add'){
			$doc ['regtime'] = time();
			$doc ['password'] = encrypt_password ( $_POST['password'] );
			$this->db->do_insert ( $tbl, $doc );
			$mmm = 'game_addok';
		} else {
			if(!empty($_POST['password'])) $doc ['password'] = encrypt_password ( $_POST['password'] );
			$this->db->do_update ( $tbl, $doc, 'id='.$_POST['id']);
			$mmm = 'game_editok';	
		}
		
		//send mail
		if($_POST['isnotify'] == 1){
			$doc['ps'] = $_POST['password'];
			$this->tpl->assign ( 'doc', $doc );
			$body = $this->tpl->fetch("members_info");
			//send_mail("Your information has been updated.", $body, $doc['email']);
			
			$subject = "Your information has been updated.";
			include_once D_ROOT.'source/mailer/class.phpmailer.php';
			$mail = new PHPMailer ( );
			$mail->From = "info@avatarseeds.com";
			$mail->FromName = "avatarseeds.com";
			$mail->Subject = $subject;
			$mail->MsgHTML ( $body );
			$mail->AddAddress ( $email, $username );
			$mail->AddAddress ( "info@avatarseeds.com", "info" );
			return $mail->Send ();
	
		}
		
		
		
		
		$this->showmsg ( $mmm, $reurl );
	}
	


	function del(){

		$id = isset ( $_GET ['id'] ) ? intval ( $_GET ['id'] ) : 0;
		if($id){
			$user = $this->db->do_one('customers','*','id='.$id);
			//$this->db->do_update('customers',array('rank'=>9),'id='.$id);
			$this->db->query('delete from customers where id='.$id);
		}
		$this->syslogs("删除用户:".$id.",".serialize($user),'members,restart');
		//返回
		dheader ( $_SERVER ['HTTP_REFERER'] );
	}
	
	function vieworders(){
		$sql2 = "select * from orders where orders_id = ". (int)$_GET['id'];
		
		$doc = $this->db->do_one_bysql ( $sql2  );
		//pre($doc);
		
		if(!empty($doc['orders_id'])){
			$docs = $this->db->do_all_bysql ( "select * from orders_products  where oid = ". $doc['orders_id'] ."  order by id " );
			$this->tpl->assign ( 'docs', $docs );
		}
		$this->tpl->assign ( 'doc', $doc );
		$this->tpl->assign ( 'act', "vieworders" );
		$this->tpl->show ( 'members' );
	}
	
	
	function delorders(){
		//状态改为9
		$id = isset ( $_GET ['id'] ) ? intval ( $_GET ['id'] ) : 0;
		if($id){

			$this->db->query('delete from orders where orders_id='.$id);
			$this->db->query('delete from orders_products where oid='.$id);
		}
		$this->syslogs("删除order:".$id,'members,delorders');
		//返回
		dheader ( $_SERVER ['HTTP_REFERER'] );
	}
	
	function delcontact(){
		//状态改为9
		$id = isset ( $_GET ['id'] ) ? intval ( $_GET ['id'] ) : 0;
		if($id){

			$this->db->query('delete from contactus where id='.$id);
		}
		$this->syslogs("删除order:".$id,'members,delcontact');
		//返回
		dheader ( $_SERVER ['HTTP_REFERER'] );
	}
	
	function viewcontact(){
		$sql2 = "select * from contactus where id = ". (int)$_GET['id'];
		
		$doc = $this->db->do_one_bysql ( $sql2  );

		$this->tpl->assign ( 'doc', $doc );
		$this->tpl->assign ( 'act', "viewviewcontact" );
		$this->tpl->show ( 'members' );
	}
}

?>