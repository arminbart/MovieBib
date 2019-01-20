<?php

include_once 'db/dbconnect.php';

class Session
{
	private $session;
	private $nick;

	private function __construct($session)
	{
		$this->nick = Session::verify($session);
		$this->session = $this->nick != "" ? $session : null;
	}

	public static function get()
	{
		return new Session(get_php_param("s"));
	}

	public static function login()
	{
		$nick = get_http_param("nick");
		$pass = get_http_param("pass");

		if ($nick == "" or $pass == "")
		{
			debug_out("User or password empty.");
			return new Session(null);
		}

		$session = 0;
		$con = new Connection();
		$hash = md5($pass, false);

		$where = new Where("Name", $nick);
		$where->sub_where(new Where("Pwd", $pass, "OR", "Pwd", $hash));
		$id = $con->value(new SelectStatement("Users", "ID", $where));

		if ($id != "")
		{
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
		return new Session($session);
	}

	public static function verify($session)
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
				$nick = $row["Name"];
				debug_out("Verify session of user " . $row["ID"] . " ($nick). Last login = $time");
			}

			$result->close();
			$ps->close();
			$con->close();
		}

		return $nick;
	}

	public static function session_param($session_id)
	{
		return "?s=" . ($session_id == "" ? "0" : $session_id);
	}

	public function valid()
	{
		return intval($this->session) != 0;
	}

	public function param($id = 0)
	{
		return Session::session_param($this->session) . ($id != 0 ? "&id=" . $id : ""); 
	}

	public function nick()
	{
		return $this->nick;
	}
}

?>
