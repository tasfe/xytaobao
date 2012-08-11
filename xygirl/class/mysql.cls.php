<?php

class db_mysql {
	var $querynum = 0;
	var $con;

	function connect($dbhost, $dbuser, $dbpw, $dbname, $pconnect = 0) {
		if($pconnect) {
			if(!@mysql_pconnect($dbhost, $dbuser, $dbpw)) {
				$this->halt('Can not connect to MySQL server');
			}
		} else {
			if(!@mysql_connect($dbhost, $dbuser, $dbpw)) {
				$this->halt('Can not connect to MySQL server');
			}
		}
		mysql_query("SET NAMES 'UTF8'");
		mysql_select_db($dbname);
	}

	function fetch_array($result_type = MYSQL_ASSOC) {
		return mysql_fetch_array($this->con, $result_type);
	}

	function query($sql, $silence = 0)
	{
		$this->con = mysql_query($sql);
		if(!$this->con && !$silence)
		{
			$this->halt('MySQL Query Error', $sql);
		}
		$this->querynum++;
	}

	function affected_rows() {
		return mysql_affected_rows();
	}

	function error() {
		return mysql_error();
	}

	function errno() {
		return mysql_errno();
	}

	function result($row) {
		$query = @mysql_result($this->con, $row);
		return $query;
	}

	function num_rows() {
		return mysql_num_rows($this->con);
	}

	function num_fields() {
		return mysql_num_fields($this->con);
	}

	function free_result() {
		return mysql_free_result($this->con);
	}

	function insert_id() {
		return mysql_insert_id();
	}

	function fetch_row() {
		return mysql_fetch_row($this->con);
	}

	function close() {
		return mysql_close();
	}

	function halt($message = '', $sql = '') {
		$timestamp = time();
		$errmsg = '';

		$dberror = $this->error();
		$dberrno = $this->errno();

		if($message) {
			$errmsg = "<b>Error Info</b>: $message\n\n";
		}

		$errmsg .= "<b>Time</b>: ".date("Y-m-d H:i:s", $timestamp)."\n";
		$errmsg .= "<b>Script</b>: ".$_SERVER['PHP_SELF']."\n\n";
		if($sql) {
			$errmsg .= "<b>SQL</b>: ".htmlspecialchars($sql)."\n";
		}
		$errmsg .= "<b>Error</b>:  $dberror\n";
		$errmsg .= "<b>Errno.</b>:  $dberrno";

		echo "<p style=\"font-family: Verdana, Tahoma; font-size: 11px; background: #FFFFFF;\">";
		echo nl2br($errmsg);
		echo '</p>';
		exit;
	}

	function insertStr($data) {
		$field_names  = "";
		$field_values = "";
		foreach ($data as $k => $v)
		{
			$field_names  .= "$k,";
			if ( is_numeric($v) && $v === intval($v))
			{
				$field_values .= $v.",";
			}
			else
			{
				$field_values .= "'$v',";
			}
		}
		$field_names  = preg_replace( "/,$/" , "" , $field_names  );
		$field_values = preg_replace( "/,$/" , "" , $field_values );
		return array( 'FIELD_NAMES'  => $field_names,'FIELD_VALUES' => $field_values);
	}

	function updateStr($data) {
		$return_string = "";
		foreach ($data as $k => $v)
		{
			if ( is_numeric($v) && intval($v) === $v )
			{
				$return_string .= $k . "=".$v.",";
			}
			else
			{
				$return_string .= $k . "='".$v."',";
			}
		}
		$return_string = preg_replace("/,$/" , "" , $return_string);
		return $return_string;
	}

	function do_insert($tbl, $arr)
	{
		$dba = $this->insertStr($arr);
		$sql = "INSERT INTO $tbl ({$dba['FIELD_NAMES']}) VALUES({$dba['FIELD_VALUES']})";
		$this->query($sql);
		$rid = $this->insert_id();
		//writelog($sql);
	}

	function do_update($tbl, $arr, $where="")
	{
		$dbstr = $this->updateStr($arr);
		$sql = "UPDATE $tbl SET $dbstr";
		if ( $where )
		{
			$sql .= " WHERE ".$where;
		}
		$this->query($sql);
		//writelog($sql);
	}

	function do_select($tbl, $get="*", $where="", $ord="", $lit="")
	{
		$sql = "SELECT $get FROM  $tbl";
		if ( $where != "" ) $sql .= " WHERE ".$where;
		if ( $ord != "" ) $sql .= " ORDER BY ".$ord;
		if ( $lit != "" ) $sql .= " LIMIT ".$lit;
		$this->query($sql);
	}


	function do_one($tbl, $field='*', $where='1 = 1',$query=true)
	{
		$sql = "select $field from $tbl where $where";
		if ($query == false) {
		$result = mysql_query($sql);
		return mysql_fetch_array($result);
		}
		$this->query($sql);
		return $this->fetch_array();
	}

	function do_one_bysql($sql)
	{
		$this->query($sql);
		return $this->fetch_array();
	}

	function do_all($tbl, $get="*", $where="", $ord="", $lit="")
	{
		$this->do_select($tbl, $get, $where, $ord, $lit);
		$docs = array();
		while ($row = $this->fetch_array()) {
			$docs[] = $row;
		}
		return $docs;
	}

	function do_all_bysql($sql,$filed='')
	{
		$this->query($sql);
		$docs = array();
		if($filed) $docs['total']=0;
		while ($row = $this->fetch_array()) {
			if($filed){
				$docs['total'] += $row[$filed];
				$docs['sub'][] = $row;
			} else {
				$docs[] = $row;
			}
		}
		return $docs;
	}


	function do_count($tbl,$where = ' 1 = 1 ')
	{
		$sql = "select count(*) from $tbl where $where";
		$this->query($sql);
		return mysql_result($this->con,0);
	}

	function do_count_bysql($sql)
	{
		$this->query($sql);
		return mysql_result($this->con,0);
	}

}
?>