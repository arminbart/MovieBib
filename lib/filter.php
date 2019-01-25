<?php

include_once "lib/tools.php";

class Filter
{
	public $search = null;
	public $genre = null;


	private function __construct()
	{
	}

	public static function get()
	{
		$filter = new Filter();

		$filter->search = get_http_param("search");
		$filter->genre = get_http_param("genre");
		
		return $filter;
	}

	public function is_set($name, $value = null)
	{
		if ($name == "search")
			return Filter::test_param($this->search, $value);
		if ($name == "genre")
			return Filter::test_param($this->genre, $value);

		exit("Unknown filter type '$name'!");
	}

	public function param($add_name = null, $add_value = null)
	{
		$param = "";

		$param .= Filter::filter_param("search", $this->search, $add_name, $add_value);
		$param .= Filter::filter_param("genre", $this->genre, $add_name, $add_value);

		return $param;
	}

	public function where()
	{
		$where = new Where();

		Filter::add_where_part($where, "Genre", $this->genre);

		if ($this->search != "")
		{
			$subwhere = new Where(null, null, "OR");
			$subwhere->sub_where("Title LIKE '%" . str_replace("'", "''", $this->search) . "%' ");
			$subwhere->sub_where("Phonetic LIKE '%" . phonetic($this->search) . "%' ");
			$where->sub_where($subwhere);
		}

		return $where;
	}

	private static function add_where_part($where, $name, $value)
	{
		if ($value != "")
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
