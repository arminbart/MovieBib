<?php

include_once 'db/dbstatements.php';
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
			exit("Database pwd starts with '**'. Please provide correct password in db/dbparams.txt!");

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

	// Executes SQL and returns true, if successfull
	function verify_sql($sql)
	{
		debug_out("SQL statement: " . $sql, false);

		if ($this->sqli->query($sql))
			return true;
		else
			return false;
	}

	// Executes SQL without result
	function execute_sql($sql)
	{
		debug_out("SQL statement: " . $sql, false);

		if (!$this->sqli->query($sql))
			exit("Executing SQL statement failed: " . $sql . "<br>Error " . $this->sqli->errno . ": " . $this->sqli->error);
	}

	// Executes SQL statement without result
	function execute(Statement $stmt)
	{
		debug_out("SQL statement: " . $stmt->sql(true), false);

		$stmt->execute($this->sqli)->close();
	}

	// Executes SQL query and returns prepared statement (caller must call get_result() and then close() on the ps).
	function query(Statement $stmt)
	{
		debug_out("SQL query: " . $stmt->sql(true), false);

		return $stmt->execute($this->sqli);
	}

	// Executes SQL query and returns single value
	function value(Statement $stmt)
	{
		$ps = $this->query($stmt);
		$result = $ps->get_result();
		$value = null;

		if ($result !== null)
		{
			$row = $result->fetch_row();
			if ($row !== null)
				$value = $row[0];
			$result->close();
		}

		$ps->close();
		return $value;
	}

	function table_exists($table)
	{
		if ($this->verify_sql("SELECT count(*) FROM " . $table . " WHERE 0"))
			return true;

		debug_out("Table " . $table . " does not exist.");
		return false;
	}

	function column_exists($table, $col)
	{
		if ($this->verify_sql("SELECT " . $col . " FROM " . $table . " LIMIT 1"))
			return true;

		debug_out("Column " . $table . "." . $col . " does not exist.");
		return false;
	}

	function entry_exists($table, $col, $val)
	{
		if ($this->value(new SelectStatement($table, "count(*)", new Where($col, $val))))
			return true;

		debug_out("Entry '" . $val . "' in table " . $table . " not found.");
		return false;
	}
}

?>
