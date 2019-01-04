<?php

function debug()
{
	return get_php_param("debug") == "true";
}

function debug_out($msg, $important = true)
{
	if (!debug())
		return;

	if ($important)
		echo "<p style='color: #606060; font-size: 14'>";
	else
		echo "<p style='color: #A0A0A0; font-size: 10'>";

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

function strpos_regex($string, $regex)
{
	if (preg_match($regex, $string, $matches))
		return strpos($string, $matches[0]);
	else
		return false;
}

$PHP_PARAMS = array();

function get_php_param($name, $paramfile = "lib/params.txt")
{
	global $PHP_PARAMS;

	$maxretries = 100;
	$value = get_http_param($name);

	if ($value == "")
		$value = $PHP_PARAMS[$paramfile . "#" . $name];

	if ($value == "")
	{
		$file = fopen($paramfile, "r");
		if (!$file)
			exit("Failed to open " . $paramfile .".");

		while ($value == "" and $maxretries > 0 and !feof($file))
		{
			$maxretries = $maxretries - 1;
			$line = fgets($file);

			$values = explode("=", $line);
			if (trim($values[0]) === $name)
				$value = trim($values[1]);
		}

		$PHP_PARAMS[$paramfile . "#" . $name] = $value;
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
