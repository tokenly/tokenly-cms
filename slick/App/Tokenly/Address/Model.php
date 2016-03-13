<?php
namespace App\Tokenly;
use Core\ProxyModel;
class Address_Model extends ProxyModel
{
	use \Traits\Driveable;
	
	protected $driver_key = 'coinAddress';
	protected $driver_type = 'model';


}
