<html>
<head>
	<link rel="stylesheet" href="styles.css">

	<?php
		include_once 'db/dbconnect.php';
		include_once 'lib/session.php';
		include_once 'lib/videohelpers.php';

		$id = get_php_param("id");
		$session = Session::get();
		$details = (bool_php_param("detalis") and $session->valid());

		$con = new Connection();
		$ps = $con->query(new SelectStatement("Videos", "*, (SELECT Name FROM Genres WHERE ID = Genre) AS GenreName", new Where("ID", $id)));
		$result = $ps->get_result();
		$row = $result->fetch_assoc();

		$coverfile = get_cover_filename($id, $session);
	?>

	<style>
		table, th, td {
			<?php if (debug()) { echo "border: 1px solid white;"; } ?>
		}

		img {
			float: right;
		}
	</style>

	<title>MovieBib - <?php echo $row["Title"]; ?></title>

	<?php if (get_php_param("no_cache") == 1) { ?>
	<meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
	<?php } ?>
</head>
<body>
	<table>
		<tr>
			<td style="width:  2%;">&nbsp;</td>
			<td style="width: 96%;">
				<table>
					<tr>
						<td style="width:  5%;"><a href="index.php<?php echo $session->param(); ?>">&lt;</a></td>
						<td style="width: 10%;">&nbsp;</td>
						<td style="width: 60%;">&nbsp;</td>
						<td style="width: 25%;text-align: right;"><?php echo $session->valid() ? $session->nick() : '<a href="login.php?from=show_video;id;' . $id . '">Login</a>'; ?></td>
					</tr>
					<tr>
						<td colspan="4" id="title_large"><?php echo $row["Title"] . " [" . ($row["Lang"] == "de" ? "dt" : $row["Lang"]) . ".]"; ?><hr></td>
					</tr>
					<tr>
						<td colspan="3" id="title_medium"><?php echo ($row["Country"] != "" ? $row["Country"] : "(Produktionsland unbekannt)") . " " . ($row["Year"] > 0 ? $row["Year"] : "(Jahr unbekannt)") ?></td>
						<td rowspan="3"><?php if ($session->valid()) { ?><a href="edit_video.php<?php echo $session->param($id); ?>"><img src="img/edit.png"></a><?php } ?></td>
					</tr>
					<tr>
						<td colspan="3" id="spacer_small"></td>
					</tr>
					<tr>
						<td colspan="2" id="title_small"><?php echo $row["GenreName"]; ?></td>
						<td colspan="1"><?php echo str_replace(";", ", ", $row["OtherGenres"]); ?></td>
					</tr>
					<tr>
						<td colspan="4" id="spacer_medium"></td>
					</tr>
					<tr>
						<td colspan="4" id="text_small"><?php echo $row["Info"] != "" ? $row["Info"] : "Keine Beschreibung vorhanden"; ?></td>
					</tr>
					<tr>
						<td colspan="4" id="spacer_medium" style="text-align: right; font-size: 18;">
							<?php
								if ($details)
									echo get_cover_info($coverfile);
								else if ($session->valid())
									echo "<a href='show_video.php" . $session->param($id) . "&detalis=1'>...</a>";
							?>
						</td>
					</tr>
					<tr>
						<td colspan="2" id="title_small">Alternativtitel</td>
						<td colspan="1"><?php echo $row["OtherTitles"] != "" ? str_replace(";","<br>", $row["OtherTitles"]) : "- / -"; ?></td>
						<td rowspan="14" style="vertical-align:top;">
							<table style="margin-right: 0;">
								<tr>
									<td style="width: 40px;">&nbsp;</td>
									<td><a href="edit_cover.php<?php echo $session->param($id); ?>"><img src="<?php echo $coverfile; ?>"></a></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="2" id="title_small">Originaltitel</td>
						<td colspan="1"><?php echo $row["OrigTitle"] != "" ? $row["OrigTitle"] : "- / -"; ?></td>
					</tr>
					<tr>
						<td colspan="2">Vorg&auml;nger</td>
						<td colspan="1">Vorg&auml;nger-Verkn&uuml;pfung noch nicht implementiert.</td>
					</tr>
					<tr>
						<td colspan="2">Nachfolger</td>
						<td colspan="1">Nachfolger-Verkn&uuml;pfung noch nicht implementiert.</td>
					</tr>
					<tr>
						<td colspan="3" id="spacer_medium"><hr></td>
					</tr>
					<tr>
						<td colspan="2" id="title_small">Link</td>
						<td colspan="1"><?php echo $row["Link"] != "" ? '<a href="' . $row["Link"] . '">' . $row["Link"] . "</a>" : "- / -"; ?></td>
					</tr>
					<tr>
						<td colspan="2" id="title_small">Format</td>
						<td colspan="1"><?php echo ($row["Medium"] == "BR" ? "Blu-ray" : $row["Medium"]) . ($row["Resolution"] != "" ? ", " . $row["Resolution"] : "") . (boolval($row["Cut"]) ? "" : "&nbsp;&nbsp;(ungeschnitten)"); ?></td>
					</tr>
					<tr>
						<td colspan="2" id="title_small">Spieldauer</td>
						<td colspan="1"><?php echo $row["Duration"] != "" ? $row["Duration"] . " min" : "unbekannt"; ?></td>
					</tr>
					<tr>
						<td colspan="2" id="title_small">Dateiname</td>
						<td colspan="1"><?php echo $row["File"]; ?></td>
					</tr>
					<?php if ($details) { ?>
					<tr>
						<td colspan="2" id="title_small">urspr. Dateiname</td>
						<td colspan="1"><?php echo $row["OrigFile"]; ?></td>
					</tr>
					<tr>
						<td colspan="2" id="title_small">urspr. Ablage</td>
						<td colspan="1"><?php echo $row["OrigLocation"]; ?></td>
					</tr>
					<tr>
						<td colspan="2" id="title_small">Phonetischer Code</td>
						<td colspan="1"><?php echo $row["Phonetic"]; ?></td>
					</tr>
					<?php } ?>
					<tr>
						<td colspan="3" id="spacer_large"></td>
					</tr>
					<?php if ($row["Director"] != "") { ?>
					<tr>
						<td colspan="1" id="title_small">von</td>
						<td colspan="2"><?php echo $row["Director"]; ?></td>
					</tr>
					<?php } ?>
					<?php if ($row["Actors"] != "") { ?>
					<tr>
						<td colspan="1" id="title_small">mit</td>
						<td colspan="2"><?php echo str_replace(";", ", ", $row["Actors"]); ?></td>
					</tr>
					<?php } ?>
					<tr>
						<td colspan="3" id="spacer_large" style="color: black;">This text is here just to give the browser a hint that it should use a certain minimum width if possible. A simple form of res-pon-sive-ness if you will.</td>
					</tr>
					<tr>
						<td colspan="3" id="title_small">Bewertung<br>
							<table style="margin-left: 0;">
								<tr>
									<td colspan="5" id="spacer_small"></td>
								</tr>
								<tr>
									<td id="rating"><?php echo $row["Rating"] > 0 ? '<img src="img/star5.png">' : '<img src="img/star1.png">'; ?></td>
									<td id="rating"><?php echo $row["Rating"] > 1 ? '<img src="img/star5.png">' : '<img src="img/star1.png">'; ?></td>
									<td id="rating"><?php echo $row["Rating"] > 2 ? '<img src="img/star5.png">' : '<img src="img/star1.png">'; ?></td>
									<td id="rating"><?php echo $row["Rating"] > 3 ? '<img src="img/star5.png">' : '<img src="img/star1.png">'; ?></td>
									<td id="rating"><?php echo $row["Rating"] > 4 ? '<img src="img/star5.png">' : '<img src="img/star1.png">'; ?></td>
								</tr>
							</table>
						</td>
					</tr>
				</table>
			</td>
			<td style="width:  2%;">&nbsp;</td>
		</tr>
	</table>
</body>
</html>

<?php
	$result->close();
	$ps->close();
	$con->close();
?>
