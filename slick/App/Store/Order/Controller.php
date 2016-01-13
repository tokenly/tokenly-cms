<?php
namespace App\Store;
use Core;
/*
 * @module-type = dashboard
 * @menu-label = Orders
 * 
 * */
class Order_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Core\Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';
		
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'delete':
					$output = $this->container->deleteOrder($output);
					break;
				default:
					$output['view'] = '404';
					break;
			}
			return $output;
		}
		
		$output['view'] = 'list';
		$output['orders'] = $this->model->getAll('payment_order', array(), array(), 'orderId', 'desc');
		foreach($output['orders'] as &$row){
			$row['orderData'] = json_decode($row['orderData'], true);
		}
		
		return $output;
	}
	
	protected function deleteOrder($output)
	{
		if(isset($this->args[3])){
			$getOrder = $this->model->get('payment_order', $this->args[3]);
			if($getOrder){
				$delete = $this->model->delete('payment_order', $this->args[3]);
			}
		}
		redirect($this->site.$this->moduleUrl);
	}
	
}
