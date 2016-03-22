<?php
namespace Util;

class Driver
{
	protected $driver = false;
	protected $vehicle = false;
	protected $this_class = false;
	
	function __construct($vehicle, $this_class = false)
	{
		$drivers = $this->loadDrivers();
		$this->vehicle = $vehicle;
		if(isset($drivers[$vehicle])){
			$this->driver = $drivers[$vehicle];
			$this->this_class = $this_class;
		}
	}
	
	public function load($type = 'controller', $passables = array())
	{
		if($this->driver){
			$class = 'Drivers\\'.ucfirst($this->vehicle).'\\'.ucfirst($this->driver).'_'.ucfirst($type);
			$init = new $class;
			
			if($this->this_class){
				$passables = array_merge($passables, array('data', 'args', 'site', 'itemId', 'moduleUrl', 'module', 'app', 'user'));
				foreach($passables as $passable){
					if(isset($this->this_class->$passable)){
						$init->$passable = $this->this_class->$passable;
					}
				}
			}
			return $init;
		}
		throw new \Exception('No valid driver to load for '.$this->vehicle.'/'.$type);
	}
	
	protected function loadDrivers()
	{
		$get = static_cache('driver_list');
		if(!$get){
			$get = static_cache('driver_list', $this->loadDriverList());
		}
		return $get;
	}
	
	protected function loadDriverList()
	{
		$file = SITE_BASE.'/conf/drivers.php';
		if(!file_exists($file)){
			return array();
		}
		$get = include($file);
		return $get;
	}
	
	public static function driverName($key)
	{
		$util = new \Util\Driver($key);
		$drivers = $util->loadDrivers();
		if(!isset($drivers[$key])){
			return false;
		}
		return $drivers[$key];
	}
	
	
}
