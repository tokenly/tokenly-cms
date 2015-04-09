<?php
class Slick_App_Ad_Controller extends Slick_App_AppControl
{
    function __construct()
    {
        parent::__construct();
    }
    
    public function init()
    {
		$output = parent::init();
		
		if(!$this->module){
			$this->redirect($this->site['url']);
			$output['view'] = '404';
		}
		
		return $output;
    }
    
    
    
}
