<?php

include_once 'lib/tools.php';

class Connection
{
	public $sqli = null; // mysqli Objekt

	function __construct()
	{
		$host = get_php_param("host", "db/dbparams.txt");
		$user = get_php_param("user", "db/dbparams.txt");
		$pwd  = get_php_param("pwd", "db/dbparams.txt");
		$db   = get_php_param("db", "db/dbparams.txt");

		if (starts_with($pwd, "**"))
			exit("Database pwd starts with '**'. Please provide correct password!");

		$this->sqli = new mysqli($host, $user, $pwd, $db);

		if ($this->sqli->connect_errno)
			exit("Failed to connect to MySQL: (" . $this->sqli->connect_errno . ") " . $this->sqli->connect_error);

		debug_out("Connected to MySQL db " . $db . " on " . $host . ".");
	}

	function __destruct()
	{
		// Closing in the descrtutor was told to be a bad idea. But just in case it was forgotten.
		$this->close();
	}

	function close()
	{
		if ($this->sqli !== null)
		{
			$this->sqli->close();
			$this->sqli = null;
			debug_out("Closed MySQL connection.");
		}
	}

	// Executes SQL statement without result
	function execute($stmt)
	{
		debug_out("SQL statement: " . $stmt, false);

		if (!$this->sqli->query($stmt))
			exit("Executing SQL statement failed: " . $stmt . "<br>" . $this->sqli->error);
	}

	// Executes SQL statement and returns true, if successfull
	function verify($stmt)
	{
		debug_out("SQL statement: " . $stmt, false);

		if ($this->sqli->query($stmt))
			return true;
		else
			return false;
	}
	
	// Executes SQL query and returns mysqli_result
	function query($stmt)
	{
		debug_out("SQL query: " . $stmt, false);

		if (!$this->sqli->real_query($stmt))
			exit("Executing SQL query failed: " . $stmt . "<br>" . $this->sqli->error);

		return $this->sqli->use_result();
	}

	// Executes SQL query and returns single value
	function value($stmt)
	{
		$result = $this->query($stmt);
		$value = null;

		if ($result !== null)
		{
			$row = $result->fetch_row();
			if ($row !== null)
				$value = $row[0];
			$result->close();
		}

		return $value;
	}

	function table_exists($table)
	{
		if ($this->verify("SELECT count(*) FROM " . $table . " WHERE 0"))
			return true;

		debug_out("Table " . $table . " does not exist.");
		return false;
	}

	function column_exists($table, $col)
	{
		if ($this->verify("SELECT " . $col . " FROM " . $table . " LIMIT 1"))
			return true;

		debug_out("Column " . $table . "." . $col . " does not exist.");
		return false;
	}

	function entry_exists($table, $col, $val)
	{
		if ($this->value("SELECT count(*) FROM " . $table . " WHERE " . $col . " = '" . $val . "'"))
			return true;

		debug_out("Entry '" . $val . "' in table " . $table . " not found.");
		return false;
	}
}

?>
