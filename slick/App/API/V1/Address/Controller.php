<?php
namespace App\API\V1;
class Address_Controller extends \Core\Controller
{
	public $methods = array('GET','POST');
	
	function __construct()
	{
		parent::__construct();
		$this->model = new \App\Tokenly\Address_Model;
	}
	
	protected function init($args = array())
	{
		$this->args = $args;
		$output = array();
		
		try{
			$this->user = Auth_Model::getUser($this->args['data']);
		}
		catch(\Exception $e){
			http_response_code(403);
			$output['error'] = $e->getMessage();
			return $output;
		}		
		
		if(isset($args[1])){
			$methods = array('GET' => array('get'), 'POST' => array('submit','verify'));
			foreach($methods as $method => $points){
				if(in_array($args[1], $points)){
					if($this->useMethod != $method){
						http_response_code(400);
						$output['error'] = 'Invalid request method';
						$output['methods'] = array($method);
						return $output;
					}
				}
			}
			switch($args[1]){
				case 'get':
					$output = $this->container->getAddresses();
					break;
				case 'submit':
					$output = $this->container->submitAddress();
					break;
				case 'verify':
					$output = $this->container->verifyAddress();
					break;
				default:
					http_response_code(400);
					$output['error'] = 'Invalid Request';
					break;
				
			}
		}
		else{
			http_response_code(400);
			$output['error'] = 'Invalid Request';
		}	
		return $output;
	}
	
	protected function getAddresses()
	{		
		$output = array();

		$output['addresses'] = $this->model->getAll('coin_addresses', array('userId' => $this->user['userId']),
													array('addressId', 'type','address','submitDate','verified',
														  'isXCP', 'isPrimary', 'label', 'public'));
		
		return $output;
	}
	
	protected function submitAddress()
	{
		$output = array();
		
		$data = $this->args['data'];
		$data['userId'] = $this->user['userId'];
		if(!isset($data['type'])){
			$data['type'] = 'btc';
		}
		
		try{
			$add = $this->model->addAddress($data);
		}
		catch(\Exception $e){
			http_response_code(400);
			$output['error'] = $e->getMessage();
			return $output;
		}
		
		$output['result'] = 'success';
		$output['address'] = $add;
		try{
			$output['verify_address'] = $this->model->getDepositAddress($add, true);
			$output['secret_message'] = $this->model->getSecretMessage($add);
			$output['broadcast_text'] = $this->model->getBroadcastText($add);
		}
		catch(\Exception $e){
			http_response_code(400);
			$output = array('error' => $e->getMessage());
			return $output;
		}
		unset($output['address']['userId']);
		return $output;
	}
	
	protected function verifyAddress()
	{
		$output = array();
		
		if(!isset($this->args['data']['address'])){
			http_response_code(400);
			$output['error'] = 'Address required';
			return $output;
		}
		$validMethods = array('message', 'donate', 'broadcast');
		if(!isset($this->args['data']['method']) OR !in_array($this->args['data']['method'], $validMethods)){
			http_response_code(400);
			$output['error'] = 'Verification method required ('.join(', ',$validMethods).')';
			return $output;
		}
		
		$getAddress = $this->model->fetchSingle('SELECT * FROM coin_addresses where address = :address AND userId = :userId',
												array(':address' => $this->args['data']['address'], ':userId' => $this->user['userId']));
		if(!$getAddress){
			http_response_code(404);
			$output['error'] = 'Address not found';
			return $output;
		}
		
		switch($this->args['data']['method']){
			case 'message':
				if(!isset($this->args['data']['signature'])){
					http_response_code(400);
					$output['error'] = 'Signature required';
					return $output;
				}
				$check = $this->model->checkSecretMessage($getAddress, $this->args['data']['signature']);
				if(isset($check['error']) AND trim($check['error']) != ''){
					http_response_code(400);
					$output['error'] = $check['error'];
					return $output;
				}
				if($check['result'] == 'none'){
					http_response_code(400);
					$output['error'] = 'Could not verify signature';
					return $output;
				}
				$output['result'] = true;
				break;
			case 'donate':
				$check = $this->model->checkAddressPayment($getAddress);
				if(isset($check['error']) AND trim($check['error']) != ''){
					http_response_code(400);
					$output['error'] = $check['error'];
					return $output;
				}
				if($check['result'] == 'verified'){
					$output['result'] = true;
				}
				else{
					$output['result'] = false;
				}
				break;
			case 'broadcast':
				try{
					$check = $this->model->checkAddressBroadcast($getAddress);
				}
				catch(\Exception $e){
					http_response_code(400);
					$output['error'] = $e->getMessage();
					return $output;
				}
				$output['result'] = $check;
				break;
		}
		return $output;
	}
}
