<?php

function get_cover_filename($id)
{
	$filename = "cover_" . str_pad($id, 5, "0", STR_PAD_LEFT);
	$hash = md5($filename, false);

	return "img/covers/" . $filename . "_" . substr($hash, 0, 5) . ".jpg";
}

function get_forward_header($from, $session = 0)
{
	$params = explode(";", $from);
	$forward = $params[0] . ".php?session=" . $session;

	for ($i = 1; count($params) > $i + 1; $i += 2)
		$forward .= "&" . $params[$i] . "=" . $params[$i + 1];

	return $forward;
}

?>
