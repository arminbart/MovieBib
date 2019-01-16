<?php

include_once 'lib/session.php';
include_once 'lib/tools.php';
include_once 'lib/videohelpers.php';

$id = get_http_param("id");
$session = get_php_param("s");
$nick = verify_session($session);

$tmpname = $_FILES['image']['tmp_name'];
$filename = get_cover_filename($id);


if ($nick == null)
{
	forward("login.php");
}
else if ($tmpname == "")
{
	forward("edit_cover.php" . session_param($nick, $session, $id) . "&err=no_file");
}
else
{
	move_uploaded_file($tmpname, $filename);

	list($w, $h) = getimagesize($filename);

	$maxw = get_php_param("cover_max_width");
	$maxh = get_php_param("cover_max_height");

	if ($w > $maxw or $h > $maxh)
	{
		$oldimg = imagecreatefromjpeg($filename);

		if ($w > $maxw and $h > $maxh)
		{ // Width and Height too large -> Zoom
			$factorw = $w / $maxw;
			$factorh = $h / $maxh;
			$factor = $factorw < $factorh ? $factorh : $factorw;
			$neww = $w / $factor;
			$newh = $h / $factor;

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

	forward("show_video.php" . session_param($nick, $session, $id) . "&no_cache=1");
}

?>
