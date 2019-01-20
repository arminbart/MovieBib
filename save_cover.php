<?php

include_once 'lib/session.php';
include_once 'lib/tools.php';
include_once 'lib/videohelpers.php';

$id = get_http_param("id");
$session = Session::get();

$tmpname = $_FILES['image']['tmp_name'];


if (!$session->valid())
{
	forward("login.php");
}
else if ($tmpname == "")
{
	forward("edit_cover.php" . $session->param($id) . "&err=no_file");
}
else
{
	process_cover($id, $tmpname);
	forward("show_video.php" . $session->param($id) . "&no_cache=1");
}

?>
