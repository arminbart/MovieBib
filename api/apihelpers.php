<?php

class ApiException extends Exception
{
	private $warning;

	function __construct($msg, $warning = false)
	{
		parent::__construct($msg);

		$this->warning = $warning;
	}

	function isWarning()
	{
		return $this->warning;
	}
}

?>
