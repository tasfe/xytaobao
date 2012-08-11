<?php

! defined ( 'IN_SITE' ) && die ( 'Forbidden' );

class articles extends base {

	function view() {
		die();
	}
	
	function cate() {
		if(isset($_GET['act'])){
			$act = $_GET['act'];
			$this->tpl->assign ( 'act', $act );
			if($act == 'add'){
				
			} elseif($act == 'edit'){
				$doc = $this->db->do_one('articles_cate', '*', 'id='.(int)$_GET['id']);
				$this->tpl->assign ( 'doc', $doc );
			} elseif ($act == 'del'){
				$doc = array('status' => 1);
				$this->db->do_update ( 'articles_cate', $doc, 'id="' . $_GET ['id'] . '"' );
				$this->showmsg ( 'cate_delok', 'admin.php?adm=articles&opt=cate' );
			}
		} else {
			$this->db->do_select ( 'articles_cate', '*', 'status=0', 'id desc' );
			$docs = array();
			while ( $row = $this->db->fetch_array () ) {
				$docs [] = $row;
			}
			$this->tpl->assign ( 'docs', $docs );
		}
		$this->tpl->show ( 'article_cate' );
	}
	
	function cate_do()
	{
		$step = isset ( $_POST ['step'] ) ? $_POST ['step'] : '';
		
		$url = 'admin.php?adm=articles&opt=cate';

		if ($step == 'add') {
			$doc = array ( );
			$doc ['cname'] = daddslashes($_POST['cname']);
			$doc ['fid'] = daddslashes($_POST['fid']);
			$doc ['dateline'] = $this->_now;

			$this->db->do_insert ( 'articles_cate', $doc );
			$this->showmsg ( 'info_addok', $url );
		} elseif ($step == 'edit') {
			$doc = array ( );
			$doc ['cname'] = daddslashes($_POST['cname']);
			$doc ['fid'] = daddslashes($_POST['fid']);
			$doc ['dateline'] = $this->_now;
			$this->db->do_update ( 'articles_cate', $doc, 'id="' . $_POST ['id'] . '"' );
			$this->showmsg ( 'info_editok',  $url);
		}
	}


	function info()
	{
		
		$this->db->do_select ( 'articles_cate', 'id,cname', 'status=0' );
		$cates = array();
		while ( $row = $this->db->fetch_array () ) {
			$cates [] = $row;
		}
		$this->tpl->assign ( 'cates', $cates );
		$cid = "";
		if(!empty($_GET['cid']) && intval($_GET['cid']) > 0){
			$cid = intval($_GET['cid']);
		}
		$this->tpl->assign ( 'cid', $cid );
			
		if(isset($_GET['act'])){
			$act = $_GET['act'];
			$this->tpl->assign ( 'act', $act );
			if($act == 'add'){
				$this->tpl->assign ( 'addtime', date('Y-m-d H:i:s', time()) );
				$this->tpl->assign ( 'doc', array("cid"=>$cid) );
			} elseif($act == 'edit'){
				$doc = $this->db->do_one('articles_info', '*', 'id='.(int)$_GET['id']);
				$this->tpl->assign ( 'doc', $doc );
			} elseif ($act == 'del'){
				$doc = array('status' => 1);
				$this->db->do_update ( 'articles_info', $doc, 'id="' . $_GET ['id'] . '"' );
				$this->showmsg ( 'info_delok', 'admin.php?adm=articles&opt=info' );
			}
		} else {
			
			$sql = "select a.*,b.cname from articles_info a left join articles_cate b on b.id = a.cid where a.status=0";
			if(!empty($cid)){
				$sql .= " and cid=".intval($_GET['cid']);
			}
			
			$sql .= " order by a.id desc";
			$docs = $this->db->do_all_bysql ( $sql );
			
			$this->tpl->assign ( 'docs', $docs );
		}
		$this->tpl->show ( 'article_info' );
	}

