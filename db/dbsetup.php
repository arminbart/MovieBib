<?php

include 'db/dbconnect.php';

function init_db()
{
	$maxretries = 1000;
	$file = fopen("db/dbstructure.sql", "r");
	$con = new Connection();

	while ($maxretries > 0 and !feof($file))
	{
		$maxretries = $maxretries - 1;
		$line = trim(fgets($file));

		if (starts_with($line, "CREATE TABLE"))
			check_table($con, $file, $line, $maxretries);
	}

	if ($maxretries <= 0)
		exit("Reading DB structure reached max retries.");

	$con->close();
}

function check_table($con, $file, $header, $maxretries)
{
	$values = explode(" ", $header);

	if (!ends_with($header, "(") or count($values) != 4)
		exit("Invalid CREATE TABLE syntax: " . $header);

	$table = $values[2];

	if (!table_exists($con, $table))
		create_table($con, $file, $table, $maxretries);
	else
		update_table($con, $file, $table, $maxretries);
}

function table_exists($con, $table)
{
	if ($con->verify("SELECT count(*) FROM " . $table . " WHERE 0"))
		return true;

	debug_out("Table " . $table . " does not exist.");
	return false;
}

function column_exists($con, $table, $col)
{
	if ($con->verify("SELECT " . $col . " FROM " . $table . " LIMIT 1"))
		return true;

	debug_out("Column " . $table . "." . $col . " does not exist.");
	return false;
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
			db_execute($con, $stmt);
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
		
		if (!column_exists($con, $table, $col))
		{
			db_execute($con, "ALTER TABLE " . $table . " ADD COLUMN " . $line);
			debug_out("Added column " . $col . " to table " . $table . ".");
		}
	}
}

?>
