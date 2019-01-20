<html>
<head>
	<link rel="stylesheet" href="styles.css">

	<?php
		include 'db/dbconnect.php';
		include 'lib/session.php';
		include 'lib/videohelpers.php';

		$id = get_php_param("id");
		$session = get_php_param("s");
		$nick = verify_session($session);

		$con = new Connection();
		$ps = $con->query(new SelectStatement("Videos", "*, (SELECT Name FROM Genres WHERE ID = Genre) AS GenreName", new Where("ID", $id)));
		$result = $ps->get_result();
		$row = $result->fetch_assoc();
		$result->close();
		$ps->close();
		$con->close();

		$coverfile = get_cover_filename($id, true, $nick);
	?>

	<style>
		body {
			vertical-align: middle;
		}

		table, th, td {
			<?php if (debug()) { echo "border: 1px solid white;"; } ?>
		}

		#title_small, #title_medium, #title_large {
			vertical-align: middle;
		}

		#hint {
			font-size: 10;
		}
	</style>

	<title>MovieBib - <?php echo $row["Title"]; ?></title>
</head>
<body>
	<table>
		<tr>
			<td style="width:  2%;">&nbsp;</td>
			<td style="width: 96%;">
				<form method="post" action="save_video.php<?php echo session_param($nick, $session, $id); ?>" enctype="multipart/form-data">
				<table> 
					<?php if ($nick == null) { ?>
					<tr>
						<td>
							<a href="show_video.php?id=<?php echo $id; ?>">&lt;</a><br>
							<a href="login.php?from=edit_cover;id;<?php echo $id; ?>">Einloggen zum Bearbeiten des Videos.</a>
						</td>
					</tr>
					<?php } else { ?>
					<tr>
						<td style="width: 15%;"><a href="index.php<?php echo session_param($nick, $session); ?>">&lt;</a></td>
						<td style="width: 27%;">&nbsp;</td>
						<td style="width:  2%;">&nbsp;</td>
						<td style="width: 27%;">&nbsp;</td>
						<td style="width:  2%;">&nbsp;</td>
						<td style="width: 27%;text-align: right;"><?php echo $nick; ?></td>
					</tr>
					<tr>
						<td id="title_large">Titel</td>
						<td colspan="3"><input type="text" name="title" style="width: 100%" value="<?php echo $row['Title'];?>"></td>
						<td></td>
						<td>
							<input type="radio" name="lang" value="de" <?php echo get_checked($row['Lang'], 'de'); ?>>&nbsp;[dt.]
							<input type="radio" name="lang" value="en" <?php echo get_checked($row['Lang'], 'en'); ?>>&nbsp;[en.]
						</td>
					</tr>
					<tr>
						<td colspan="6" id="spacer_small"><hr></td>
					</tr>
<!--
					<tr>
						<td id="title_small">Produktionsjahr</td>
						<td colspan="5"><input type="text" name="year" size="15" maxlength="4" value="<?php echo $row['Year'];?>">
							&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
							<span id="title_small">Produktionsland (bzw. -l&auml;nder)</span>&nbsp;
							<input type="text" name="country" size="20" maxlength="15" value="<?php echo $row['Country'];?>"><a href="https://filmdenken.de/sonst/land.htm"><span id="hint">&nbsp;&nbsp;(z.B. D/I/F, siehe L&auml;nderk&uuml;rzel)</span></a>
						</td>
					</tr>
-->
					<tr>
						<td colspan="6" id="spacer_small"></td>
					</tr>
					<tr>
						<td colspan="6">
							<table style="width: 100%;">
								<tr>
									<td id="title_small">Produktionsjahr</td>
									<td><input type="text" name="year" maxlength="4" style="width: 100%;" value="<?php echo $row['Year'];?>"></td>
									<td id="title_small" style="text-align: right;">Produktionsland&nbsp;&nbsp;</td>
									<td><input type="text" name="country" size="20" style="width: 100%;" value="<?php echo $row['Country'];?>"></td>
									<td colspan="2"><a href="https://filmdenken.de/sonst/land.htm"><span id="hint">&nbsp;(z.B. D/I/F, siehe L&auml;nderk&uuml;rzel)</span></a></td>
								</tr>
								<tr>
									<td colspan="6" id="spacer_medium"></td>
								</tr>
								<tr id="title_small">
									<td colspan="3">Genre</td>
									<td colspan="3">weitere Genres</td>
								</tr>
								<tr id="text_small">
								<?php 
									$con = new Connection();
									$stmt = new SelectStatement("Genres", "*", null, "Name");
									$ps = $con->query($stmt);
									$result = $ps->get_result();;
									$cnt = 0;

									while (($row2 = $result->fetch_assoc()) != null)
									{
										if ($cnt % 6 == 0)
										{
											if ($cnt > 0)
												echo "</td>";
											echo "<td id='text_small' style='width: " . ($cnt > 10 ? 22 : 13) . "%;'>";
										}

										echo "<input type='radio' name='genre' value='" . $row2["ID"] . "' " . get_checked($row2["ID"], $row["Genre"]) . ">&nbsp;" . $row2["Name"] . "<br>";
										$cnt += 1;
									}
									echo "</td>";
									$result->close();

									$ps = $con->query($stmt);
									$result = $ps->get_result();;
									$cnt = 0;
									$others = explode(";", $row["OtherGenres"]);

									while (($row2 = $result->fetch_assoc()) != null)
									{
										if ($cnt % 6 == 0)
										{
											if ($cnt > 0)
												echo "</td>";
											echo "<td id='text_small' style='width: " . ($cnt > 10 ? 13 : 13) . "%;'>";
										}

										$checked = false;
										for ($i = 0; $i < sizeof($others); $i++)
											if ($others[$i] == $row2["ID"])
												$checked = true;
										echo "<input type='checkbox' name='othergenre" . $cnt . "' value='" . $row2["ID"] . "'" . ($checked ? " checked" : "") . ">&nbsp;" . $row2["Name"] . "<br>";
										$cnt += 1;
									}
									echo "</td>";
									$result->close();
									$ps->close();
									$con->close();
								?>
								</tr>		
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="6" id="spacer_medium"></td>
					</tr>
					<tr>
						<td id="title_small" style="vertical-align: text-top;">Beschreibung</td>
						<td colspan="5"><textarea name="info" rows="7" style="width: 100%;"><?php echo $row['Info'];?></textarea></td>
					</tr>
					<tr>
						<td colspan="6" id="spacer_medium" style="text-align: right;"></td>
					</tr>
					<tr>
						<td id="title_small" style="vertical-align: text-top;">Alternativtitel<br><span id="hint">(bis zu 3)</span></td>
						<td colspan="3">
							<?php 
								$othertitles = explode(";", $row["OtherTitles"]);

								for ($i = 0; $i < 3; $i++)
								{
							?>
							<input type="text" name="othertitle<?php echo $i; ?>" style="width: 100%" <?php echo (sizeof($othertitles) > $i ? ' value="' . trim($othertitles[$i]) . '"' : ''); ?>><br>
							<?php } ?>
						</td>
						<td rowspan="12">&nbsp;</td>
						<td rowspan="12" style="vertical-align:top;">
							<img style="float: right;" src="<?php echo $coverfile; ?>">
						</td>
					</tr>
					<tr>
						<td id="title_small">Originaltitel</td>
						<td colspan="3"><input type="text" name="origtitle" style="width: 100%;" value="<?php echo $row['OrigTitle'];?>"></td>
					</tr>
					<tr>
						<td>Vorg&auml;nger</td>
						<td colspan="3">Vorg&auml;nger-Verkn&uuml;pfung noch nicht implementiert.</td>
					</tr>
					<tr>
						<td>Nachfolger</td>
						<td colspan="3">Nachfolger-Verkn&uuml;pfung noch nicht implementiert.</td>
					</tr>
					<tr>
						<td colspan="4" id="spacer_medium"><hr></td>
					</tr>
					<tr>
						<td id="title_small" style="vertical-align: text-top;">Format</td>
						<td colspan="3">
							<table style="margin-left: 0; float: left;">
								<tr>
									<td><input type="radio" name="medium" value="auto" <?php echo get_checked($row['Medium'], ''); ?>>&nbsp;Auto&nbsp;&nbsp;&nbsp;</td>
									<td><input type="radio" name="medium" value="avi" <?php echo get_checked($row['Medium'], 'avi'); ?>>&nbsp;AVI&nbsp;&nbsp;&nbsp;</td>
									<td><input type="radio" name="medium" value="mp4" <?php echo get_checked($row['Medium'], 'mp4'); ?>>&nbsp;MP4&nbsp;&nbsp;&nbsp;</td>
									<td><input type="radio" name="medium" value="mkv" <?php echo get_checked($row['Medium'], 'mkv'); ?>>&nbsp;MKV&nbsp;&nbsp;&nbsp;</td>
									<td><input type="radio" name="medium" value="iso" <?php echo get_checked($row['Medium'], 'iso'); ?>>&nbsp;ISO&nbsp;&nbsp;&nbsp;</td>
								</tr>
								<tr>
									<td>&nbsp;</td>
									<td><input type="radio" name="medium" value="vhs" <?php echo get_checked($row['Medium'], 'vhs'); ?>>&nbsp;VHS</td>
									<td><input type="radio" name="medium" value="dvd" <?php echo get_checked($row['Medium'], 'dvd'); ?>>&nbsp;DVD</td>
									<td colspan="2"><input type="radio" name="medium" value="br" <?php echo get_checked($row['Medium'], 'br'); ?>>&nbsp;Blu-ray</td>
								</tr>
							</table>
							<table style="margin-right: 0; float: right;">
								<tr>
									<td><input type="checkbox" name="cut" value="1" <?php echo get_checked($row['Cut'], 'bool'); ?>>&nbsp;geschnitten</td>
								</tr>
								<tr>
									<td><input type="checkbox" name="resolution" value="hd" <?php echo get_checked($row['Resolution'], 'hd'); ?>>&nbsp;HD</td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td id="title_small" style="margin-left: 0;">Spieldauer</td>
						<td colspan="3"><input type="text" name="duration" size="15" maxlength="4" value="<?php echo $row['Duration'];?>">&nbsp;min</td>
					</tr>
					<tr>
						<td id="title_small">Dateiname</td>
						<td colspan="3"><input type="text" name="file" style="width: 100%;" value="<?php echo $row['File'];?>"></td>
					</tr>
					<tr>
						<td id="title_small">Link</td>
						<td colspan="3"><input type="text" name="link" style="width: 100%;" value="<?php echo $row['Link'];?>"></td>
					</tr>
					<tr>
						<td id="title_small">Trailer</td>
						<td colspan="3"><input type="text" name="trailer" style="width: 100%;" value="<?php echo $row['Trailer'];?>"></td>
					</tr>
					<tr>
						<td id="title_small">Cover</td>
						<td><?php echo $coverfile;?></td>
						<td></td>
						<td>
							<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo get_php_param('cover_max_filesize'); ?>" />
							<input type="file" accept="image/jpeg" name="image" id="image"/>
						</td>
					</tr>
					<tr>
						<td colspan="4" id="spacer_large"></td>
					</tr>
					<tr>
						<td id="title_small">Regisseur</td>
						<td colspan="3"><input type="text" name="director" maxlength="64" style="width: 100%;" value="<?php echo $row['Director'];?>"></td>
					</tr>
					<tr>
						<td colspan="4" id="spacer_small"></td>
					</tr>
					<tr>
						<td rowspan="7" id="title_small" style="vertical-align: text-top;">Schauspieler</td>
						<?php 
							$actors = explode(";", $row["Actors"]);

							for ($i = 0; $i < 21; $i++)
							{
								if ($i % 3 == 0 and $i > 0)
									echo "<tr>";

								echo "<td><input type='text' name='actor" . $i . "' maxlength='32' style='width: 100%' value='" . (sizeof($actors) > $i ? trim($actors[$i]) : "") . "'></td>";

								if ($i % 3 < 2)
									echo "<td></td>";
								else
									echo "</tr>";
							}
						?>
					<tr>
						<td colspan="6" id="spacer_large" style="color: black;">This text is here just to give the browser a hint that it should use a certain minimum width if possible. A simple form of res-pon-sive-ness if you will.</td>
					</tr>
					<tr>
						<td id="title_small" style="vertical-align: text-top;">Bewertung<br>
						<td colspan="4"> 
							<table style="margin-left: 0;">
								<tr>
									<td colspan="5" id="spacer_small"></td>
								</tr>
								<tr>
									<td id="rating" style="width: 150;"><input type="radio" name="rating" value="0" <?php if (intval($row["Rating"]) == 0) { echo "checked"; } ?>></td>
									<?php for ($i = 1; $i <= 5; $i++) { ?>
									<td id="rating"><input type="radio" name="rating" value="<?php echo $i; ?>" <?php if ($row["Rating"] == $i) { echo "checked"; } ?>></td>
									<?php } ?>
								</tr>
								<tr>
									<td id="rating" style="width: 150;">(keine Bewertung)</td>
									<td id="rating"><img src="img/star1.png"></td>
									<td id="rating"><img src="img/star2.png"></td>
									<td id="rating"><img src="img/star3.png"></td>
									<td id="rating"><img src="img/star4.png"></td>
									<td id="rating"><img src="img/star5.png"></td>
								</tr>
							</table>
						</td>
						<td style="margin-right: 0; text-align: right;"><input type="submit" value='Speichern' onclick="return validateForm()"/></td>
					</tr>
					<?php } ?>
				</table>
				</form>
			</td>
			<td style="width:  2%;">&nbsp;</td>
		</tr>
	</table>
</body>
</html>
