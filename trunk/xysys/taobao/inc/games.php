<?php

! defined ( 'IN_SITE' ) && die ( 'Forbidden' );

class games extends base {

	function view() {
		die();
	}


	function mygame(){
		
		$sql = "select id,gname from games where status <>4 and istop=0 order by place";
		$games = $this->db->do_all_bysql($sql);
		$this->tpl->assign ( 'games', $games );
		

		if(isset($_GET['act'])){
			$act = $_GET['act'];
			$this->tpl->assign ( 'act', $act );
			if($act == 'add'){
				$this->tpl->assign ( 'addtime', date('Y-m-d H:i:s', time()) );
			} elseif($act == 'edit'){
				$doc = $this->db->do_one('games', '*', 'id='.(int)$_GET['id']);
				$this->tpl->assign ( 'doc', $doc );
			} elseif ($act == 'del'){
				$doc = array('status' => 4, 'seoname' => time());
				$this->db->do_update ( 'games', $doc, 'id="' . $_GET ['id'] . '"' );
				$this->showmsg ( 'game_delok', 'admin.php?adm=games&opt=mygame' );
			}
		} else {
			$where = " a.status<>4 ";
		
			$url = '?adm=games&opt=mygame';
	
			$page = ! empty ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
			$pagesize = DISPLAY_NUM;
			$strat = ($page - 1) * $pagesize;
			$total = $this->db->do_count ( 'games a', $where );
	
			$sql = "select a.*  from games a   " ." where  $where order by a.istop,a.place  ";
			$this->db->query($sql);
			$docs = array();
			while($row = $this->db->fetch_array()){
				if($row['istop'] == 0){
					$docs[$row['id']] = $row;
				} else {
					$docs[$row['istop']]['sub'][] = $row;
				}
			}
			
			$this->tpl->assign ( 'docs', $docs );
			$this->tpl->assign ( 'now', $this->_now );
	
			$this->tpl->assign ( 'pages', numofpage ( $total, $page, ceil ( $total / $pagesize ), $url.'&' ) );
		}
		$this->tpl->show ( 'games' );

	}

	function mygame_do(){
		$step = isset ( $_POST ['step'] ) ? $_POST ['step'] : '';

		$reurl = "?adm=games&opt=mygame";

		$tbl = "games";
		
		$doc = array ( );
		$doc ['gname'] = daddslashes ( $_POST ['gname'] );
		$doc ['summary'] = daddslashes ( $_POST ['summary'] );
		
		$doc ['place'] = (int)$_POST ['place'];
		$doc ['istop'] = (int)$_POST ['istop'];

		$imgs = upload_multi ();
		if (is_array ( $imgs )) {
			if(!empty($imgs [0])) $doc ['image'] = $imgs [0];
		}
		

		if ($step == 'add') {
			$this->db->do_insert ( $tbl, $doc );
			$this->showmsg ( 'game_addok', $reurl );
		} elseif ($step == 'edit') {
			$this->db->do_update ( $tbl, $doc, 'id="' . $_POST ['id'] . '"' );
			$this->showmsg ( 'game_editok', $reurl );
		}
	}
	
	function service() {
		
		$sql = "select id,gname from games where status <>4";
		$games = $this->db->do_all_bysql($sql);
		$this->tpl->assign ( 'games', $games );
			
		if(isset($_GET['act'])){
			$act = $_GET['act'];
			$this->tpl->assign ( 'act', $act );
			
			
			if($act == 'add'){
				$this->tpl->assign ( 'addtime', date('Y-m-d H:i:s', time()) );
			} elseif($act == 'edit'){
				$doc = $this->db->do_one('services', '*', 'id='.(int)$_GET['id']);
				$this->tpl->assign ( 'doc', $doc );
			} elseif ($act == 'del'){
				$doc = array('status' => 4, );
				$this->db->do_update ( 'services', $doc, 'id="' . $_GET ['id'] . '"' );
				$this->showmsg ( 'game_delok', 'admin.php?adm=games&opt=service' );
			}
		} else {
			$where = " a.status<>4 ";
			
			if(!empty($_GET['gid'])) {
				$where .= " and gid=".intval($_GET['gid']);
				$this->tpl->assign ( 'gid', (int)$_GET['gid'] );
			}
		
			$url = '?adm=games&opt=service';
	
			$page = ! empty ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
			$pagesize = DISPLAY_NUM;
			$strat = ($page - 1) * $pagesize;
	
			$total = $this->db->do_count ( 'services a', $where );
	
			$sql = "select a.*,b.gname  from services a left join games b on a.gid=b.id  " ." where  $where order by a.id desc limit $strat,$pagesize";
	
			$docs = $this->db->do_all_bysql($sql);
			$this->tpl->assign ( 'docs', $docs );
			$this->tpl->assign ( 'now', $this->_now );
	
			$this->tpl->assign ( 'pages', numofpage ( $total, $page, ceil ( $total / $pagesize ), $url.'&' ) );
		}
		$this->tpl->show("service");
	}
	
