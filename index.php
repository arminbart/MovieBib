<html>
<head>
	<title>MovieBib - Film- und Video-Bibliothek</title>

	<link rel="stylesheet" href="styles.css">

	<?php
		include_once 'db/dbconnect.php';
		include_once 'lib/filter.php';
		include_once 'lib/phonetic.php';
		include_once 'lib/session.php';

		$session = Session::get();
		$filter = Filter::get();
		$con = new Connection();
	?>

	<style>
		table, th, td {
			<?php if (debug()) { echo "border: 1px solid white;"; } ?>
		}

		a.filter:link {
			color: white;
		} /* unvisited link */
		a.filter:visited {
			color: white;
		} /* visited link */
		a.filter:hover {
			color: #440000;
		} /* mouse over link */
		a.filter:active {
			color: #440000;
		} /* selected link */

		#spacer {
			font-face: arial;
			color: #666666;
		}

		#selected {
			color: #880000;
		}
	</style>
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
			<td style="width: 49%; text-align: right;"><?php echo $session->valid() ? $session->nick() : '<a href="login.php?from=index">Login</a>'; ?></td>
		</tr>
		<tr>
			<td colspan="3" style="text-align: center;">
				<table>
					<tr>
						<td style="width: 1050; text-align: center;">
							<form method="post" action="index.php<?php echo $session->param() . $filter->param("search", null); ?>" enctype="multipart/form-data">
								<input type="text" name="search" size="75" style="font-size: 16; background: #DDDDDD; padding: 5;" value="<?php echo $filter->search; ?>"/>
								&nbsp;&nbsp;
								<input type="submit" style="font-size: 14; background: white;" value='Suche'/>
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
							<a href="index.php<?php echo $session->param() . $filter->param() . '#' . strtolower(chr($i)) ?>" class="filter"><?php echo chr($i); ?></a>
						</td>
						<?php } ?>
						<td style="width: 30px;">
							<a href="index.php#misc" class="filter">0-9</a>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<tr>
			<td colspan="3" id="spacer_medium"></td>
		</tr>
		<tr>
			<td colspan="3" id="title_small" style="text-align: justify;">
			<?php
				$stmt = new SelectStatement("Genres", "*", null, "Name");
				$ps = $con->query($stmt);
				$result = $ps->get_result();;
				$first = true;

				while (($row = $result->fetch_assoc()) != null)
				{
					if ($first)
						$first = false;
					else
						echo '<font id="spacer"> | </font>';

					if ($filter->is_set("genre", $row["ID"]))
						echo "<font id='selected'>" . $row["Name"] . "</font>";
					else
						echo "<a href='index.php" . $session->param() . $filter->param("genre", $row["ID"]) . "' class='filter'>" . $row["Name"] . "</a>";
				}

				if ($filter->is_set("genre"))
					echo "<a href='index.php" . $session->param() . $filter->param("genre", null) . "'>&nbsp;<img src='img/cancel.png' style='vertical-align: middle;'></a>";
				else
					echo "<img src='img/cancel_invisible.png'>";

				$result->close();
				$ps->close();
			?>
			</td>
		</tr>
		<tr>
			<td colspan="3" id="spacer_medium"><a href="index.php<?php echo $session->param() . $filter->param("special", $filter->special() ? null : 1); ?>"><img src="img/<?php echo $filter->special() ? "up" : "down"; ?>.png" style="float: right;"></a></td>
		</tr>
		<tr>
			<td colspan="3" id="title_small">
				<table id="title_small" style="width: 100%;">
					<tr>
						<td style="text-align: left;">
							<?php
								$stmt = new SelectStatement("Languages", "*", null, "ShortName");
								$ps = $con->query($stmt);
								$result = $ps->get_result();;
								$first = true;

								while (($row = $result->fetch_assoc()) != null)
								{
									if ($first)
										$first = false;
									else
										echo '<font id="spacer"> | </font>';

									if ($filter->is_set("lang", $row["ID"]))
										echo "<font id='selected'>" . $row["Name"] . "</font>";
									else
										echo "<a href='index.php" . $session->param() . $filter->param("lang", $row["ID"]) . "' class='filter'>" . $row["Name"] . "</a>";
								}

								if ($filter->is_set("lang"))
									echo "<a href='index.php" . $session->param() . $filter->param("lang", null) . "'>&nbsp;<img src='img/cancel.png' style='vertical-align: middle;'></a>";

								$result->close();
								$ps->close();
							?>
						</td>
						<td style="text-align: right;">
							<?php
								$type = null;
								if ($filter->special())
								{
									$special_filters = array("medium,Datei,File", "medium,Blu-ray,BR", "medium,DVD", "medium,VHS", "res,HD", "status,neu erfasst,0", "status,teilweise erfasst,1-8", "status,vollst&auml;ndig erfasst,9");
									foreach ($special_filters as $sf)
									{
										$sf2 = explode(",", $sf);

										if ($sf2[0] == $type)
										{
											echo "<font id='spacer'> | </font>";
										}
										else if ($type !== null)
										{
											if ($filter->is_set($type))
												echo "<a href='index.php" . $session->param() . $filter->param($type, null) . "'>&nbsp;<img src='img/cancel.png' style='vertical-align: middle;'></a>";
											else
												echo "<img src='img/cancel_invisible.png'>";
											echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
										}
										$type = $sf2[0];

										if ($filter->is_set($sf2[0], $sf2[sizeof($sf2) - 1]))
											echo "<font id='selected'>" . $sf2[1] . "</font>";
										else
											echo "<a href='index.php" . $session->param() . $filter->param($sf2[0], $sf2[sizeof($sf2) - 1]) . "' class='filter'>" . $sf2[1] . "</a>";
									}

									if ($filter->is_set($type))
										echo "<a href='index.php" . $session->param() . $filter->param($type, null) . "'>&nbsp;<img src='img/cancel.png' style='vertical-align: middle;'></a>";
									else
										echo "<img src='img/cancel_invisible.png'>";
								}
							?>
						</td>
					</tr>
				</table>
			</td>
		</tr>
		<?php
			$ps = $con->query(new SelectStatement("Videos", "*", $filter->where(), "CASE WHEN strcmp(Title, 'A') >= 0 AND strcmp(Title, 'ZZZ') <= 0 THEN 0 ELSE 1 END, Title"));
			$result = $ps->get_result();
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
			<td colspan="3" id="title_large"><a id="<?php $l = strtolower($letter); echo ($l < "a" or $l > "z") ? "misc" : $l; ?>"><?php echo $letter; ?></a></td>
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
			<td><a href="show_video.php<?php echo $session->param($row["ID"]); ?>"><?php echo $row["Title"]; ?></a></td>
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
			$ps->close();
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
