<?php
namespace Traits;

trait Modable
{
	use Filterable, Hookable;
	
	function __call($method, $arguments)
	{
		$use = $this;
		if(isset($this->container) AND is_object($this->container)){
			$use = $this->container;
			if(method_exists($use, 'get_obj')){
				$use = $use->get_obj();
			}
		}
		if(!method_exists($use, $method)){
			dd(get_class($use).'\\'.$method.' cannot be found');
		}	
		$output = self::applyFilters($use, $method, $arguments);
		self::triggerHooks($use, $method, $output);
		return $output;
	}	
	
	public static function __callStatic($method, $arguments)
	{
		$class = get_called_class();	
		$output =  self::applyFilters($class, $method, $arguments, true);
		self::triggerHooks($class, $method, $output);
		return $output;
	}		
	
	public function apply_post_mods($method, $output, $args)
	{
		$output = self::applyPostFilters($this, $method, $output, $args);
		self::triggerHooks($this, $method, $output);
		return $output;
	}
	
	public function apply_pre_mods($method, $args)
	{
		$args = self::applyPreFilters($this, $method, $args);
		return $args;
	}
}
