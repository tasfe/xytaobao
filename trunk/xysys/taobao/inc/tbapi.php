<?php

! defined ( 'IN_SITE' ) && die ( 'Forbidden' );

class tbapi extends base {
	
	//$this->conf->taobao_nick;
	//$this->conf->taobao_pid;

	function view() {
		die ();
	}
	
	function cate(){
		
		$tbl = "games";
		
		$req = new ItemcatsGetRequest;
		$req->setFields("cid,parent_cid,name,is_parent,status,sort_order,last_modified");
		$parent_cid = 0;
		
		if(!empty($_GET['pid']) && intval($_GET['pid'])){
			$parent_cid = intval($_GET['pid']);	
		}

		$req->setParentCid($parent_cid);
		
		$resp = $this->tbc->execute($req);
		
		$docs = $resp->item_cats->item_cat;
		
		
		/*if($parent_cid == 0 ){
			foreach($docs as $d){
				$doc = array();
				echo $doc ['gname'] = $d->name;
				$doc ['tb_cid'] = $d->cid;
				$doc ['istop'] = $parent_cid;
				$doc ['place'] = $d->sort_order;
				$this->db->do_insert ( $tbl, $doc );
			}
		}*/
		
		if(!empty($_GET['act']) && $_GET['act'] == "import"){
			
			$cate = $this->db->do_one($tbl, 'id', 'tb_cid='.$parent_cid);
			
			foreach($docs as $d){
				$doc = array();
				echo $doc ['gname'] = $d->name;
				$doc ['tb_cid'] = $d->cid;
				$doc ['istop'] = $cate['id'];
				$doc ['place'] = $d->sort_order;
				$this->db->do_insert ( $tbl, $doc );
			}
			echo "<br>";
			die("导入成功!");
		}
	
		//$taobaoke_res = $resp->taobaoke_items->item_props ;
		
		//print_r($resp);
		
		//$last_modified = $resp->last_modified;
		//$this->tpl->assign ( 'last_modified', $last_modified);
		
		$this->tpl->assign ( 'docs', $docs );
		$this->tpl->show ( 'tbapi_cate' );
	}
	
	function items(){
		$page = ! empty ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
		$pagesize = $this->conf['item_pagesize'];
		$strat = ($page - 1) * $pagesize;
		$url = '?adm=tbapi&opt=items';
		
		$req = new TaobaokeItemsGetRequest;
		$req->setNick($this->conf['taobao_nick']);
		$req->setFields("iid,num_iid,title,nick,pic_url,price,click_url,commission,commission_rate,commission_num,commission_volume,shop_click_url,seller_credit_score,item_location");

		//$req->setArea("武汉");
		//$req->setKeyword("连衣裙");
	
		if(!empty($_GET['cid']) && intval($_GET['cid'])){
			$item_cid = intval($_GET['cid']);	
			$req->setCid($item_cid);
			$url .=  "&cid=".$item_cid;
		}
		
		
		$req->setPageNo($page);
		$req->setPageSize($pagesize);

		$resp = $this->tbc->execute($req);
		
		//print_r($resp);die();
		$total = $resp->total_results;
		$this->tpl->assign ( 'pages', numofpage ( $total, $page, ceil ( $total / $pagesize ), $url.'&' ) );
		$this->tpl->assign ( 'docs', $resp->taobaoke_items->taobaoke_item);
		
		$cate = $this->db->do_one("games", 'id,istop', 'tb_cid='.$item_cid);
		
		$this->tpl->assign ( 'item_sid', $cate['id']);
		$this->tpl->assign ( 'item_gid', $cate['istop']);
		
		$this->tpl->show ( 'tbapi_items' );
	}
	
	function itemrelate(){
		
		
		$item_id = intval($_GET['item_id']);	

		
		$req = new TaobaokeItemsRelateGetRequest; //商品关联推荐 

		$req->setNick($this->conf['taobao_nick']);
		
		$req->setFields("iid,num_iid,title,nick,pic_url,price,click_url,commission,commission_rate,commission_num,commission_volume,shop_click_url,seller_credit_score,item_location");

		$req->setNumIid($item_id);
		
		
		$req->setMaxCount($this->conf['item_relate_count']);
		$req->setRelateType($this->conf['item_relate_type']);
		$req->setSort($this->conf['item_relate_sort']);
		

		$resp = $this->tbc->execute($req);
		
		//print_r($resp);die();
		$total = $resp->total_results;

		$this->tpl->assign ( 'docs', $resp->taobaoke_items->taobaoke_item);
		$this->tpl->show ( 'tbapi_items' );
	}
	
