<?php
class Slick_App_CMS_Controller extends Slick_App_AppControl
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
    
