<?php

! defined ( 'IN_SITE' ) && die ( 'Forbidden' );
class systems extends base {

	function view() {
		die ();
	}

	function top() {
		$this->tpl->show ( 'top' );
	}

	function tree() {
		$trees = $this->db->do_one ( 'tree', '*', "adm='systems'" );
		$this->db->do_select ( 'tree', '*', 'id <> pid and pid=' . $trees ['id'], 'sorder' );
		while ( $row = $this->db->fetch_array () ) {
			$trees ['sub'] [] = $row;
		}
		$this->tpl->assign ( 'tree', $trees );
		$this->tpl->show ( 'tree1' );
	}

	function user() {
		$act = isset ( $_GET ['act'] ) ? daddslashes ( $_GET ['act'] ) : '';
		$this->tpl->assign ( 'act', $act );
		if (! $act) {
			$this->db->do_select ( 'admin', '*', 'status = 1' );
			$docs = array ();
			while ( $row = $this->db->fetch_array () ) {
				$docs [] = $row;
			}
			$this->tpl->assign ( 'docs', $docs );

		} elseif ($act == 'edit') {
			$doc = $this->db->do_one ( 'admin', 'id, username, email', 'id=' . ( int ) $_GET ['id'] . ' and status = 1' );
			$this->tpl->assign ( 'doc', $doc );
		} elseif ($act == 'del') {
			//$this->db->query("delete from admin where id=".(int)$_GET['id']);
			$this->db->do_update ( 'admin', array ('status' => 0 ), 'id="' . $_GET ['id'] . '"' );
			$this->showmsg ( 'admin_delok', 'admin.php?adm=systems&opt=user' );
		}

		$this->tpl->show ( 'systems_user' );
	}

	function user_do() {
		$step = isset ( $_POST ['step'] ) ? intval ( $_POST ['step'] ) : '';
		if ($step == 1) {
			if ($_POST ['new_password2'] != $_POST ['new_password']) {
				$this->showmsg ( 'set_confirmpwd', 'admin.php?adm=systems&opt=user&act=add' );
			}
			$_user = daddslashes ( $_POST ['new_username'] );
			$num = $this->db->do_count ( 'admin', 'username="' . $_user . '" and status = 1' );
			if ($num) {
				$this->showmsg ( 'admin_userexists', 'admin.php?adm=systems&opt=user&act=add' );
			}
			$doc = array ();
			$doc ['username'] = $_user;
			$doc ['password'] = encrypt_password ( $_POST ['new_password'] );
			$doc ['email'] = daddslashes ( $_POST ['new_email'] );
			$this->db->do_insert ( 'admin', $doc );
			$this->showmsg ( 'admin_addok', 'admin.php?adm=systems&opt=user' );
		} elseif ($step == 2) {
			$doc = array ();
			if ($_POST ['new_password'] && $_POST ['new_password2'] != $_POST ['new_password']) {
				$this->showmsg ( 'set_confirmpwd', 'admin.php?adm=systems&opt=user&act=edit&id=' . $_POST ['id'] );
			}
			if ($_POST ['new_password2'] && $_POST ['new_password2'] && $_POST ['new_password'] == $_POST ['new_password2']) {
				$doc ['password'] = encrypt_password ( $_POST ['new_password'] );
			}
			$doc ['email'] = daddslashes ( $_POST ['new_email'] );
			$this->db->do_update ( 'admin', $doc, 'id="' . $_POST ['id'] . '"' );
			$this->showmsg ( 'admin_editok', 'admin.php?adm=systems&opt=user' );
		}
	}




	function setup(){
		$sid = isset ( $_POST ['id'] ) ? intval ( $_POST ['id'] ) : '';
		if($sid){
			$this->db->do_update ( 'config', array('sval'=>$_POST['sval']), 'id="' . $sid . '"' );
			$this->showmsg ( 'config_editok', 'admin.php?adm=systems&opt=setup' );
		}
		$act = isset ( $_GET ['act'] ) ? daddslashes ( $_GET ['act'] ) : '';
		$this->tpl->assign ( 'act', $act );
		if($act == 'add'){
			if(!empty($_POST['skey']) && !empty($_POST['sval'])){
				$this->db->do_insert('config', array('skey'=>$_POST['skey'] ,'sval'=>$_POST['sval']) );
				$this->showmsg ( 'config_addok', 'admin.php?adm=systems&opt=setup' );
			}
			
		} elseif($act == 'edit'){
			$doc = $this->db->do_one ( 'config', '*', 'id=' . ( int ) $_GET ['id']  );
			if($doc['type'] == 'radio'){
				$this->tpl->assign ( 'cust_radios', array('1'=>'是','0'=>'否') );
			}
			$this->tpl->assign ( 'doc', $doc );
		}else {
			$docs = $this->db->do_all('config');
			$this->tpl->assign ( 'docs', $docs );
		}
		$this->tpl->show ( 'systems_config' );
		//pre($docs);
	}

	function vlogs(){
		$docs = $this->db->do_all_bysql ( "select * from system_log where act<>'index' order by id desc" );
		$this->tpl->assign ( 'docs', $docs );
		$this->tpl->show ( 'systems_logs' );
	}

}

?>