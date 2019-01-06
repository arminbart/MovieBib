<?php

include_once 'db/dbconnect.php';

function get_session($nick, $pass)
{
	$con = new Connection();
	$hash = md5($pass, false);

	$id = $con->value("SELECT ID FROM Users WHERE Name = '" . $nick . "' AND (Pwd = '" . $pass . "' OR Pwd = '" . $hash . "')");

	if ($id != "")
	{
		$session = 0;
		while ($session == 0 or $con->value("SELECT count(*) FROM Users WHERE Session = '" . $session . "'") > 0)
			$session = mt_rand(100000, 999999);
		$con->execute("UPDATE Users SET Login = now(), Session = " . $session . " WHERE ID = " . $id);
	}
	else
	{
		debug_out("Wrong user nickname or password.");
	}

	$con->close();
	return $session;
}

function verify_session($session)
{
	$nick = null;

	if ($session != "")
	{
		$con = new Connection();
		$result = $con->query("SELECT * FROM Users WHERE Session = '" . $session . "'");

		if (($row = $result->fetch_assoc()) != null)
		{
			$time = $row["Login"];
			debug_out("Verify session of user " . $row["ID"] . ". Last login = " . $time);
			$nick = $row["Name"];
		}
		
		$result->close();
		$con->close();
	}

	return $nick;
}

?>
