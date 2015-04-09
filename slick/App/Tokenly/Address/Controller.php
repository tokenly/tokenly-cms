<?php
/*
 * @module-type = dashboard
 * @menu-label = Address Manager
 * 
 * */
class Slick_App_Tokenly_Address_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_Tokenly_Address_Model;
	}
	
	public function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';
		
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'verify':
					$output = $this->verifyAddress($output);
					break;
				case 'edit':
					$output = $this->editAddress($output);
					break;
				case 'delete':
					$output = $this->deleteAddress($output);
					break;
				case 'checkPayment':
					$output = $this->checkPayment($output);
					break;
				case 'checkMessage':
					$output = $this->checkMessage($output);
					break;
				default:
					$output = $this->showAddresses($output);
					break;
			}
		}
		else{
			$output = $this->showAddresses($output);
		}

		return $output;
	}
		
	private function showAddresses($output)
	{
		$output['view'] = 'index';
		$output['form'] = $this->model->getAddressForm();
		$output['addresses'] = $this->model->getAll('coin_addresses', array('userId' => $this->data['user']['userId']));
		$output['message'] = '';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['userId'] = $this->data['user']['userId'];
			if(!isset($data['type']) OR trim($data['type']) == ''){
				$data['type'] = 'btc';
			}
			
			try{
				$add = $this->model->addAddress($data);
			}
			catch(Exception $e){
				$output['message'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				$this->redirect($this->site.'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/verify/'.$data['address']);
			}
		}
		
		return $output;
	}
	
	private function verifyAddress($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $ouput;
		}
		$getAddress = $this->model->getAll('coin_addresses', array('address' => $this->args[3], 'userId' => $this->data['user']['userId']));
		if(!$getAddress OR count($getAddress) == 0){
			$output['view'] = '404';
			return $ouput;
		}
		$getAddress = $getAddress[0];
		
		if($getAddress['verified'] != 0){
			$this->redirect($this->site.'/'.$this->data['app']['url'].'/'.$this->data['module']['url']);
			return $output;
		}
		
		
		$output['view'] = 'verify';
		$output['address'] = $getAddress;
		$output['depositAddress'] = $this->model->getDepositAddress($getAddress);
		$output['secretMessage'] = $this->model->getSecretMessage($getAddress);
		
		
		return $output;
	}
	
	private function editAddress($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $ouput;
		}
		$getAddress = $this->model->getAll('coin_addresses', array('address' => $this->args[3], 'userId' => $this->data['user']['userId']));
		if(!$getAddress OR count($getAddress) == 0){
			$output['view'] = '404';
			return $ouput;
		}
		$getAddress = $getAddress[0];
		
		$output['form'] = $this->model->getAddressForm();
		$output['form']->remove('address');
		$output['address'] = $getAddress;
		$output['view'] = 'edit';
		$output['message'] = '';
		
		if(posted()){
			$data = $output['form']->grabData();
			
			try{
				$edit = $this->model->editAddress($getAddress['addressId'], $data);
			}
			catch(Exception $e){
				$output['message'] = $e->getMessage();
				$edit = false;
			}
			
			if($edit){
				$this->redirect($this->site.'/'.$this->data['app']['url'].'/'.$this->data['module']['url']);
				return $output;
			}
			
		}
		$output['form']->setValues($getAddress);
		
		
		return $output;
	}
	
	private function deleteAddress($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $ouput;
		}
		$getAddress = $this->model->getAll('coin_addresses', array('address' => $this->args[3], 'userId' => $this->data['user']['userId']));
		if(!$getAddress OR count($getAddress) == 0){
			$output['view'] = '404';
			return $ouput;
		}
		$getAddress = $getAddress[0];
		
		$delete = $this->model->delete('coin_addresses', $getAddress['addressId']);
		
		if($delete){
			$getVal = $this->model->fetchSingle('SELECT * FROM user_profileVals WHERE userId = :userId AND fieldId = :fieldId',
										array(':userId' => $this->data['user']['userId'], ':fieldId' => PRIMARY_TOKEN_FIELD));
			if($getVal AND $getVal['value'] == $getAddress['address']){
				$this->model->edit('user_profileVals', $getVal['profileValId'], array('value' => ''));
			}	
		}
		
		$this->redirect($this->site.'/'.$this->data['app']['url'].'/'.$this->data['module']['url']);
		
		return $output;
	}
	
	private function checkPayment($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $ouput;
		}
		$getAddress = $this->model->getAll('coin_addresses', array('address' => $this->args[3], 'userId' => $this->data['user']['userId']));
		if(!$getAddress OR count($getAddress) == 0){
			$output['view'] = '404';
			return $ouput;
		}
		$getAddress = $getAddress[0];
		
		$check = $this->model->checkAddressPayment($getAddress);
		ob_end_clean();
		header('Content-Type: application/json');
		echo json_encode($check);
		die();
	}
	
	private function checkMessage($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $ouput;
		}
		$getAddress = $this->model->getAll('coin_addresses', array('address' => $this->args[3], 'userId' => $this->data['user']['userId']));
		if(!$getAddress OR count($getAddress) == 0){
			$output['view'] = '404';
			return $ouput;
		}
		$getAddress = $getAddress[0];
		
		$check = $this->model->checkSecretMessage($getAddress);
		ob_end_clean();
		header('Content-Type: application/json');
		echo json_encode($check);
		die();
	}	
	
}
