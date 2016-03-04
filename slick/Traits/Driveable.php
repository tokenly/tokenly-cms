<?php
namespace Traits;
use Exception, Util\Driver;
trait Driveable
{
	
	public function load_driver($type = 'controller')
	{
		if(!isset($this->driver_key)){
			throw new Exception('No driver key set for this module');
		}
		$driver = new Driver($this->driver_key, $this);		
		$this->driver = $driver->load($type);
		if(isset($this->container)){
			$this->container = $this->driver;
		}			
	}
	
	
	
}
