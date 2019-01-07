<html>
<head>
	<title>MovieBib - Film- und Video-Bibliothek</title>

	<link rel="stylesheet" href="styles.css">

	<style>
		table, th, td {
			/*border: 1px solid white;*/
		}
		a:link {
			color: #666666;
		}
		a:visited {
			color: #666666;
		}
		a:hover {
			color: #440000;
		}
	</style>

	<?php
		include_once 'db/dbconnect.php';
		include_once 'lib/session.php';

		$session = get_php_param("s");
		$nick = verify_session($session);

		$search = get_php_param("search");
		$con = new Connection();
	?>
</head>
<body>
	<table>
		<tr>
			<td style="width:  2%;">&nbsp;</td>
			<td style="width: 96%;">		
	<table>
		<tr>
			<td style="width:  2%;">&nbsp;</td>
			<td style="width: 49%;">&nbsp;</td>
			<td style="width: 49%; text-align: right;"><?php echo $nick != "" ? $nick : '<a href="login.php?from=index">Login</a>'; ?></td>
		</tr>
		<tr>
			<td colspan="3" style="text-align: center;">
				<table>
					<tr>
						<td style="width: 1050; text-align: center;">
							<form method="post" action="index.php<?php echo session_param($nick, $session)?>" enctype="multipart/form-data">
								<input type="text" name="search" size="75" value="<?php echo $search; ?>"/>
								<input type="submit" value='Suche'/>
							</form>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" id="spacer_medium"></td>
		</tr>
		<tr>
			<td colspan="3">
				<table id="title_medium">
					<tr>
						<?php for ($i = 65; $i < 91; $i++) { ?>
						<td style="width: 30px;">
							<?php echo chr($i); ?>
						</td>
						<?php } ?>
						<td style="width: 30px;">
							0-9
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" id="spacer_medium"></td>
		</tr>
		<tr>
			<td colspan="3" id="title_small" style="font-size: 14; text-align: justify;">
			<?php
				$result = $con->query("SELECT * FROM Genres ORDER BY Name");
				$first = true;

				while (($row = $result->fetch_assoc()) != null)
				{
					if ($first)
						$first = false;
					else
						echo '<font face="arial" color="#666666"> | </font>';
					echo $row["Name"];
				}
				$result->close();
			?>
			</td>
		</tr>
		<tr>
			<td colspan="3" id="spacer_medium"></td>
		</tr>
		<?php
			if ($search != "")
				$where = " WHERE Title LIKE '%" . $search . "%' ";
			$result = $con->query("SELECT * FROM Videos " . $where . " ORDER BY CASE WHEN strcmp(Title, 'A') >= 0 AND strcmp(Title, 'ZZZ') <= 0 THEN 0 ELSE 1 END, Title");
			$letter = "";
			$col = 1;

			while (($row = $result->fetch_assoc()) != null)
			{
				$title = $row["Title"];
				if (strtoupper(substr($title, 0, 1)) != $letter)
				{
					$letter = strtoupper(substr($title, 0, 1));

					if ($col == 2)
					{
						echo "<td></td></tr>";
						$col = 1;
					}
		?>	
		<tr>
			<td colspan="3" id="spacer_medium"></td>
		</tr>
		<tr>
			<td colspan="3" id="title_large"><?php echo $letter; ?></td>
		</tr>
		<?php
				}
				
				if ($col == 1)
				{
		?>
		<tr>
			<td></td>
		<?php
				}
		?>
			<td><a href="show_video.php<?php echo session_param($nick, $session, $row["ID"]); ?>"><?php echo $row["Title"]; ?></a></td>
		<?php
				if ($col == 2)
				{
		?>
		</tr>
		<?php
				}
				
				$col = $col == 1 ? 2 : 1;
			}

			if ($col == 2)
				echo "<td></td></tr>";

			$result->close();
		?>
	</table>
</td>
<td style="width:  2%"></td>
</tr>
</table>
</body>
<?php
	$con->close()
?>
</html>
