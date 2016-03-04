<?php
namespace Traits;
use Util\Driver, Exception;
trait DriveableController
{
	use Driveable;
	
	public function init()
	{
		$this->load_driver();
		if(method_exists($this->driver, 'init')){
			return $this->driver->init();
		}
	}	
}
