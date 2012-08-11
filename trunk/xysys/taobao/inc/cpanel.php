<?php
class cpanel extends base {

	function view() {
		die();
	}

	function top()
	{
		$this->tpl->assign("user", $_SESSION['s_user']);
		$this->tpl->show('top');
	}

	function tree()
	{
		$trees = $this->db->do_all ( 'tree', '*','id=pid and adm <>""','sorder' );
		foreach ($trees as $key => $val){
			$trees[$key]['sub'] =  $this->db->do_all ( 'tree', '*','id <> pid and pid='.$val['id'],'sorder' );
		}
		$this->tpl->assign ( 'trees', $trees );
		$this->tpl->show ( 'tree' );
	}

	function logout()
	{
		unset($_SESSION['s_user']);
		unset($_SESSION['s_priv']);
		dheader('index.php');
	}
	
}
?>