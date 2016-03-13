<?php
namespace App\Tokenly;
/*
 * @module-type = dashboard
 * @menu-label = Address Manager
 * 
 * */
class Address_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Address_Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';
		
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'verify':
					$output = $this->container->verifyAddress($output);
					break;
				case 'edit':
					$output = $this->container->editAddress($output);
					break;
				case 'delete':
					$output = $this->container->deleteAddress($output);
					break;
				case 'checkPayment':
					$output = $this->container->checkPayment($output);
					break;
				case 'checkMessage':
					$output = $this->container->checkMessage($output);
					break;
				case 'checkBroadcast':
					$output = $this->container->checkBroadcast($output);
					break;
				default:
					$output = $this->container->showAddresses($output);
					break;
			}
		}
		else{
			$output = $this->container->showAddresses($output);
		}

		return $output;
	}
		
	protected function showAddresses($output)
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
			catch(\Exception $e){
				$output['message'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url'].'/verify/'.$data['address']);
			}
		}
		
		return $output;
	}
	
	protected function verifyAddress($output)
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
			redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url']);
		}
		
		$output['view'] = 'verify';
		$output['unverifiable'] = $this->model->checkAddressUnverifiable($getAddress);
		$output['address'] = $getAddress;
		$output['depositAddress'] = $this->model->getDepositAddress($getAddress);
		$output['secretMessage'] = $this->model->getSecretMessage($getAddress);
		$output['broadcastMessage'] = $this->model->getBroadcastText($getAddress);
		
		
		return $output;
	}
	
	protected function editAddress($output)
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
			catch(\Exception $e){
				$output['message'] = $e->getMessage();
				$edit = false;
			}
			
			if($edit){
				redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url']);
			}
			
		}
		$output['form']->setValues($getAddress);
		return $output;
	}
	
	protected function deleteAddress($output)
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
		
		redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url']);
	}
	
	protected function checkPayment($output)
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
		$unverifiable = $this->model->checkAddressUnverifiable($getAddress);
		if(!$unverifiable){
			$check = $this->model->checkAddressPayment($getAddress);
			ob_end_clean();
			header('Content-Type: application/json');
			echo json_encode($check);
		}
		die();
	}
	
	protected function checkMessage($output)
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
	
	protected function checkBroadcast($output)
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
		$json = array('error' => null);
		try{
			$check = $this->model->checkAddressBroadcast($getAddress);
		}
		catch(\Exception $e){
			http_response_code(400);
			$json['error'] = $e->getMessage();
			$check = false;
		}
		$json['result'] = $check;
		ob_end_clean();
		header('Content-Type: application/json');
		echo json_encode($json);
		die();
	}
}

