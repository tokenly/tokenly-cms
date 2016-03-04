<?php
namespace Traits;

trait Containerized
{
	public $container = false;
	public static $container_class = false;
	
	public function get_container()
	{
		return $this->container;
	}
	
	public function load_container()
	{
		if(!is_object($this->container)){
			if($this->container != -1){
				$this->container = new \Core\Container($this);
				$obj = $this->container->get_obj();
			}
		}
		else{
			$obj = $this->container;
		}
		self::$container_class = get_class($obj);
	}

	
}
