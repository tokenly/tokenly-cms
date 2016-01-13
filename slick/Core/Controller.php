<?php
namespace Core;

class Controller
{
	use \Traits\Modable;
	
	private $script = '';
	protected $container = false;

	function __construct()
	{
		$this->container = new Container($this);
	}
	

}
