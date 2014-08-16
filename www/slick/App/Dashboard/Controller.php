<?php
class Slick_App_Dashboard_Controller extends Slick_App_AppControl
{
    function __construct()
    {
        parent::__construct();
        
        
    }
    
    public function init()
    {

		$output = parent::init();
		$output['title'] = $output['module']['name'];

		return $output;
    }
    
    
    
}