	function service_do(){
		$step = isset ( $_POST ['step'] ) ? $_POST ['step'] : '';

		$reurl = "?adm=games&opt=service";

		$tbl = "services";
		$doc = array ( );
		$doc ['sname'] = daddslashes($_POST['sname']);
		$doc ['gid'] = daddslashes($_POST['gid']);
		$doc ['place'] = daddslashes ( $_POST ['place'] );
			
		if ($step == 'add') {
			$this->db->do_insert ( $tbl, $doc );
			$this->showmsg ( 'game_addok', $reurl );
		} elseif ($step == 'edit') {
			
			$this->db->do_update ( $tbl, $doc, 'id="' . $_POST ['id'] . '"' );
			$this->showmsg ( 'game_editok', $reurl );
		}
	}
	
	function gold() {
		
		if(isset($_GET['act'])){
			$act = $_GET['act'];
			if($act == 'add'){
				$doc = array ( );
				$doc ['pname'] = daddslashes($_GET['pname']);
				$doc ['gid'] = daddslashes($_GET['gid']);
				$doc ['sid'] = daddslashes ( $_GET ['sid'] );
				$doc ['price'] = daddslashes($_GET['price']);
				$doc ['num'] = daddslashes ( $_GET ['num'] );
	
				$this->db->do_insert ( 'products', $doc );
				echo 1;
			} elseif($act == 'ajax'){
				$doc = array ( );
				$field = daddslashes($_GET['editfield']);
				$doc [$field] = trim( unescape($_GET['val']) );
				$this->db->do_update ( "products", $doc, 'id="' . $_GET ['id'] . '"' );
				echo 1;
			} elseif ($act == 'del'){
				$doc = array('status' => 4, );
				$this->db->do_update ( 'products', $doc, 'id="' . $_GET ['id'] . '"' );
				echo 1;
			}
		} else {
			$sql = "select id,gname from games where status <>4";
			$games = $this->db->do_all_bysql($sql);
			$this->tpl->assign ( 'games', $games );
			
			$where = " a.status <>4 ";
			$url = '?adm=games&opt=gold&';
	
			$page = ! empty ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
			$pagesize = DISPLAY_NUM;
			$strat = ($page - 1) * $pagesize;
			$total = $this->db->do_count ( 'products a', $where );
			
			$sql2 = "select a.*,b.gname,c.sname  from products a left join games b on a.gid=b.id  left join services c on a.sid=c.id " 
			." where  $where order by a.id desc limit $strat,$pagesize";
	
			$docs = $this->db->do_all_bysql($sql2);
			$this->tpl->assign ( 'docs', $docs );
			$this->tpl->assign ( 'pages', numofpage ( $total, $page, ceil ( $total / $pagesize ), $url ) );
			$this->tpl->show("gold");
		}
	}

	function leveling() {
		
		$sql = "select id,gname from games where status <>4";
		$games = $this->db->do_all_bysql($sql);
		$this->tpl->assign ( 'games', $games );
		
		if(isset($_GET['act'])){
			$act = $_GET['act'];
			$this->tpl->assign ( 'act', $act );
			
			
			if($act == 'add'){
				$this->tpl->assign ( 'addtime', date('Y-m-d H:i:s', time()) );
			} elseif($act == 'edit'){
				$doc = $this->db->do_one('leveling', '*', 'id='.(int)$_GET['id']);
				$this->tpl->assign ( 'doc', $doc );
			} elseif ($act == 'del'){
				$doc = array('status' => 4, );
				$this->db->do_update ( 'leveling', $doc, 'id="' . $_GET ['id'] . '"' );
				$this->showmsg ( 'game_delok', 'admin.php?adm=games&opt=leveling' );
			}
		} else {
			
			
			$where = " 1 =1 ";
			$url = '?adm=games&opt=gold&';
	
			$page = ! empty ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
			$pagesize = DISPLAY_NUM;
			$strat = ($page - 1) * $pagesize;
			$total = $this->db->do_count ( 'leveling a', $where );
			
			$sql2 = "select a.*,b.gname,c.sname  from leveling a left join games b on a.gid=b.id  left join services c on a.sid=c.id " 
			." where  $where order by a.id desc limit $strat,$pagesize";
	
			$docs = $this->db->do_all_bysql($sql2);
			$this->tpl->assign ( 'docs', $docs );
			$this->tpl->assign ( 'pages', numofpage ( $total, $page, ceil ( $total / $pagesize ), $url ) );
			
		}
		$this->tpl->show("leveling");
	}
	