	function info_do()
	{
		$step = isset ( $_POST ['step'] ) ? $_POST ['step'] : '';

		if ($step == 'add') {
			$doc = array ( );
			$doc ['title'] = daddslashes($_POST['title']);
			$doc ['cid'] = daddslashes($_POST['cid']);
			$doc ['summary'] = daddslashes($_POST['summary']);
			$doc ['contents'] = daddslashes ( $_POST ['contents'] );
			$doc ['addtime'] = strtotime ( $_POST ['addtime'] );
			$doc ['linkurl'] = $_POST ['linkurl'];
			$this->db->do_insert ( 'articles_info', $doc );
			$this->showmsg ( 'info_addok', 'admin.php?adm=articles&opt=info' );
		} elseif ($step == 'edit') {
			$doc = array ( );
			$doc ['title'] = daddslashes ( $_POST ['title'] );
			$doc ['summary'] = daddslashes ( $_POST ['summary'] );
			$doc ['contents'] = daddslashes ( $_POST ['contents'] );
			$doc ['cid'] = daddslashes($_POST['cid']);
			$doc ['addtime'] = strtotime ( $_POST ['addtime'] );
			if($_POST['linkurl']){
				$doc ['linkurl'] = $_POST ['linkurl'];
			}
			$this->db->do_update ( 'articles_info', $doc, 'id="' . $_POST ['id'] . '"' );
			$this->showmsg ( 'info_editok', 'admin.php?adm=articles&opt=info' );
		}
	}


	function help()
	{
		$this->tpl->assign ( 'action', 'help' );
		if(isset($_GET['act'])){
			$act = $_GET['act'];
			$this->tpl->assign ( 'act', $act );
			if($act == 'add'){
				//$this->tpl->assign ( 'addtime', date('Y-m-d H:i:s', time()) );
			} elseif($act == 'edit'){
				$doc = $this->db->do_one('articles_help', '*', 'id='.(int)$_GET['id']);
				$this->tpl->assign ( 'doc', $doc );
			} elseif ($act == 'del'){
				$doc = array('status' => 1);
				$this->db->do_update ( 'articles_help', $doc, 'id="' . $_GET ['id'] . '"' );
				$this->showmsg ( 'help_delok', 'admin.php?adm=articles&opt=help' );
			}
		} else {
			$this->db->do_select ( 'articles_help', '*', 'cid=1 and status=0', 'sorder,id desc' );
			$docs = array();
			while ( $row = $this->db->fetch_array () ) {
				$docs [] = $row;
			}
			$this->tpl->assign ( 'docs', $docs );
		}
		$this->tpl->show ( 'article_help' );
	}

	function help_do()
	{

		$step = isset ( $_POST ['step'] ) ? $_POST ['step'] : '';

		if ($step == 'add') {
			$doc = array ( );
			$doc ['title'] = daddslashes($_POST['title']);
			//$doc ['summary'] = daddslashes($_POST['summary']);
			$doc ['contents'] = daddslashes ( $_POST ['contents'] );
			$doc ['sorder'] = intval ( $_POST ['sorder'] );
			$doc ['addtime'] = time();
			$doc ['editer'] = $_SESSION['s_user'];
			$doc ['linkurl'] = $_POST ['linkurl'];
			$doc ['cid'] = 1; //帮助中心 cid=1
			$this->db->do_insert ( 'articles_help', $doc );
			$this->showmsg ( 'help_addok', 'admin.php?adm=articles&opt=help' );
		} elseif ($step == 'edit') {
			$doc = array ( );
			$doc ['title'] = daddslashes ( $_POST ['title'] );
			//$doc ['summary'] = daddslashes ( $_POST ['summary'] );
			$doc ['contents'] = daddslashes ( $_POST ['contents'] );
			$doc ['sorder'] = intval ( $_POST ['sorder'] );
			$doc ['addtime'] = time();
			if($_POST['linkurl']){
				$doc ['linkurl'] = $_POST ['linkurl'];
			}
			$this->db->do_update ( 'articles_help', $doc, 'id="' . $_POST ['id'] . '"' );
			$this->showmsg ( 'help_editok', 'admin.php?adm=articles&opt=help' );
		}
	}

	function adhelp()
	{
		$this->tpl->assign ( 'action', 'adhelp' );
		if(isset($_GET['act'])){
			$act = $_GET['act'];
			$this->tpl->assign ( 'act', $act );
			if($act == 'add'){
				//$this->tpl->assign ( 'addtime', date('Y-m-d H:i:s', time()) );
			} elseif($act == 'edit'){
				$doc = $this->db->do_one('articles_help', '*', 'id='.(int)$_GET['id']);
				$this->tpl->assign ( 'doc', $doc );
			} elseif ($act == 'del'){
				$doc = array('status' => 1);
				$this->db->do_update ( 'articles_help', $doc, 'id="' . $_GET ['id'] . '"' );
				$this->showmsg ( 'help_delok', 'admin.php?adm=articles&opt=adhelp' );
			}
		} else {
			$this->db->do_select ( 'articles_help', '*', 'status=0 and cid=2', 'sorder,id desc' );
			$docs = array();
			while ( $row = $this->db->fetch_array () ) {
				$docs [] = $row;
			}
			$this->tpl->assign ( 'docs', $docs );
		}
		$this->tpl->show ( 'article_help' );
	}

