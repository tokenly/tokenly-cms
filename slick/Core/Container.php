<?php
namespace Core;

class Container
{
	protected $obj = false;
	
	function __construct($obj)
	{
		$obj->container = -1;
		$this->obj = $obj;
	}
	
	function __call($method, $arguments)
	{
		return call_user_func_array(array($this->obj, $method), $arguments);
	}
	
	public function get_obj()
	{
		return $this->obj;
	}
	
}