	function leveling_do(){
		$step = isset ( $_POST ['step'] ) ? $_POST ['step'] : '';

		$reurl = "?adm=games&opt=leveling";

		$tbl = "leveling";
		
		$doc = array ( );
		$doc ['lname'] = daddslashes($_POST['lname']);
		$doc ['gid'] = daddslashes($_POST['gid']);
		$doc ['sid'] = daddslashes ( $_POST ['sid'] );
		$doc ['price'] = daddslashes($_POST['price']);
		$doc ['days'] = intval($_POST['days']);
		$doc ['summary'] = daddslashes ( $_POST ['summary'] );

		if ($step == 'add') {
			$this->db->do_insert ( $tbl, $doc );
			$this->showmsg ( 'game_addok', $reurl );
		} elseif ($step == 'edit') {
			$this->db->do_update ( $tbl, $doc, 'id="' . $_POST ['id'] . '"' );
			$this->showmsg ( 'game_editok', $reurl );
		}
	}
	
	
	function equipment() {
		
		$sql = "select id,gname from games where status <>4 and istop=0";
		$games = $this->db->do_all_bysql($sql);
		$this->tpl->assign ( 'games', $games );
		
		if(isset($_GET['act'])){
			$act = $_GET['act'];
			$this->tpl->assign ( 'act', $act );
			
			
			if($act == 'add'){
				$this->tpl->assign ( 'addtime', date('Y-m-d H:i:s', time()) );
			} elseif($act == 'edit'){
				$doc = $this->db->do_one('equipment', '*', 'id='.(int)$_GET['id']);
				$this->tpl->assign ( 'doc', $doc );
				$imgs = $this->db->do_all('equipment_img', '*', 'equipment_id='.(int)$_GET['id']);
				$this->tpl->assign ( 'imgs', $imgs );
			} elseif ($act == 'del'){
				$this->db->query ( 'delete from equipment_img where id="' . $_GET ['id'] . '"' );
				echo 1;die();
			} elseif($act == 'dele'){
				$this->db->query ( 'delete from equipment where id="' . $_GET ['id'] . '"' );
				$this->db->query ( 'delete from equipment_img where equipment_id="' . $_GET ['id'] . '"' );
				echo 1;die();
			} elseif($act == 'imgdef'){
				//echo($_GET['path']);
				$this->db->do_update ( 'equipment', array('image'=>$_GET['path']), 'id="' . $_GET ['id'] . '"' );
				echo 1;die();
				
			}
		} else {
			
			
			$where = " 1 =1 ";
			$url = '?adm=games&opt=equipment&';
			
			if(!empty($_GET['gid'])){
				$where .= "  and a.gid =  ".$_GET['gid'];
				$this->tpl->assign ( 'gid', $_GET['gid']);
			}
	
			$page = ! empty ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
			$pagesize = DISPLAY_NUM;
			$strat = ($page - 1) * $pagesize;
			$total = $this->db->do_count ( 'equipment a', $where );
			
			$sql2 = "select a.*,b.gname,c.gname as sname  from equipment a left join games b on a.gid=b.id  left join games c on a.sid=c.id " 
			." where  $where order by a.id desc limit $strat,$pagesize";
	
			$docs = $this->db->do_all_bysql($sql2);
			$this->tpl->assign ( 'docs', $docs );
			$this->tpl->assign ( 'pages', numofpage ( $total, $page, ceil ( $total / $pagesize ), $url ) );
			
		}
		$this->tpl->show("equipment");
	}
	
	function equipment_do(){
		$step = isset ( $_POST ['step'] ) ? $_POST ['step'] : '';

		$reurl = "?adm=games&opt=equipment";

		$tbl = "equipment";
		
		$doc = array ( );
		$doc ['ename'] = daddslashes($_POST['ename']);
		$doc ['gid'] = daddslashes($_POST['gid']);
		$doc ['sid'] = daddslashes ( $_POST ['sid'] );
		$doc ['itemid'] = daddslashes($_POST['itemid']);
		$doc ['price'] = $_POST['price'];
		$doc ['place'] = intval($_POST['place']);
		$doc ['summary'] = daddslashes ( $_POST ['summary'] );
		$doc ['description'] = daddslashes ( $_POST ['description'] );

		$doc ['istop'] = (int)$_POST ['istop'];
		
		$imgs = upload_multi ();
		

		if ($step == 'add') {
			if (is_array ( $imgs )) {
				if(!empty($imgs [0])) $doc ['image'] = $imgs [0];
			}
			$this->db->do_insert ( $tbl, $doc );

			
			
			$this->showmsg ( 'game_addok', $reurl );
		} elseif ($step == 'edit') {
			$id = (int)$_POST ['id'];
			
			if (is_array ( $imgs )) {
				if(!empty($imgs [0]) && empty($_POST ['image'])) $doc ['image'] = $imgs [0];
			}
			
			$this->db->do_update ( $tbl, $doc, 'id="' . $id . '"' );
			
			if(count($imgs)){
				foreach($imgs as $img){
					$doc = array("equipment_id" => $id,"imgpath" => $img);
					$this->db->do_insert ( "equipment_img", $doc );
				}
			}
			
			$this->showmsg ( 'game_editok', $reurl );
		}
	}
	
