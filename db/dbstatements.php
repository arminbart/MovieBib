<?php

class InsertStatement
{
	private $table;
	private $cols = "";
	private $vals = "";

	function __construct($table)
	{
		$this->table = $table;
	}

	function addValue($col, $val)
	{
		if ($this->cols != "")
			$this->cols .= ", ";
		$this->cols .= $col;
		
		if ($this->vals != "")
			$this->vals .= ", ";
		$this->vals .= "'" . $val . "'";
	}

	function stmt()
	{
		return "INSERT INTO " . $this->table . " (" . $this->cols . ") VALUES (" . $this->vals . ")";
	}
}

?>
