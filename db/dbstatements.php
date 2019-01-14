<?php

class Statement
{
	protected $table;
	protected $params = array();
	protected $where = null;

	public function __construct($table)
	{
		$this->table = $table;
	}

	public function add_value($col, $val, $direct = false)
	{
		$this->params[] = new DBParam($col, $val, $direct);
	}

	public function set_where(Where $where)
	{
		$this->where = $where;
	}

	public function execute($sqli)
	{
		if (!($ps = $sqli->prepare($this->sql())))
			exit("Executing SQL statement failed: " . $this->sql(true) . "<br>Error " . $sqli->errno . ": " . $sqli->error);

		if (!$this->bind($ps))
			exit("Binding parameters to SQL statement failed: " . $this->sql(true) . "<br>Prepared statement: " . $this->sql(false));

		if (!$ps->execute())
			exit("Executing SQL statement failed: " . $this->sql(true) . "<br>Error " . $ps->errno . ": " . $ps->error);

		return $ps;
	}

	public function bind($ps)
	{
		return bind_values($ps, $this->params) and ($this->where == null or $this->where->bind($ps));
	}

	public function sql($debug = false)
	{
		exit("Statement classes must override funciton sql().");
	}
}

class InsertStatement extends Statement
{
	public function sql($debug = false)
	{
		$sql = "INSERT INTO $this->table (";
		$valstr = "";

		foreach ($this->params as $param)
		{
			if ($valstr != "")
			{
				$sql .= ", ";
				$valstr .= ", ";
			}
			$sql .= $param->col;
			$valstr .= $param->sql($debug);
		}
		$sql .= ") VALUES ($valstr)";

		return $sql;
	}
}

class UpdateStatement extends Statement
{
	public function sql($debug = false)
	{
		$sql = "UPDATE $this->table SET ";
		$valstr = "";

		foreach ($this->params as $param)
		{
			if ($valstr != "")
				$valstr .= ", ";
			$valstr .= $param->col . " = " . $param->sql($debug);
		}

		$sql .= $valstr;
		if ($this->where != null)
			$sql .= " WHERE " . $this->where->sql($debug);

		return $sql;
	}
}

class SelectStatement extends Statement
{
	private $cols;
	private $order;

	public function __construct($table, $cols = "*", $where = null, $order = null)
	{
		parent::__construct($table);
		if ($where != null)
			$this->set_where($where instanceof Where ? $where : Where::from_sql($where));
		$this->cols = $cols;
		$this->order = $order;
	}

	public function sql($debug = false)
	{
		$sql = "SELECT $this->cols FROM $this->table";

		if ($this->where != null)
			$sql .= " WHERE " . $this->where->sql($debug);

		if ($this->order != "")
			$sql .= " ORDER BY " . $this->order;

		return $sql;
	}
}

class Where
{
	private $operator;
	private $params = array();
	private $wheres = array();

	public function __construct($col = null, $val = null, $operator = "AND", $col2 = null, $val2 = null)
	{
		if ($col != null)
			$this->add_value($col, $val);
		$this->operator = $operator;
		if ($col2 != null)
			$this->add_value($col2, $val2);
	}

	public static function from_sql($sql)
	{
		$where = new Where();
		$where->sub_where($sql);

		return $where;
	}

	public function add_value($col, $val)
	{
		$this->params[] = new DBParam($col, $val);
	}

	public function sub_where($where)
	{
		$this->wheres[] = $where;
	}

	public function sql($debug)
	{
		$sql = "";

		foreach ($this->params as $param)
		{
			if ($sql != "")
				$sql .= " $this->operator ";
			$sql .= $param->col . " = " . $param->sql($debug);
		}

		foreach ($this->wheres as $where)
		{
			if ($sql != "")
				$sql .= " $this->operator ";
			$sql .= "(" . ($where instanceof Where ? $where->sql($debug) : $where) . ")";
		}

		return $sql;
	}

	public function bind($ps)
	{
		if (!bind_values($ps, $this->params))
			return false;

		foreach ($this->wheres as $where)
			if ($where instanceof Where)
				if (!$where->bind($ps))
					return false;

		return true;
	}
}

class DBParam
{
	public $col;
	public $val;
	public $direct;

	public function __construct($col, $val, $direct = false)
	{
		$this->col = $col;
		$this->val = $val;
		$this->direct = $direct;
	}

	public function sql($debug)
	{
		if ($this->direct)
			return $this->val;
		else if (!$debug and false) // Armin fix me: false entfernen, um prepared statements zu nutzen
			return "?";
		if (gettype($this->val) == "boolean")
			return $this->val ? 1 : 0;
		else if (gettype($this->val) == "integer")
			return $this->val;
		else
			return "'" . str_replace("'", "''", $this->val) . "'";
	}
}


function bind_values($ps, $params)
{
/*	foreach (array_keys($vals) as $key)
	{
		$val = $vals[$key];
		debug_out("Binding parameter (" . (in_array(gettype($val), array("integer", "boolean")) ? "i" : "s") . ") $val");
		if (!$ps->bind_param(in_array(gettype($val), array("integer", "boolean")) ? "i" : "s", $val))
		{
			debug_out("Cannot bind parameter (" . (in_array(gettype($val), array("integer", "boolean")) ? "i" : "s") . ") $val.");
			return false;
		}
	}
*/
	return true;
}

?>
