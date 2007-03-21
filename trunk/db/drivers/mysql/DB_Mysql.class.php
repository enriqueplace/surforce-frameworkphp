<?php

class DB_Mysql extends DB {

	function __construct() {
		parent::__construct();
	}

	public function affectedRows() {
		$rows = @mysql_affected_rows($this->conn);
		return $rows;
	}
	
	public function insertId() {
		$id = @mysql_insert_id($this->conn);
		return $id;
	}
	
	protected function dbConnect() {
		$conn = @mysql_connect($this->host, $this->user, $this->pwd);
		if($conn) {
			$this->conn = $conn;
			return true;
		}
		return false;
	}
	
	protected function dbSelect() {
		$ret = @mysql_select_db($this->db, $this->conn);
		return $ret;
	}
	
	protected function dbExecute( $sql ) {
		$resource = mysql_query($sql, $this->conn);
		return $resource;
	}
	
	protected function returnDBResult( $sql ) {
		$ret = eregi('^(select|show|describe|explain)', $sql);
		return $ret;
	}
	
	protected function dbError() {
		if($this->conn) {
			$errorMsg = @mysql_error($this->conn);
		} else {
			$errorMsg = @mysql_error();
		}
		return $errorMsg;
	}
	
	protected function dbClose() {
		$ret = @mysql_close($this->conn);
		return $ret;
	}
	
}

?>