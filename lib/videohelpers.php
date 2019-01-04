<?php

function get_cover_filename($id)
{
	$filename = "cover_" . str_pad($id, 5, "0", STR_PAD_LEFT);
	$hash = md5($filename, false);

	return "img/covers/" . $filename . "_" . substr($hash, 0, 5) . ".jpg";
}

?>
