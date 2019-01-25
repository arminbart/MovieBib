<?php

include_once 'db/dbstatements.php';
include_once 'lib/phonetic.php';

class Language
{
	public $id;
	public $name;
	public $short;

	public function __construct($id, $con = null)
	{
		$this->id = $id;
		$this->name = $id . "???";
		$this->short = $id . ".";

		if ($con != null)
		{
			$ps = $con->query(new SelectStatement("Languages", "*", new Where("ID", $id)));
			$result = $ps->get_result();
			if (($row = $result->fetch_assoc()) != null)
			{
				$this->name = $row["Name"];
				$this->short = $row["ShortName"];
			}
			else
				debug_out("Unknown language '$id'!");
			$result->close();
			$ps->close();
		}
	}
}

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
	public $lang;			// CHAR(2) -> class Language
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

		$ps = $con->query(new SelectStatement("Genres", "ID, Name", null, "ID"));
		$result = $ps->get_result();
		while (($row = $result->fetch_assoc()) != null)
			$this->genres[$row["ID"]] = $row["Name"];
		$result->close();
		$ps->close();

		$ps = $con->query(new SelectStatement("Videos", "*", new Where("ID", $this->id)));
		$result = $ps->get_result();
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
			$lang				= $row["Lang"];
			$this->year			= intval($row["Year"]) > 0 ? intval($row["Year"]) : null;
			$this->duration		= intval($row["Duration"]) > 0 ? intval($row["Duration"]) : null;
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
		$ps->close();

		$this->lang = new Language($lang, $con);

		$con->close();
	}

	function save()
	{
		$con = new Connection();
		$stmt = new UpdateStatement("Videos");

		$stmt->add_value("Status",			$this->status);
		$stmt->add_value("Rating",			$this->rating);
		$stmt->add_value("Title",			$this->title);
		$stmt->add_value("OrigTitle",		$this->origtitle);
		$stmt->add_value("OtherTitles",		$this->othertitles);
		$stmt->add_value("Predecessor",		$this->predecessor);
		$stmt->add_value("Successor",		$this->successor);
		$stmt->add_value("Country",			$this->country);
		$stmt->add_value("Genre",			$this->genre);
		$stmt->add_value("OtherGenres",		$this->othergenres);
		$stmt->add_value("Lang",			$this->lang->id);
		$stmt->add_value("Year",			$this->year);
		$stmt->add_value("Duration",		$this->duration);
		$stmt->add_value("Medium",			$this->medium);
		$stmt->add_value("Resolution",		$this->resolution);
		$stmt->add_value("Cut",				$this->cut);
		$stmt->add_value("File",			$this->file);
		$stmt->add_value("OrigFile",		$this->origfile);
		$stmt->add_value("OrigLocation",	$this->origlocation);
		$stmt->add_value("Info",			$this->info);
		$stmt->add_value("Link",			$this->link);
		$stmt->add_value("Trailer",			$this->trailer);
		$stmt->add_value("Director",		$this->director);
		$stmt->add_value("Actors",			$this->actors);
		$stmt->add_value("Phonetic",		phonetic($this->title));

		$stmt->set_where(new Where("ID",	 $this->id));

		$con->execute($stmt);
		$con->close();
	}

	function set_watched()
	{
		$watched = now();
		$watchcnt++;
		$con = new Connection();
		$stmt = new UpdateStatement("Videos");

		$stmt->add_value("Watched", $watched);
		$stmt->add_value("WatchCnt", $watchcnt);
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
			debug_out("Lang: "			. $this->lang->id);		// CHAR(2)
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
