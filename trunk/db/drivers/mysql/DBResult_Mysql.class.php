<?php

class DBResult_Mysql extends DBResult {

	function __construct() {
		parent::__construct();
	}

	public function numRows() {
		$numRows = mysql_num_rows($this->res);
		return $numRows;
	}
	
	public function freeResult() {
		$ret = mysql_free_result($this->res);
		return $ret;
	}
	
	protected function fetchAssoc() {
		return mysql_fetch_assoc($this->res);
	}
	
	protected function fetchObject() {
		return mysql_fetch_object($this->res);
	}
	
}

?>