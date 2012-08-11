<?php

! defined ( 'IN_SITE' ) && die ( 'Forbidden' );

class orders extends base {

	function view() {
		die ();
	}


	function olist() {
die();
		//狀態 0默認  1審核 2管理員刪除 4用戶刪除
		$where = '1 = 1 ';

		$page = ! empty ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
		$pagesize = DISPLAY_NUM;
		$strat = ($page - 1) * $pagesize;

		$url = '?adm=orders&opt=olist';

		if(!empty($_GET['uid']) && intval($_GET['uid'])){
			$where .= " and a.uid=".intval($_GET['uid']);
			$url .= '&uid='.intval($_GET['uid']);
		}
		if(isset($_GET['os']) && $_GET['os'] != ''){
			$where .= " and a.status=" . $_GET ['os'];
			$this->tpl->assign ( 'os', $_GET['os'] );
			$url .= '&os='.$_GET['os'];
		}

		if (! empty ( $_GET ['keyword'] )) {
			if($_GET ['keyword'] == intval($_GET ['keyword'])){
				$oid = intval($_GET ['keyword']);
			} else {
				$oid = substr ( trim ( $_GET ['keyword'] ), 1 );
			}
			$where .= " and a.id=" . intval ( $oid );
			$this->tpl->assign ( 'keyword', $_GET['keyword'] );
			$url .= '&keyword='.$_GET['keyword'];
		}

		//取記錄總數
		$total = $this->db->do_count ( 'orders a ', $where );

		$sql = "select * from orders a" 
		. " where " . $where . " order by a.orders_id desc" . " limit $strat,$pagesize";
		//where b.ad_type=1";
		$docs = $this->db->do_all_bysql ( $sql );
		$this->tpl->assign ( 'docs', $docs );



		$this->tpl->assign ( 'pages', numofpage ( $total, $page, ceil ( $total / $pagesize ), $url."&" ) );

		$this->tpl->show ( 'orders' );
	}

	
	function detail() {
		global $langs;
		$oid = $_GET ['oid'];
		/*$sql = "select * from orders a left join orders_house_ad b on a.id=b.oid
		left join house_ad c on b.ad_id = c.id where a.id=".$oid;*/

		$sql = "select a.*,b.ad_id,b.ad_time from orders a left join orders_house_ad b on a.id = b.oid where a.id=" . $oid;
		$doc = $this->db->do_one_bysql ( $sql ); //訂單信息
		$this->tpl->assign ( 'doc', $doc );

		$sql = "select * from house_ad where id=" . $doc ['ad_id'];
		$doc = $this->db->do_one_bysql ( $sql ); //廣告信息
		$doc ['adtype'] = $langs ['ad_flow'] [$doc ['ad_type']];
		$this->tpl->assign ( 'ai', $doc );

		$sql = "select * from customers where id=" . $doc ['uid'];
		$doc = $this->db->do_one_bysql ( $sql ); //用戶信息
		$this->tpl->assign ( 'ui', $doc );

		$this->tpl->display ( 'orders_detail' );
	}

	function del() {
		$oid = $_GET ['oid'];
		//修改支付狀態0
		$doc = array ('status' => 2 );
		$this->db->do_update ( 'orders', $doc, 'id=' . $oid );

		//$row = $this->db->do_one ( 'orders_house_ad', 'ad_id', 'oid=' . $oid );
		//$this->db->do_update ( 'house_ad', array ('status' => 4 ), 'id=' . $row ['ad_id'] );

		//管理員日誌
		$this->syslogs("删除订单:".$oid."修改状态2",'orders,del');


		//返回
		dheader ( $_SERVER ['HTTP_REFERER'] );

	}

}

?>