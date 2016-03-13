<?php
namespace App\Account;
use Core\ProxyModel;
class Auth_Model extends ProxyModel
{
	use \Traits\Driveable;
	use \Traits\Containerized;
	
	protected $driver_key = 'auth';
	protected $driver_type = 'model';
	
}
