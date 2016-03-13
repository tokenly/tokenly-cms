<?php
namespace App\Tokenly;
use Core\ProxyModel;
class Inventory_Model extends ProxyModel
{
	use \Traits\Driveable;
	use \Traits\Containerized;
	
	protected $driver_key = 'inventory';
	protected $driver_type = 'model';
	
}
