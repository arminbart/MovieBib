<?php

include_once 'lib/tools.php';
include_once 'lib/session.php';
include_once 'lib/videohelpers.php';


$from = get_http_param("from");
$session = Session::login();

if ($from == "")
	$from = "index";

if ($session->valid())
{
	forward(get_forward_page($from, $session));
}
else
{
	forward("login.php?from=" . $from);
}

?>
