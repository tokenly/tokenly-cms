<?php
class Slick_App_Dashboard_LTBcoin_POT_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_Dashboard_LTBcoin_POT_Model;
		
	}
	
	public function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';
		$output['view'] = 'index';
		
		
		return $output;
		
	}
	
}
