<?php

$DEBUG = true;

function debug_out($msg)
{
    if ($GLOBALS['DEBUG'] === true)
        echo $msg . "<br><br>";
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
