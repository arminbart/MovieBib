<?php

include_once 'api/apihelpers.php';
include_once 'db/dbstatements.php';

// Test:
// http://video.bartmail.de/api.php?file=Abyss_Abgrund_des_Todes_18.11.25_20-15_arte_165_TVOON_DE.mpg.HD.cut.mp4&lang=de&location=Filme%20Deutsch%5CAbenteuer

function add_video($con)
{
	$filename = get_http_param("file");
	$location = str_replace("\\", "/", get_http_param("location"));
	$lang = strtolower(get_http_param("lang"));
	$genre = strtolower(get_http_param("genre"));

	if ($filename == "" or $location == "" or $lang == "")
		throw new ApiException("To add a video, specify at least file, location and language.");

	if ($genre != "" and !genre_exists($genre))
		throw new ApiException("Invalid Genre '" . $genre . "'");

	$cut = is_cut($filename);
	$res = extract_resolution($filename);
	$type = extract_type($filename);
	$title = suggest_title($filename);

	if ($genre == "")
		$genre = extract_genre($con, $location);

	debug_out("name: " . $title);
	debug_out("filename: " . $filename);
	debug_out("newname: " . $name . "." . strtolower($type));
	debug_out("location: " . $location);
	debug_out("language: " . $lang);
	debug_out("cut: " . $cut);
	debug_out("type: " . $type);
	debug_out("resolution: " . $res);

	if ($con->value(new SelectStatement("Videos", "count(*)", new Where("File", $filename, "OR", "OrigFile", $filename))))
		throw new ApiException("Video '" . $filename . "' already added.", true);

	$stmt = new InsertStatement("Videos");
	$stmt->add_value("Title",			$title);
	$stmt->add_value("Lang",			$lang);
	$stmt->add_value("File",			$filename);
	$stmt->add_value("OrigFile",		$filename);
	$stmt->add_value("OrigLocation",	$location);
	$stmt->add_value("Cut",				$cut);
	$stmt->add_value("Medium",			$type);
	$stmt->add_value("Resolution",		$res);
	if ($genre != "")
		$stmt->add_value("Genre",		$genre);

	$con->execute($stmt);
	$id = $con->value(new SelectStatement("Videos", "max(ID)"));

	return "Added video $id: $title";
}

function suggest_title($filename)
{
	if (preg_match("/[0-9]{5}_/", substr($filename, 0, 6)))
		$title = substr($filename, 6);
	else
		$title = $filename;

	$recording = strpos_regex($title, "/_[0-9]{2}\.[0-9]{2}\.[0-9]{2}_[0-9]{2}-[0-9]{2}_/");
	if ($recording > 0)
		$title = substr($title, 0, $recording);
	else
		$title = substr($title, 0, strrpos($title, "."));

	return trim(str_replace("_", " ", $title));
}

function is_cut($filename)
{
	return stripos($filename, ".cut.") > 0;
}

function extract_resolution($filename)
{
	if (stripos($filename, ".hd.") > 0)
		return "HD";
	else
		return "";
}

function extract_type($filename)
{
	$types = explode(",", get_php_param("types"));
	$pos = strrpos($filename, ".");

	if ($pos > 0)
		$type = strtoupper(substr($filename, $pos + 1));
	if ($type == "" or !in_array($type, $types))
		throw new ApiException("Unsupported file type '" . $type . "' or invalid file name '" . $filename . "'.");

	return $type;
}

function extract_genre($con, $location)
{
	$pos = strrpos($location, "/");

	if ($pos > 0)
	{
		$genre = substr($location, $pos + 1);
		if (genre_exists($con, $genre))
			return $con->value(new SelectStatement("Genres", "ID", new Where("ID", $genre))); // Fetch Genre in correct upper-lower-case
		else if ($con->entry_exists("Genres", "Name", $genre))
			return $con->value(new SelectStatement("Genres", "ID", new Where("Name", $genre)));  // Fetch Genre by name
	}

	return null;
}

function genre_exists($con, $genre)
{
	return $con->entry_exists("Genres", "ID", $genre);
}

?>
