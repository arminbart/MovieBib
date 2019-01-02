<?php

include 'lib/tools.php';

class Connection
{
	public $sqli = null; // mysqli Objekt

	function __construct()
	{
		$host   = $this->get_php_param("host");
		$user   = $this->get_php_param("user");
		$pwd    = $this->get_php_param("pwd");
		$db     = $this->get_php_param("db");

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

	function get_php_param($name)
	{
		$maxretries = 100;
		$value = trim($_GET[$name]);

		if ($value == "")
		{
			$file = fopen("db/dbparams.txt", "r");

			while ($value == "" and $maxretries > 0 and !feof($file))
			{
				$maxretries = $maxretries - 1;
				$line = fgets($file);

				$values = explode("=", $line);
				if (trim($values[0]) === $name)
					$value = trim($values[1]);
			}

			fclose($file);
		}

		return $value;
	}

	// Executes SQL statement without result
	function execute($stmt)
	{
		debug_out("SQL statement: " . $stmt);

		if (!$this->sqli->query($stmt))
			exit("Executing SQL statement failed: " . $stmt);
	}

	// Executes SQL statement and returns true, if successfull
	function verify($stmt)
	{
		debug_out("SQL statement: " . $stmt);

		if ($this->sqli->query($stmt))
			return true;
		else
			return false;
	}
	
	// Executes SQL query and returns mysqli_result
	function query($stmt)
	{
		debug_out("SQL query: " . $stmt);

		if (!$this->sqli->real_query($stmt))
			exit("Executing SQL query failed: " . $stmt);

		return $this->sqli->use_result();
	}
}

?>
