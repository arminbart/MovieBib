<html>
<head>
	<?php
		include 'db/dbconnect.php';

		$id = trim($_GET["id"]);
		$con = new Connection();
		$result = $con->query("SELECT * FROM Videos WHERE ID = " . $id);
		$row = $result->fetch_assoc();
	?>
	<style>
		body {
			background-color: black;
			font-family: helvetica;	
			color: #666666;
			font-size: 14;
			vertical-align: text-top;
		}
		/*hr {		
			height: 2px;
		  	background-image: linear-gradient(to left, rgba(255,0,0,0), rgba(255,0,0,1)); 
		} */
		hr {
			border-color: #440000;
		}
		table {
			margin-left:auto;
			margin-right:auto;
		}
		img {
		  float: right;
		  /*vertical-align: top;*/
		}
		#spacer_small {
			height: 5;
		}
		#spacer_medium {
			height: 20;
		}
		#spacer_large {
			height: 50;
		}
		#title1 {
			text-align: left;
			color: white;
			font-size: 26;
			vertical-align: text-top;
		} 
		#title2 {
			text-align: left;
			color: white;
			font-size: 18;
			vertical-align: text-top;
		} 
		#title3 {
			text-align: left;
			color: white;
			vertical-align: text-top;
		} 
		#info {
			text-align: left;
			color: #666666;
			font-size: 12;
			/*font-style: italic;*/
			vertical-align: text-top;
			text-align: justify;
		}
		#rating {
			width: 30px;
			text-align: center;
		}
	</style>
</head>
<body>
	<table border="0">
		<tr>
			<td style="width:  2%">&nbsp;</td>
			<td style="width: 96%">		
				<table border="0">
					<tr>
						<td style="width:  5%">&nbsp;</td>
						<td style="width: 10%">&nbsp;</td>
						<td style="width: 60%">&nbsp;</td>
						<td style="width: 25%">&nbsp;</td>
					</tr>
					<tr>
						<td colspan="4" id="title1"><?php echo $row["Title"] . " [" . ($row["Lang"] == "de" ? "dt" : $row["Lang"]) . ".]"; ?><hr></td>
					</tr>
					<tr>
						<td colspan="3" id="title2"><?php echo ($row["Country"] != "" ? $row["Country"] : "(Produktionsland unbekannt)") . " " . ($row["Year"] > 0 ? $row["Year"] : "(Jahr unbekannt)") ?></td>
						<td rowspan="3"><img src="img/edit.png"></td>
					</tr>
					<tr>
						<td colspan="3" id="spacer_small"></td>
					</tr>
					<tr>
						<td colspan="2" id="title3"><?php echo $row["Genre"] != "" ? $row["Genre"] : "(Genre unbekannt)"; ?></td>
						<td colspan="1"><?php echo str_replace(",", ", ", $row["SubGenre"]); ?></td>			
					</tr>
					<tr>
						<td colspan="4" id="spacer_medium"></td>
					</tr>
					<tr>
						<td colspan="4" id="info"><?php echo $row["Info"] != "" ? $row["Info"] : "Keine Beschreibung vorhanden"; ?></td>
					</tr>
					<tr>
						<td colspan="4" id="spacer_medium"></td>
					</tr>
					<tr>
						<td colspan="2" id="title3">Alternativtitel</td>
						<td colspan="1"><?php echo $row["OtherTitles"] != "" ? $row["OtherTitles"] : "- / -"; ?></td>
						<td rowspan="9"><img src="img/cover.png"></td>
					</tr>
					<tr>
						<td colspan="2" id="title3">Originaltitel</td>
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
						<td colspan="2" id="title3">Link</td>
						<td colspan="1"><?php echo $row["Link"] != "" ? '<a href="' . $row["Link"] . '">' . $row["Link"] . "</a>" : "- / -"; ?></td>
					</tr>
					<tr>
						<td colspan="2" id="title3">Format</td>
						<td colspan="1"><?php echo $row["Medium"] . ($row["Resolution"] != "" ? ", " . $row["Resolution"] : ""); ?></td>
					</tr>
					<tr>
						<td colspan="2" id="title3">Spieldauer</td>
						<td colspan="1"><?php echo $row["Duration"] != "" ? $row["Duration"] . " min" : "unbekannt"; ?></td>
					</tr>
					<tr>
						<td colspan="2" id="title3">Dateiname</td>
						<td colspan="1"><?php echo $row["File"]; ?></td>
					</tr>
					<tr>
						<td colspan="4" id="spacer_large"></td>
					</tr>
					<?php if ($row["Director"] != "") { ?>
					<tr>
						<td colspan="1" id="title3">von</td>
						<td colspan="3"><?php echo $row["Director"]; ?></td>
					</tr>
					<?php } ?>
					<?php if ($row["Actors"] != "") { ?>
					<tr>
						<td colspan="1" id="title3">mit</td>
						<td colspan="3"><?php echo $row["Actors"]; ?></td>
					</tr>
					<?php } ?>
					<tr>
						<td colspan="4" id="spacer_large"></td>
					</tr>
					<tr>
						<td colspan="4" id="title2">Bewertung<br>
							<table border="0" style="margin-left: 0;">
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
			<td style="width:  2%">&nbsp;</td>
		</tr>
	</table>
</body>
</html>