	function itemdetail(){
		
		
		$item_id = intval($_GET['item_id']);	

		
		$req = new TaobaokeItemsDetailGetRequest; //查询淘宝客推广商品详细信息 
		$req->setNick($this->conf['taobao_nick']);
		
		$req->setFields("click_url,shop_click_url,seller_credit_score,num_iid,title,nick,props_name,input_str,pic_url,item_imgs,desc,price,location");
		$req->setNumIids($item_id);
		
		$resp = $this->tbc->execute($req);
		
		print_r($resp);die();


		$this->tpl->assign ( 'docs', $resp->taobaoke_item_details->taobaoke_item_detail);
		$this->tpl->show ( 'tbapi_items' );
	}
	
	
	function itemrate(){
		
		$item_id = intval($_GET['item_id']);	
		
		$page = ! empty ( $_GET ['page'] ) && intval ( $_GET ['page'] ) ? intval ( $_GET ['page'] ) : 1;
		$pagesize = DISPLAY_NUM;
		$strat = ($page - 1) * $pagesize;
		$url = '?adm=tbapi&opt=itemrate&item_id='.$item_id;
		
		die('功能不支持');

		$req = new TraderatesGetRequest; //搜索评价信息 

		$req->setFields("tid,oid,role,rated_nick,nick,result,created,item_title,item_price,content,reply");
		$req->setRateType("get");
		$req->setRole("buyer");
		echo $url;
		$req->setPageNo($page);
		$req->setPageSize($pagesize);
		
		$req->setNumIid($item_id);

		$resp = $this->tbc->execute($req, $this->conf['secret_id']);
		
		print_r($resp);die();
		$total = $resp->total_results;
		$this->tpl->assign ( 'pages', numofpage ( $total, $page, ceil ( $total / $pagesize ), $url.'&' ) );
		$this->tpl->assign ( 'docs', $resp->trade_rates->trade_rate);
		$this->tpl->show ( 'tbapi_itemrate' );
	}
	
	
	function itemimport(){

		$req = new TaobaokeItemsDetailGetRequest;
		$req->setNick($this->conf['taobao_nick']);
		
		$req->setFields("click_url,shop_click_url,seller_credit_score,num_iid,title,nick,input_str,pic_url,desc,price,location");
		//$req->setFields("iid,num_iid,title,nick,pic_url,price,click_url,commission,commission_rate,commission_num,commission_volume,shop_click_url,seller_credit_score,item_location");

		$req->setNumIids(substr($_GET['str'],1));

		$resp = $this->tbc->execute($req);
		
		$total = $resp->total_results;
		
		$docs = $resp->taobaoke_item_details->taobaoke_item_detail;
		
		$gid = $_GET['gid'];
		$sid = $_GET['sid'];
		
		
		foreach($docs as $d){
			  $doc = array();
			  $doc ['ename'] = $d->item->title;
			  $doc ['tb_cid'] = $d->item->cid;
			  $doc ['price'] = $d->item->price;
			  $doc ['image'] = $d->item->pic_url;
			  
			  
			  $doc ['description'] = $d->item->desc;
			  //$doc ['summary'] = $d->item->props_name;
			  $doc ['click_url'] = $d->click_url;
			  $doc ['shop_click_url'] = $d->shop_click_url;
			  $doc ['seller_credit_score'] = $d->seller_credit_score;
			  $doc ['nick'] = $d->item->nick;
			 
			  $doc ['item_state'] = $d->item->location->state;
			  $doc ['item_city'] = $d->item->location->city;
			  
			  $doc ['gid'] = $gid;
			  $doc ['sid'] = $sid;
			  
			  
			  $inum = $this->db->do_count("equipment", "tb_iid=".$d->item->num_iid);
			  if($inum > 0){
				  $this->db->do_update ( "equipment", $doc, "tb_iid=".$d->item->num_iid );
			  } else {
				  $doc ['tb_iid'] = $d->item->num_iid;
				  $this->db->do_insert ( "equipment", $doc );
			  }
		}
		
		echo 1;
	}
	
	
}

?>