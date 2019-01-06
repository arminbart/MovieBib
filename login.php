<html>
<head>
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
		include 'lib/tools.php';
	?>
</head>
<body>
	<table>
		<tr>
			<td>
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
