<?php

include_once 'db/dbconnect.php';

function get_session($nick, $pass)
{
	if ($pass == "")
	{
		debug_out("Password empty.");
		return null;
	}

	$con = new Connection();
	$hash = md5($pass, false);

	$where = new Where("Name", $nick);
	$where->sub_where(new Where("Pwd", $pass, "OR", "Pwd", $hash));
	$id = $con->value(new SelectStatement("Users", "ID", $where));

	if ($id != "")
	{
		$session = 0;
		while ($session == 0 or $con->value(new SelectStatement("Users", "count(*)", new Where("Session", $session))) > 0)
			$session = mt_rand(100000, 999999);

		$stmt = new UpdateStatement("Users");
		$stmt->add_value("Login", "now()", true);
		$stmt->add_value("Session", $session);
		$stmt->set_where(new Where("ID", $id));
		$con->execute($stmt);
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

	if (intval($session) > 0)
	{
		$con = new Connection();
		$ps = $con->query(new SelectStatement("Users", "*", new Where("Session", $session)));
		$result = $ps->get_result();

		if (($row = $result->fetch_assoc()) != null)
		{
			$time = $row["Login"];
			debug_out("Verify session of user " . $row["ID"] . ". Last login = " . $time);
			$nick = $row["Name"];
		}

		$result->close();
		$ps->close();
		$con->close();
	}

	return $nick;
}

function session_param($nick, $session, $id = 0)
{
	return "?s=" . ($nick != null ? intval($session) : 0) . ($id != 0 ? "&id=" . $id : ""); 
}

?>
