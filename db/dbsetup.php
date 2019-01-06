<?php

include_once 'db/dbconnect.php';

function init_db()
{
	$con = new Connection();

	init_tables($con);
	init_data($con);

	$con->close();
}

function init_tables($con)
{
	$maxretries = 1000;
	$file = fopen("db/dbstructure.sql", "r");

	while ($maxretries > 0 and !feof($file))
	{
		$maxretries = $maxretries - 1;
		$line = trim(fgets($file));

		if (starts_with($line, "CREATE TABLE"))
			check_table($con, $file, $line, $maxretries);
	}

	if ($maxretries <= 0)
		exit("Reading DB structure reached max retries.");
}

function check_table($con, $file, $header, $maxretries)
{
	$values = explode(" ", $header);

	if (!ends_with($header, "(") or count($values) != 4)
		exit("Invalid CREATE TABLE syntax: " . $header);

	$table = $values[2];

	if (!$con->table_exists($table))
		create_table($con, $file, $table, $maxretries);
	else
		update_table($con, $file, $table, $maxretries);
}

function create_table($con, $file, $table, $maxretries)
{
	$stmt = "CREATE TABLE " . $table . " (";

	while ($maxretries > 0 and !feof($file))
	{
		$maxretries = $maxretries - 1;
		$line = trim(fgets($file));

		if ($line == "")
			continue;

		$stmt .= trim($line) . " ";
		if ($line == ")")
		{
			$con->execute($stmt);
			debug_out("Table " . $table . " created.");
			break;
		}
	}
}

function update_table($con, $file, $table, $maxretries)
{
	debug_out("Check table " . $table . "...");	

	while ($maxretries > 0 and !feof($file))
	{
		$maxretries = $maxretries - 1;
		$line = trim(fgets($file));

		if ($line == "")
			continue;
		if ($line == ")")
			break;

		if (ends_with($line, ","))
			$line = substr($line, 0, strlen($line) - 1);

		$col = explode(" ", $line)[0];

		if (!$con->column_exists($table, $col))
		{
			$con->execute("ALTER TABLE " . $table . " ADD COLUMN " . $line);
			debug_out("Added column " . $col . " to table " . $table . ".");
		}
	}
}

function init_data($con)
{
	$maxretries = 1000;
	$file = fopen("db/dbentries.sql", "r");

	debug_out("Check database default entries...");

	while ($maxretries > 0 and !feof($file))
	{
		$maxretries = $maxretries - 1;
		$line = trim(fgets($file));

		if (starts_with($line, "INSERT INTO"))
			check_entry($con, $line);
	}

	if ($maxretries <= 0)
		exit("Reading DB default entries reached max retries.");
}

function check_entry($con, $line)
{
	if (!preg_match("/INSERT INTO [a-zA-Z]+ \([a-zA-Z]+(, ?[a-zA-Z]+)*\) VALUES \('[a-zA-Z\-]+'(, ?'[a-zA-Z\-]+')*\)/", $line))
		exit("Invalid INSERT syntax: " . $line);

	$values = explode("VALUES", substr($line, strlen("INSERT INTO") + 1));
	$table = trim(substr($values[0], 0, strpos($values[0], " ")));
	$col = trim(explode(",", trim(substr($values[0], strlen($table) + 1), " ()"))[0]);
	$val = trim(explode(",", trim($values[1], " ()"))[0], " '");

	if (!$con->entry_exists($table, $col, $val))
	{
		$con->execute($line);
		debug_out("Inserted value '" . $val. "' in table " . $table . ".");
	}
}

?>
