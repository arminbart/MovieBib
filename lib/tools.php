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

?>
