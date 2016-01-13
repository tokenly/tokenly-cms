<?php
namespace App\Ad;
class Controller extends \App\AppControl
{
    function __construct()
    {
        parent::__construct();
    }
    
    protected function init()
    {
		$output = parent::init();
		if(!$this->module){
			redirect($this->site['url']);
		}
		return $output;
    }  
}
