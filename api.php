<?php

include 'api/add_video.php';
include_once 'db/dbconnect.php';

$con = new Connection();

try
{
	$result = add_video($con);
}
catch (ApiException $e)
{
	$result = ($e->isWarning() ? "Warning" : "Error") . ": " . $e->getMessage();
}

if ($con != null)
	$con->close();

echo $result;

?>
