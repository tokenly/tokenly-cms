<?php
namespace Core;

class Controller
{
	use \Traits\Modable, \Traits\Containerized;
	
	private $script = '';

	function __construct()
	{
		$this->load_container();
	}

	
}
