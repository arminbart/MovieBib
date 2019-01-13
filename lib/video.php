<?php

include_once 'db/dbstatements.php';

class Video
{ //      [Status]              [set by MovieButler] [set by MovieBib]
	const enlisted		= 0; //			X
	const edited		= 1; //								X
	const duplicated	= 2; //			X					X			(usually skipped, unless duplicate found)
	const deduplicated	= 3; //								X
	const renamed		= 4; //			X
	const moved			= 5; //			X
	const error			= 6; //			X								(usually skipped, unless error occured; e.g. file not found)
	const nocover		= 7; //								X
	const incomplete	= 8; //								X
	const complete		= 9; //								X

	private $genres = array();

	private $id = null;		// INT UNSIGNED
	private $watched;		// TIMESTAMP
	private $watchcnt = 0;	// INT
	private $enlisted;		// TIMESTAMP

	// Types see also dbstructure.sql
	public $status;			// INT(1)
	public $rating;			// INT(1)
	public $title;			// TEXT
	public $origtitle;		// TEXT
	public $othertitles;	// TEXT
	public $predecessor;	// INT UNSIGNED
	public $successor;		// INT UNSIGNED
	public $country;		// VARCHAR(16)
	public $genre;			// VARCHAR(16)
	public $othergenres;	// TEXT
	public $lang;			// CHAR(2)
	public $year;			// INT(4)
	public $duration;		// INT(4)
	public $medium;			// VARCHAR(16)
	public $resolution;		// VARCHAR(16)
	public $cut;			// BOOLEAN NOT NULL DEFAULT FALSE
	public $file;			// TEXT
	public $origfile;		// TEXT
	public $origlocation;	// TEXT
	public $info;			// TEXT
	public $link;			// TEXT
	public $trailer;		// TEXT
	public $director;		// VARCHAR(64)
	public $actors;			// VARCHAR(1024)

	function __construct($id = null)
	{
		$this->id = $id;
		
		if ($id != null and $id != 0)
			$this->load();
	}

	function load()
	{
		$con = new Connection();

		$result = $con->query("SELECT ID, Name FROM Genres ORDER BY ID");
		while (($row = $result->fetch_assoc()) != null)
			$this->genres[$row["ID"]] = $row["Name"];
		$result->close();

		$result = $con->query("SELECT * FROM Videos WHERE ID = " . $this->id);
		$row = $result->fetch_assoc();

		if ($row == null)
		{
			exit("Loading video '" . $this->id . "' failed. Does the video exist in database?");
		}
		else
		{
			$this->watched		= $row["Watched"];
			$this->watchcnt		= intval($row["WatchCnt"]);
			$this->enlisted		= $row["Enlisted"];

			$this->status		= intval($row["Status"]);
			$this->rating		= intval($row["Rating"]);
			$this->title		= $row["Title"];
			$this->origtitle	= $row["OrigTitle"];
			$this->othertitles	= $row["OtherTitles"];
			$this->predecessor	= $row["Predecessor"];
			$this->successor	= $row["Successor"];
			$this->country		= $row["Country"];
			$this->genre		= $row["Genre"];
			$this->othergenres	= $row["OtherGenres"];
			$this->lang			= $row["Lang"];
			$this->year			= intval($row["Year"]);
			$this->duration		= intval($row["Duration"]);
			$this->medium		= $row["Medium"];
			$this->resolution	= $row["Resolution"];
			$this->cut			= boolval($row["Cut"]);
			$this->file			= $row["File"];
			$this->origfile		= $row["OrigFile"];
			$this->origlocation	= $row["OrigLocation"];
			$this->info			= $row["Info"];
			$this->link			= $row["Link"];
			$this->trailer		= $row["Trailer"];
			$this->director		= $row["Director"];
			$this->actors		= $row["Actors"];

			$this->debug_dump();
		}
		$result->close();

		$con->close();
	}

