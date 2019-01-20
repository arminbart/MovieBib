<?php

include_once 'lib/session.php';
include_once 'lib/tools.php';
include_once 'lib/videohelpers.php';

$id = get_http_param("id");
$session = get_php_param("s");
$nick = verify_session($session);

$tmpname = $_FILES['image']['tmp_name'];


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
	process_cover($id, $tmpname);
	forward("show_video.php" . session_param($nick, $session, $id) . "&no_cache=1");
}

?>
