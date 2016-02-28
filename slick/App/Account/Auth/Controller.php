<?php
namespace App\Account;
use App\ModControl;

class Auth_Controller extends ModControl
{
	use \Traits\DriveableController;
	
	protected $driver_key = 'auth';
	
	
}
