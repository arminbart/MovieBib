<html>
<head>
	<title>MovieBib - Login</title>

	<link rel="stylesheet" href="styles.css">

	<script>
		function validateForm()
		{
			var nick = document.getElementById("nick").value;
			var pass = document.getElementById("pass").value;

			if (nick == '' || nick == 'Name' || pass == '')
				return false;
			else
				return true;
 		}
	</script>

	<?php
		include_once 'lib/tools.php';
		include_once 'lib/videohelpers.php';
	?>

	<style>
		table, th, td {
			<?php if (debug()) { echo "border: 1px solid white;"; } ?>
		}
	</style>
</head>
<body>
	<table>
		<tr>
			<td>
				<a href="<?php echo get_forward_page(get_http_param("from")); ?>">&lt;</a><br>
				<form method="post" action="verify_login.php?from=<?php echo get_http_param("from"); ?>" enctype="multipart/form-data">
					<input type="text" name="nick" size="30" value="Name"/><br>
					<input type="password" name="pass" size="30"/><br>
					<input type="submit" value='Login' onclick="return validateForm()"/>
				</form>
			</td>
		</tr>
	</table>
</body>
</html>
