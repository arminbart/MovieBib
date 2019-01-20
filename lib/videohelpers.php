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

function process_cover($id, $tmpname)
{
	$filename = get_cover_filename($id);

	move_uploaded_file($tmpname, $filename);

	list($w, $h) = getimagesize($filename);

	$maxw = get_php_param("cover_max_width");
	$maxh = get_php_param("cover_max_height");

	if ($w > $maxw or $h > $maxh)
	{
		$oldimg = imagecreatefromjpeg($filename);
		$newimg = $oldimg;

		if ($w > $maxw and $h > $maxh)
		{ // Width and Height too large -> Zoom
			$factorw = $w / $maxw;
			$factorh = $h / $maxh;
			$factor = $factorw < $factorh ? $factorh : $factorw;
			$neww = intval($w / $factor + 0.01);
			$newh = intval($h / $factor + 0.01);

			$newimg = imagecreatetruecolor($neww, $newh);
			imagecopyresampled($newimg, $oldimg, 0, 0, 0, 0, $neww, $newh, $w, $h);
			imagedestroy($oldimg);
			$oldimg = $newimg;

			$w = $neww;
			$h = $newh;
		}
/*
		if ($w > $maxw or $h > $maxh)
		{ // Width or Height (still) too large -> Crop
			$offsetx = $neww > $maxw ? ($neww - $maxw) / 2 : 0;
			$offsety = $newh > $maxh ? ($newh - $maxh) / 2 : 0;
			$newimg = imagecrop($oldimg, array('x' => $offsetx, 'y' => $offsety, 'width' => min($w, $maxw), 'height' => min($h, $maxh)));
			imagedestroy($oldimg);

			echo "<br>offset " . $offsetx . " x " . $offsety;
			echo "<br>range " . min($neww, $maxw) . " x " . min($newh, $maxh);
		}
*/
		imageconvolution($newimg, array(array(-1, -1, -1), array(-1, 16, -1), array(-1, -1, -1)), 8, 0); // Sharpen image
		imagejpeg($newimg, $filename);

		imagedestroy($newimg);
	}
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
