<?php
namespace App\Tokenly;
/*
 * @module-type = dashboard
 * @menu-label = Address Manager
 * 
 * */
class Address_Controller extends \App\ModControl
{
	use \Traits\DriveableController;
	
	protected $driver_key = 'coinAddress';
	

}
