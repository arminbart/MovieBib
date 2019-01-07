<html>
<head>
	<link rel="stylesheet" href="styles.css">

	<style>
		img {
			float: right;
		}

		rating {
			width: 30px;
			text-align: center;
		}
	</style>

	<?php
		include_once 'db/dbconnect.php';
		include_once 'lib/session.php';
		include_once 'lib/videohelpers.php';

		$id = get_php_param("id");
		$session = get_php_param("s");
		$nick = verify_session($session);

		$con = new Connection();
		$result = $con->query("SELECT *, (SELECT Name FROM Genres WHERE ID = Genre) AS GenreName FROM Videos WHERE ID = " . $id);
		$row = $result->fetch_assoc();

		$coverfile = get_cover_filename($id);
		if (!file_exists($coverfile) or $nick == null)
		{
			$coverfile = "img/cover.png";
		}
		else //if (debug())
		{
			list($w, $h) = getimagesize($coverfile);
			$coverinfo = $w . " x " . $h;
		}
	?>

	<title>MovieBib - <?php echo $row["Title"]; ?></title>
</head>
<body>
	<table>
		<tr>
			<td style="width:  2%;">&nbsp;</td>
			<td style="width: 96%;">
				<table>
					<tr>
						<td style="width:  5%;"><a href="index.php<?php echo session_param($nick, $session); ?>">&lt;</a></td>
						<td style="width: 10%;">&nbsp;</td>
						<td style="width: 60%;">&nbsp;</td>
						<td style="width: 25%;text-align: right;"><?php echo $nick != "" ? $nick : '<a href="login.php?from=show_video;id;' . $id . '">Login</a>'; ?>
						</td>
					</tr>
					<tr>
						<td colspan="4" id="title_large"><?php echo $row["Title"] . " [" . ($row["Lang"] == "de" ? "dt" : $row["Lang"]) . ".]"; ?><hr></td>
					</tr>
					<tr>
						<td colspan="3" id="title_medium"><?php echo ($row["Country"] != "" ? $row["Country"] : "(Produktionsland unbekannt)") . " " . ($row["Year"] > 0 ? $row["Year"] : "(Jahr unbekannt)") ?></td>
						<td rowspan="3"><?php if ($nick != "") { ?><img src="img/edit.png"><?php } ?></td>
					</tr>
					<tr>
						<td colspan="3" id="spacer_small"></td>
					</tr>
					<tr>
						<td colspan="2" id="title_small"><?php echo $row["GenreName"]; ?></td>
						<td colspan="1"><?php echo str_replace(",", ", ", $row["SubGenre"]); ?></td>
					</tr>
					<tr>
						<td colspan="4" id="spacer_medium"></td>
					</tr>
					<tr>
						<td colspan="4" id="text_small"><?php echo $row["Info"] != "" ? $row["Info"] : "Keine Beschreibung vorhanden"; ?></td>
					</tr>
					<tr>
						<td colspan="4" id="spacer_medium" style="text-align: right;"><?php echo $coverinfo; ?></td>
					</tr>
					<tr>
						<td colspan="2" id="title_small">Alternativtitel</td>
						<td colspan="1"><?php echo $row["OtherTitles"] != "" ? $row["OtherTitles"] : "- / -"; ?></td>
						<td rowspan="12" style="vertical-align:top;">
							<table style="margin-right: 0;">
								<tr>
									<td style="width: 40px;">&nbsp;</td>
									<td><a href="edit_cover.php<?php echo session_param($nick, $session, $id); ?>"><img src="<?php echo $coverfile; ?>"></a></td>
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
						<td colspan="1"><?php echo $row["Medium"] . ($row["Resolution"] != "" ? ", " . $row["Resolution"] : ""); ?></td>
					</tr>
					<tr>
						<td colspan="2" id="title_small">Spieldauer</td>
						<td colspan="1"><?php echo $row["Duration"] != "" ? $row["Duration"] . " min" : "unbekannt"; ?></td>
					</tr>
					<tr>
						<td colspan="2" id="title_small">Dateiname</td>
						<td colspan="1"><?php echo $row["File"]; ?></td>
					</tr>
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
						<td colspan="2"><?php echo $row["Actors"]; ?></td>
					</tr>
					<?php } ?>
					<tr>
						<td colspan="3" id="spacer_large"></td>
					</tr>
					<tr>
						<td colspan="3" id="title_small">Bewertung<br>
							<table style="margin-left: 0;">
								<tr>
									<td colspan="5" id="spacer_small"></td>
								</tr>
								<tr>
									<td id="rating"><?php echo $row["Rating"] > 0 ? '<img src="img/star_red.png">' : '<img src="img/star_grey.png">'; ?></td>
									<td id="rating"><?php echo $row["Rating"] > 1 ? '<img src="img/star_red.png">' : '<img src="img/star_grey.png">'; ?></td>
									<td id="rating"><?php echo $row["Rating"] > 2 ? '<img src="img/star_red.png">' : '<img src="img/star_grey.png">'; ?></td>
									<td id="rating"><?php echo $row["Rating"] > 3 ? '<img src="img/star_red.png">' : '<img src="img/star_grey.png">'; ?></td>
									<td id="rating"><?php echo $row["Rating"] > 4 ? '<img src="img/star_red.png">' : '<img src="img/star_grey.png">'; ?></td>
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
