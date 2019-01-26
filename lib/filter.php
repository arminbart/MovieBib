<?php

include_once "lib/tools.php";

class Filter
{
	// Standard filters
	public $search = null;
	public $genre = null;
	public $lang = null;

	// Special filters
	public $medium = null;
	public $res = null;
	public $status = null;


	private function __construct()
	{
	}

	public static function get()
	{
		$filter = new Filter();

		$filter->search = get_http_param("search");
		$filter->genre = get_http_param("genre");
		$filter->lang = get_http_param("lang");

		$filter->medium = get_http_param("medium");
		$filter->res = get_http_param("res");
		$filter->status = get_http_param("status");

		return $filter;
	}

	public function is_set($name, $value = null)
	{
		switch ($name)
		{
			case "search":
				return Filter::test_param($this->search, $value);
			case "genre":
				return Filter::test_param($this->genre, $value);
			case "lang":
				return Filter::test_param($this->lang, $value);
			case "medium":
				return Filter::test_param($this->medium, $value);
			case "res":
				return Filter::test_param($this->res, $value);
			case "status":
				return Filter::test_param($this->status, $value);
			default:
				exit("Unknown filter type '$name'!");
		}
	}

	public function special()
	{
		return boolval(get_http_param("special")) or ($this->medium . $this->res . $this->status) != "";	
	}

	public function param($add_name = null, $add_value = null)
	{
		$param = "";

		$param .= Filter::filter_param("search", $this->search, $add_name, $add_value);
		$param .= Filter::filter_param("genre", $this->genre, $add_name, $add_value);
		$param .= Filter::filter_param("lang", $this->lang, $add_name, $add_value);

		if ($add_name != "special" or $add_value == 1)
		{
			$param .= Filter::filter_param("medium", $this->medium, $add_name, $add_value);
			$param .= Filter::filter_param("res", $this->res, $add_name, $add_value);
			$param .= Filter::filter_param("status", $this->status, $add_name, $add_value);
		}

		$param .= Filter::filter_param("special", $add_name == "special" ? $add_value : (in_array($add_name, array("medium", "res", "status")) or $this->special() ? 1 : null), null, null);

		return $param;
	}

	public function where()
	{
		$where = new Where();

		Filter::add_where_part($where, "Genre", $this->genre);
		Filter::add_where_part($where, "Lang", $this->lang);

		if ($this->search != "")
		{
			$subwhere = new Where(null, null, "OR");
			$subwhere->sub_where("Title LIKE '%" . str_replace("'", "''", $this->search) . "%' ");
			$subwhere->sub_where("Phonetic LIKE '%" . phonetic($this->search) . "%' ");
			$where->sub_where($subwhere);
		}

		if ($this->medium == "File")
		{
			$types = explode(",", get_php_param("types"));
			$sql = "";
			foreach ($types as $type)
				$sql .= ($sql == "" ? "" : ", ") . "'$type'";
			$where->sub_where(Where::from_sql("Medium IN ($sql)"));
		}
		else
			Filter::add_where_part($where, "Medium", $this->medium);

		Filter::add_where_part($where, "Resolution", $this->res);
		Filter::add_where_part($where, "Status", $this->status);

		return $where;
	}

	private static function add_where_part($where, $name, $value)
	{
		$bounds = explode("-", $value);

		if (sizeof($bounds) > 1)
			$where->sub_where(Where::from_sql("$name BETWEEN '$bounds[0]' AND '$bounds[1]'"));
		else if ($value != "")
			$where->add_value($name, $value);
	}

	private static function filter_param($name, $value, $add_name, $add_value)
	{
		if ($name == $add_name)
			$value = $add_value;

		if ($value != "")
			return "&" . $name . "=" . $value;
		else
			return "";
	}

	private static function test_param($param, $value)
	{
		if ($value != "")
			return $param == $value;
		else
			return $param != "";
	}
}

?>
