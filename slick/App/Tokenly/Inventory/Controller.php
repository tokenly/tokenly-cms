<?php
namespace App\Tokenly;
/*
 * @module-type = dashboard
 * @menu-label = Inventory
 * 
 * */
class Inventory_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Inventory_Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';	
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'transactions':
					$output = $this->container->showInventoryTransactions($output);
					break;
				default:
					$output['view'] = '404';
			}
			return $output;
		}
		$output['view'] = 'index';
		$output['grouped'] = false;
		if(isset($_GET['grouped']) AND $_GET['grouped'] == 1){
			$output['grouped'] = true;
		}
		
		$forceRefresh = false;
		if(posted() AND isset($_POST['forceRefresh'])){
			$forceRefresh = true;
		}
		
		$output['addressBalances'] = $this->model->getUserBalances($this->data['user']['userId'], $output['grouped'], 'btc', $forceRefresh);
		
		return $output;
	}
	
	protected function showInventoryTransactions($output)
	{
		$output['transactions'] = $this->model->getUserInventoryTransactions($this->data['user']['userId']);
		$output['view'] = 'transactions';
		
		$output['last_address_update'] = false;
		if(isset($this->data['user']['meta']['address_tx_update'])){
			$output['last_address_update'] = intval($this->data['user']['meta']['address_tx_update']);
		}
		
		return $output;
	}
}
