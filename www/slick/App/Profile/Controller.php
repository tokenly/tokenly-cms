<?php
class Slick_App_Profile_Controller extends Slick_App_AppControl
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
			$output['title'] = '404 Page Not Found';
		}
		return $output;
    }
    
    
    
}
