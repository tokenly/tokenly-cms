<?php
namespace App\TokenItem;
/*
 * @module-type = dashboard
 * @menu-label = Item Inventory
 * 
 * */
class Inventory_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Inventory_Model;
		$this->inv = new \App\Tokenly\Inventory_Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';	
		$output['view'] = 'index';
		$output['token_items'] = $this->model->getUserItems();
		$output['balances'] = $this->inv->getUserBalances($this->data['user']['userId'], true);

		return $output;
	}

}
