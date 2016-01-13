<?php
namespace Traits;

trait Modable
{
	use Filterable, Hookable;
	
	function __call($method, $arguments)
	{
		$output = self::applyFilters($this, $method, $arguments);
		self::triggerHooks($this, $method, $output);
		return $output;
	}	
	
	public static function __callStatic($method, $arguments)
	{
		$class = get_called_class();
		$output =  self::applyFilters($class, $method, $arguments, true);
		self::triggerHooks($class, $method, $output);
		return $output;
	}		
}
