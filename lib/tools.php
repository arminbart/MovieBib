<?php

$DEBUG = true;

function debug_out($msg, $important = true)
{
	if ($important)
		echo "<p style='color: #606060; font-size: 14'>";
	else
		echo "<p style='color: #A0A0A0; font-size: 10'>";

    if ($GLOBALS['DEBUG'] === true)
        echo $msg . "</p>";
}

function starts_with($haystack, $needle)
{
    return (substr($haystack, 0, strlen($needle)) === $needle);
}

function ends_with($haystack, $needle)
{
    return (substr($haystack, -strlen($needle)) === $needle);
}

function get_php_param($paramfile, $name)
{
	$maxretries = 100;
	$value = get_http_param($name);

	if ($value == "")
	{
		$file = fopen($paramfile, "r");

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

function get_http_param($name)
{
	$value = trim($_GET[$name]);

	if ($value == "")
		$value = trim($_POST[$name]);

	return $value;
}

?>