	function getservices() {
		$sid = isset ( $_GET ['sid'] ) ? (int)$_GET ['sid'] : 0;
		$sql = "select id,gname from games where status <>4 and istop=".intval($_GET['id']);
		$games = $this->db->do_all_bysql($sql);
		echo "<select name='sid' id='sid'>";
		foreach ($games as $val) {
			echo "<option value='".$val['id']."'";
			if($sid == $val['id']) echo " selected";
			echo ">".$val['gname']."</option>";
		}
		echo "</select>";
		die();
	}
	
	function price() {
		$sql = "select id,gname from games where status <>4";
		$games = $this->db->do_all_bysql($sql);
		$this->tpl->assign ( 'games', $games );
		$this->tpl->show("price");
	}
	
	function price_do() {
		$reurl = "?adm=games&opt=price";
		$doc = array();
		$doc['price'] = serialize($_POST['price']);
		$doc['dotime'] = serialize($_POST['time']);
		$this->db->do_update ( 'games', $doc, 'id="' . $_POST ['id'] . '"' );
		$this->showmsg ( 'game_editok', $reurl );
	}
	function getprice(){
		$gid = (int)$_GET['id'];
		$sql = "select maxgrade,price,dotime from games where id=$gid";
		$doc = $this->db->do_one_bysql($sql);

		echo "<form id='price' name='pdata' method='post' action='?adm=games&opt=price_do'><table border='1'>";
		echo "<input type='hidden' name='id' value='{$gid}'>";
		echo "<tr><td width='40'>等级</td><td width='120'>价格</td><td  width='120'>时间</td></tr>";
			
		if(empty($doc['price']) || empty($doc['dotime'])){
			for($i=1;$i<=$doc['maxgrade'];$i++){
				echo "<tr>";
				echo "<td>".$i."</td><td><input type='text' size='6' name='price[".$i."]'></td><td><input type='text'  size='6'  name='time[".$i."]'></td>";
				echo "</tr>";
			}
		} else {
			$price = unserialize($doc['price']);
			$time = unserialize($doc['dotime']);
			foreach ($price as $key => $value) {
				echo "<tr>";
				echo "<td>".$key."</td><td><input type='text' size='6' value='".$value."' name='price[".$key."]'></td><td><input type='text'  size='6'  value='".$time[$key]."'  name='time[".$key."]'></td>";
				echo "</tr>";
			}
			
		}
		echo "<tr><td > &nbsp;</td><td ><input type='submit' value='保存' > </td><td >&nbsp;</td></tr>";
		echo "</forM</table>";
	}
	




	

	function del(){
		$id = $_GET['id'];
		$adtype = $_GET ['t'];
		$tbl = dbtbl($adtype);
		$this->db->do_update('house_ad', array('status'=>4), 'id='.$id); //修改狀態
		$this->syslogs("删除广告:".$id."修改状态4",'ads,del');
		dheader ( $_SERVER ['HTTP_REFERER'] );
	}




	function sendmail(){
		$ad_id = $_GET ['ad_id'];
		$t = $_GET ['t'];
		if($t<99){
			$sql = "select a.email,b.ad_no,b.ad_type,b.oid from customers a left join orders c on a.id=c.uid left join orders_house_ad b on c.id=b.oid where b.ad_id=$ad_id";

			$ad = $this->db->do_one_bysql ( $sql );

			$adno = getorderno($ad['ad_no'],6,$ad['ad_type']);
			$adid = $ad_id;
			$orderno = getorderno($ad['oid']);
			if($ad['ad_type']<>6) $showview =1;
			include D_P.'cache/data/email/info.php';
			if($t==1){
				$k = 'onedaynotice';
			} else {
				$k = 'threedaynotice';
			}
			if(!send_mail($email_info[$k]['title'], $email_info[$k]['body'], $ad['email'])){
				//
			}
		}
		//echo $ad['email'];pre($email_info[$k]);
		echo 1;
	}

}

?>