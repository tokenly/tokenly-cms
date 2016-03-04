<?php
namespace Core;

class ProxyModel
{
	use \Traits\Containerized;
	
	function __construct()
	{
		if(method_exists($this, 'load_driver')){
			$this->load_driver('model');
		}
		$this->load_container();
	}
	
	
	function __call($method, $arguments)
	{
		$use = $this;
		if(isset($this->container) AND is_object($this->container)){
			$use = $this->container;
			if(method_exists($use, 'get_obj')){
				$use = $use->get_obj();
			}
		}
		$output = call_user_func_array(array($use, $method), $arguments);
		return $output;
	}
	
	public static function __callStatic($method, $arguments)
	{
		$use = get_called_class();
		if(isset($use::$container_class)){
			$class = $use::$container_class;
			if(!$class){
				$init = new $use;
			}
			$use = $use::$container_class;
		}
		$method = '\\'.$use.'::'.$method;
		
		$output = call_user_func_array($method, $arguments);
		return $output;
	}	

	
}
