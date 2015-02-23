<?php
class Slick_App_Dashboard_Store_Collector_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		$this->model = new Slick_App_Dashboard_Store_Collector_Model;
	}
	
	public function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';
		$output['view'] = 'index';
		$output['server_balance'] = $this->model->getServerBalance();
		$output['fuel_info'] = $this->model->getFuelInfo();
		$output['select_form'] = $this->model->getSelectionForm();
		$output['collect_form'] = $this->model->getCollectionForm();
		$output['option_selected'] = false;
		$output['payments'] = false;
		$output['collections'] = false;
		
		if(posted()){
			$data = $output['collect_form']->grabData();
			$data['payments'] = $_POST['payments'];
			
			try{
				$collect = $this->model->collectPayments($data, $this->data);
			}
			catch(Exception $e){
				Slick_Util_Session::flash('collector-message', $e->getMessage(), 'error');
				$collect = false;
			}
			
			if($collect){
				Slick_Util_Session::flash('collector-message', 'Payments collected and sent to <a href="https://blockchain.info/address/'.$data['address'].'" target="_blank">'.$data['address'].'</a>', 'success');
			}
			$this->redirect($this->site.$this->moduleUrl);
			die();
		}
		
		if(isset($_GET['option'])){
			$valid = $this->model->getValidOptions();
			if(in_array($_GET['option'], $valid)){
				$output['collect_form']->setValues(array('type' => $_GET['option']));
				
				$output['option_selected'] = $_GET['option'];
				try{
					$output['payments'] = $this->model->getPaymentsList($_GET['option']);
				}
				catch(Exception $e){
					$output['payments'] = array();
					Slick_Util_Session::flash('collector-message', $e->getMessage(), 'error');
				}
			}
		}
		else{
			$output['collections'] = $this->model->getAll('payment_collections', array(), array(), 'collectionId', 'desc');
			foreach($output['collections'] as &$collection){
				$collection['user'] = $this->model->get('users', $collection['userId'], array('userId', 'username', 'slug'));
			}
		}
		return $output;
	}
	
	
}
