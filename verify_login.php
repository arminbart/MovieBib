<?php

include_once 'lib/session.php';
include_once 'lib/videohelpers.php';

$from = get_http_param("from");
$nick = get_http_param("nick");
$pass = get_http_param("pass");
$session = get_session($nick, $pass);

if ($from == "")
	$from = "index";

if ($session != "")
{
	$params = explode(";", $from);
	$forward = $params[0] . ".php?session=" . $session;

	for ($i = 1; count($params) > $i + 1; $i += 2)
		$forward .= "&" . $params[$i] . "=" . $params[$i + 1];

	header("Location: http://video.bartmail.de/" . get_forward_page($from, $session));
}
else
{
	header("Location: http://video.bartmail.de/login.php?from=" . $from);
}
exit();

?>
