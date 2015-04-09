<?php
/*
 * @module-type = dashboard
 * @menu-label = Orders
 * 
 * */
class Slick_App_Store_Order_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_Core_Model;
	}
	
	public function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';
		
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'delete':
					$output = $this->deleteOrder($output);
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
	
	private function deleteOrder($output)
	{
		if(!isset($this->args[3])){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$getOrder = $this->model->get('payment_order', $this->args[3]);
		if(!$getOrder){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$delete = $this->model->delete('payment_order', $this->args[3]);
		$this->redirect($this->site.'/'.$this->moduleUrl);
		return true;
	}
	
}
