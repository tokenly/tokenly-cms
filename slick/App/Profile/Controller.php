<?php
namespace App\Profile;
class Controller extends \App\AppControl
{
    function __construct()
    {
        parent::__construct();
    }
    
    protected function init()
    {
		$output = parent::init();
		if(!$output['module']){
			$output['view'] = '404';
		}
		return $output;
    }
}
