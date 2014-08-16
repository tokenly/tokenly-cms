<?php
class Slick_App_Page_Controller extends Slick_App_AppControl
{
    function __construct()
    {
        parent::__construct();
        
        
    }
    
    public function init()
    {
		$output = parent::init();
		
		if(trim($this->args[0]) == ''){
			$this->module = false;
			$output['view'] = 'home';
			$output['title'] = 'Home';
			$output['template'] = 'home';

		}
		
		return $output;
    }
    
    
    
}
