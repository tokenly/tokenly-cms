<?php
namespace App\Accountant;
class Controller extends App\AppControl
{
    function __construct()
    {
        parent::__construct();
    }
    
    public function init()
    {
		$output = parent::init();
		if(!$output['module']){
			$output['view'] = '404';
		}
		return $output;
    }    
}
