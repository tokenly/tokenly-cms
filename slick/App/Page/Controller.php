<?php
namespace App\Page;
class Controller extends \App\AppControl
{
    function __construct()
    {
        parent::__construct();
    }
    
    protected function init()
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