	function save()
	{
		$con = new Connection();
		$stmt = new UpdateStatement("Videos");

		$stmt->addValue("Status",		$this->status);
		$stmt->addValue("Rating",		$this->rating);
		$stmt->addValue("Title",		$this->title);
		$stmt->addValue("OrigTitle",	$this->origtitle);
		$stmt->addValue("OtherTitles",	$this->othertitles);
		$stmt->addValue("Predecessor",	$this->predecessor);
		$stmt->addValue("Successor",	$this->successor);
		$stmt->addValue("Country",		$this->country);
		$stmt->addValue("Genre",		$this->genre);
		$stmt->addValue("OtherGenres",	$this->othergenres);
		$stmt->addValue("Lang",			$this->lang);
		$stmt->addValue("Year",			$this->year);
		$stmt->addValue("Duration",		$this->duration);
		$stmt->addValue("Medium",		$this->medium);
		$stmt->addValue("Resolution",	$this->resolution);
		$stmt->addValue("Cut",			$this->cut);
		$stmt->addValue("File",			$this->file);
		$stmt->addValue("OrigFile",		$this->origfile);
		$stmt->addValue("OrigLocation",	$this->origlocation);
		$stmt->addValue("Info",			$this->info);
		$stmt->addValue("Link",			$this->link);
		$stmt->addValue("Trailer",		$this->trailer);
		$stmt->addValue("Director",		$this->director);
		$stmt->addValue("Actors",		$this->actors);

		$stmt->setWhere("ID = '" . $this->id . "'");

		$con->execute($stmt->stmt());
		$con->close();
	}

	function set_watched()
	{
		$watched = now();
		$watchcnt++;
		$con = new Connection();
		$stmt = new UpdateStatement("Videos");

		$stmt->addValue("Watched", $watched);
		$stmt->addValue("WatchCnt", $watchcnt);
		$con->execute($stmt->stmt());
		$con->close();
	}

	function genre_name()
	{
		return $genres[$this->genre];
	}

	function other_genre_names()
	{
		$gs = explode(",", $this->othergenres);
		$result = "";

		foreach ($gs as $g)
			$result .= ($result == "" ? "" : ", ") . $genres[$g];

		return $result;
	}

	function debug_dump()
	{
		if (debug())
		{
			debug_out("ID: "			. $this->id);			// INT UNSIGNED

			debug_out("Watched: "		. $this->watched);		// TIMESTAMP
			debug_out("WatchCnt: "		. $this->watchcnt);		// INT
			debug_out("Enlisted: "		. $this->enlisted);		// TIMESTAMP

			debug_out("Status: "		. $this->status);		// INT(1)
			debug_out("Rating: "		. $this->rating);		// INT(1)
			debug_out("Title: "			. $this->title);		// TEXT
			debug_out("OrigTitle: "		. $this->origtitle);	// TEXT
			debug_out("OtherTitles: "	. $this->othertitles);	// TEXT
			debug_out("Predecessor: "	. $this->predecessor);	// INT UNSIGNED
			debug_out("Successor: "		. $this->successor);	// INT UNSIGNED
			debug_out("Country: "		. $this->country);		// VARCHAR(16)
			debug_out("Genre: "			. $this->genre);		// VARCHAR(16)
			debug_out("OtherGenres: "	. $this->othergenres);	// TEXT
			debug_out("Lang: "			. $this->lang);			// CHAR(2)
			debug_out("Year: "			. $this->year);			// INT(4)
			debug_out("Duration: "		. $this->duration);		// INT(4)
			debug_out("Medium: "		. $this->medium);		// VARCHAR(16)
			debug_out("Resolution: "	. $this->resolution);	// VARCHAR(16)
			debug_out("Cut: "			. $this->cut);			// BOOLEAN
			debug_out("File: "			. $this->file);			// TEXT
			debug_out("OrigFile: "		. $this->origfile);		// TEXT
			debug_out("OrigLocation: "	. $this->origlocation);	// TEXT
			debug_out("Info: "			. $this->info);			// TEXT
			debug_out("Link: "			. $this->link);			// TEXT
			debug_out("Trailer: "		. $this->trailer);		// TEXT
			debug_out("Director: "		. $this->director);		// VARCHAR(64)
			debug_out("Actors: "		. $this->actors);		// VARCHAR(1024)
		}
	}
}

?>
