<html>
<head>
	<title>MovieBib - Cover Upload</title>

	<link rel="stylesheet" href="styles.css">

	<script>
		function validateForm()
		{
			var image = document.getElementById("image").value;

			if (image == '')
				return false;
			else
				return true;
 		}
	</script>

	<?php
		include_once 'lib/session.php';
		include_once 'lib/videohelpers.php';

		$id = get_http_param("id");
		$session = get_php_param("s");
		$nick = verify_session($session);

		$coverfile = get_cover_filename($id, true, $nick);
	?>
</head>
<body>
	<table style="text-align: center;">
		<?php if ($nick == null) { ?>
		<tr>
			<td>
				<a href="show_video.php?id=<?php echo $id; ?>">&lt;</a><br>
				<a href="login.php?from=edit_cover;id;<?php echo $id; ?>">Einloggen zum Hochladen eines Bildes.</a>
			</td>
		</tr>
		<?php } else { ?>
		<tr>
			<td>
				<a href="show_video.php<?php echo session_param($nick, $session, $id); ?>">&lt;</a><br>
				<img src="<?php echo $coverfile; ?>">
			</td>
		</tr>
		<tr>
			<td id="spacer_medium"><?php echo get_cover_info($coverfile); ?></td>
		</tr>
		<tr>
			<td>
				<form method="post" action="save_cover.php<?php echo session_param($nick, $session, $id); ?>" enctype="multipart/form-data">
					<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo get_php_param('cover_max_filesize'); ?>" />
					<input type="file" accept="image/jpeg" name="image" id="image" />
					<input type="submit" value='Speichern' onclick="return validateForm()"/>
        		</form>
			</td>
		</tr>
		<?php } ?>
		<?php if (get_http_param("err") == "no_file") { ?>
		<tr>
			<td id="spacer_medium"></td>
		</tr>
		<tr>
			<td>
				Keine JPEG-Datei ausgew&auml;hlt oder maximale Gr&ouml;&szlig;e von <?php echo get_php_param('cover_max_filesize') / 1000; ?> KB &uuml;berschritten.
			</td>
		</tr>
		<?php } ?>
	</table>
</body>
</html>
