<html>
<head>
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
		include 'lib/session.php';
		include 'lib/videohelpers.php';

		$id = get_http_param("id");

		$coverfile = get_cover_filename($id);
		if (!file_exists($coverfile))
			$coverfile = "img/cover.png";
	?>
</head>
<body>
	<table>
		<?php if (verify_session(get_php_param("session")) == null) { ?>
		<tr>
			<td>
				<a href="login.php?from=edit_cover;id;<?php echo $id; ?>">Einloggen zum Hochladen eines Bildes.</a>
			</td>
		</tr>
		<?php } else { ?>
		<tr>
			<td>
				<img src="<?php echo $coverfile; ?>">
			</td>
		</tr>
		<tr>
			<td id="spacer_medium"></td>
		</tr>
		<tr>
			<td>
				<form method="post" action="save_cover.php" enctype="multipart/form-data">
					<input type="hidden" name="id" value="<?php echo $id; ?>" />
					<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo get_php_param('cover_max_filesize'); ?>" />
					<input type="file" accept="image/jpeg" name="image" id="image" />
					<input type="submit" value='Save' onclick="return validateForm()"/>
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
