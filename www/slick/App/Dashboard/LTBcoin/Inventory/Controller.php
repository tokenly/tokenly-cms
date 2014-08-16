<?php
class Slick_App_Dashboard_LTBcoin_Inventory_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_Dashboard_LTBcoin_Inventory_Model;
	}
	
	public function init()
	{
		$output = parent::init();
		$output['view'] = 'index';
		$output['template'] = 'admin';
		
		$output['grouped'] = false;
		if(isset($_GET['grouped']) AND $_GET['grouped'] == 1){
			$output['grouped'] = true;
		}
		
		$output['addressBalances'] = $this->model->getUserBalances($this->data['user']['userId'], $output['grouped']);
		
		return $output;
	}
	
}
