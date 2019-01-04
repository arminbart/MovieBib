<html>
<head>
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
		include 'lib/tools.php';
		include 'lib/videohelpers.php';

		$id = get_http_param("id");

		$coverfile = get_cover_filename($id);
		if (!file_exists($coverfile))
			$coverfile = "img/cover.png";
	?>
	<style>
		body {
			background-color: black;
			font-family: helvetica;	
			color: #666666;
			font-size: 14;
			vertical-align: text-top;
		}
		table {
			margin-left:auto;
			margin-right:auto;
			border-collapse: collapse;
		}
		table, th, td {
			text-align: center;
			/*border: 1px solid white;*/
		}
		#spacer_small {
			height: 5px;
		}
		#spacer_medium {
			height: 20px;
		}
		#spacer_large {
			height: 50px;
		}
	</style>
</head>
<body>
	<table>
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
