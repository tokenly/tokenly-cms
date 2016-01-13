<?php
namespace App\API\V1;
class Register_Model extends \App\Account\Home_Model
{
	function __construct()
	{
		parent::__construct();
		$this->api = true;
	}
	
	protected function createAccount($data)
	{
		if(isset($data['authKey'])){
			throw new \Exception('Cannot create an account while logged in');
		}
		$data['isAPI'] = true;
		$register = $this->container->registerAccount($data);
		if($register){
			http_response_code(201);
			$output = array();
			$output['result'] = 'success';
			$output['message'] = 'Please check your email to activate your account';
			return $output;
		}
		return false;
	}
}
