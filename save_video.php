<?php

include_once 'lib/session.php';
include_once 'lib/tools.php';
include_once 'lib/video.php';
include_once 'lib/videohelpers.php';

$id = get_http_param("id");
$session = Session::get();

$tmpname = $_FILES['image']['tmp_name'];

if (!$session->valid())
{
	forward("login.php");
}
else
{
	$video = new Video($id);

	$video->rating = intval(get_http_param("rating"));
	$video->title = get_http_param("title");
	$video->origtitle = get_http_param("origtitle");
	$video->othertitles = get_concat_param("othertitle", 3);
	$video->predecessor = get_http_param("predecessor");
	$video->successor = get_http_param("successor");
	$video->country = get_http_param("country");
	$video->genre = get_http_param("genre");
	$video->othergenres = get_concat_param("othergenre", 18);
	$video->lang = get_http_param("lang");
	$video->year = intval(get_http_param("year")) > 0 ? intval(get_http_param("year")) : null;
	$video->duration = intval(get_http_param("duration")) > 0 ? intval(get_http_param("duration")) : null;
	$video->medium = get_medium(get_http_param("medium"), get_http_param("file"));
	$video->resolution = strtoupper(get_http_param("resolution"));
	$video->cut = boolval(get_http_param("cut"));
	$video->file = get_http_param("file");
	$video->origfile = $video->origfile == "" ? $video->file : $video->origfile;
	$video->location = get_http_param("location");
	$video->info = get_http_param("info");
	$video->link = get_http_param("link");
	$video->trailer = get_http_param("trailer");
	$video->director = get_http_param("director");
	$video->actors = get_concat_param("actor", 21);

	if (intval($video->status) == Video::enlisted)
		$video->status = Video::edited;

	if (debug())
		$video->debug_dump();
	$video->save();

	if ($tmpname != "")
		process_cover($id, $tmpname);

	forward("show_video.php" . $session->param($id) . "&no_cache=1");
}


function get_medium($type, $filename)
{
	if ($type == "auto")
	{
		$types = explode(",", get_php_param("types"));
		$pos = strrpos($filename, ".");

		if ($pos > 0)
		{
			$type = strtoupper(substr($filename, $pos + 1));
			if (in_array($type, $types))
				return $type;
		}

		return null; // Empty is also a valid Medium (and means, we don't have the video physically)
	}
	else
	{
		return strtoupper($type);
	}
}

?>