	function adhelp_do()
	{
		$step = isset ( $_POST ['step'] ) ? $_POST ['step'] : '';

		if ($step == 'add') {
			$doc = array ( );
			$doc ['title'] = daddslashes($_POST['title']);
			//$doc ['summary'] = daddslashes($_POST['summary']);
			$doc ['contents'] = daddslashes ( $_POST ['contents'] );
			$doc ['sorder'] = intval ( $_POST ['sorder'] );
			$doc ['addtime'] = time();
			$doc ['editer'] = $_SESSION['s_user'];
			$doc ['cid'] = 2; //廣告協助 cid=2
			$this->db->do_insert ( 'articles_help', $doc );
			$this->showmsg ( 'help_addok', 'admin.php?adm=articles&opt=adhelp' );
		} elseif ($step == 'edit') {
			$doc = array ( );
			$doc ['title'] = daddslashes ( $_POST ['title'] );
			//$doc ['summary'] = daddslashes ( $_POST ['summary'] );
			$doc ['contents'] = daddslashes ( $_POST ['contents'] );
			$doc ['sorder'] = intval( $_POST ['sorder'] );
			$doc ['addtime'] = time();
			if($_POST['linkurl']){
				$doc ['linkurl'] = $_POST ['linkurl'];
			}
			$this->db->do_update ( 'articles_help', $doc, 'id="' . $_POST ['id'] . '"' );
			$this->showmsg ( 'help_editok', 'admin.php?adm=articles&opt=adhelp' );
		}
	}

	function ucenter()
	{
		$this->tpl->assign ( 'action', 'ucenter' );
		if(isset($_GET['act'])){
			$act = $_GET['act'];
			$this->tpl->assign ( 'act', $act );
			if($act == 'add'){
				//$this->tpl->assign ( 'addtime', date('Y-m-d H:i:s', time()) );
			} elseif($act == 'edit'){
				$doc = $this->db->do_one('articles_help', '*', 'id='.(int)$_GET['id']);
				$this->tpl->assign ( 'doc', $doc );
			} elseif ($act == 'del'){
				$doc = array('status' => 1);
				$this->db->do_update ( 'articles_help', $doc, 'id="' . $_GET ['id'] . '"' );
				$this->showmsg ( 'help_delok', 'admin.php?adm=articles&opt=adhelp' );
			}
		} else {
			$this->db->do_select ( 'articles_help', '*', 'status=0 and cid=3', 'sorder,id desc' );
			$docs = array();
			while ( $row = $this->db->fetch_array () ) {
				$docs [] = $row;
			}
			$this->tpl->assign ( 'docs', $docs );
		}
		$this->tpl->show ( 'article_help' );
	}

	function ucenter_do()
	{
		$step = isset ( $_POST ['step'] ) ? $_POST ['step'] : '';

		if ($step == 'add') {
			$doc = array ( );
			$doc ['title'] = daddslashes($_POST['title']);
			//$doc ['summary'] = daddslashes($_POST['summary']);
			$doc ['contents'] = daddslashes ( $_POST ['contents'] );
			$doc ['sorder'] = intval ( $_POST ['sorder'] );
			$doc ['addtime'] = time();
			$doc ['editer'] = $_SESSION['s_user'];
			$doc ['linkurl'] = $_POST ['linkurl'];
			$doc ['cid'] = 3; //訪客中心 cid=3
			$this->db->do_insert ( 'articles_help', $doc );
			$this->showmsg ( 'help_addok', 'admin.php?adm=articles&opt=ucenter' );
		} elseif ($step == 'edit') {
			$doc = array ( );
			$doc ['title'] = daddslashes ( $_POST ['title'] );
			//$doc ['summary'] = daddslashes ( $_POST ['summary'] );
			$doc ['contents'] = daddslashes ( $_POST ['contents'] );
			$doc ['sorder'] = intval( $_POST ['sorder'] );
			$doc ['addtime'] = time();
			if($_POST['linkurl']){
				$doc ['linkurl'] = $_POST ['linkurl'];
			}
			$this->db->do_update ( 'articles_help', $doc, 'id="' . $_POST ['id'] . '"' );
			$this->showmsg ( 'help_editok', 'admin.php?adm=articles&opt=ucenter' );
		}
	}



}

?>