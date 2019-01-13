<?php

function get_cover_filename($id, $default_if_missing_or_not_logged_in = false, $nick = null)
{
	$filename = "cover_" . str_pad($id, 5, "0", STR_PAD_LEFT);
	$hash = md5($filename, false);
	$filename = "img/covers/" . $filename . "_" . substr($hash, 0, 5) . ".jpg";

	if (!$default_if_missing_or_not_logged_in)
		return $filename;

	if (!file_exists($filename) or $nick == null)
		$filename = "img/cover.png";

	return $filename;
}

function get_cover_info($filename)
{
	if ($filename == "" or !file_exists($filename))
		return "";

	list($w, $h) = getimagesize($filename);

	return $w . " x " . $h;
}

function get_forward_page($from, $session = 0)
{
	$params = explode(";", $from);
	$forward = $params[0] . ".php?s=" . $session;

	for ($i = 1; count($params) > $i + 1; $i += 2)
		$forward .= "&" . $params[$i] . "=" . $params[$i + 1];

	return $forward;
}

function get_checked($val, $cond)
{
	if ($cond == "bool" and boolval($val))
		return "checked";
	if (strtolower($val) == strtolower($cond))
		return "checked";
	else
		return "";
}

?>
