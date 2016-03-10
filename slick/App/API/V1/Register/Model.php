<?php
namespace App\API\V1;
use Core\Model, Util, App\Account\Auth_Model as AccountAuth;
class Register_Model extends Model
{
	function __construct()
	{
		parent::__construct();
		$this->api = true;
		$this->auth_model = new AccountAuth;
	}
	
	protected function createAccount($data)
	{
		if(isset($data['authKey'])){
			throw new \Exception('Cannot create an account while logged in');
		}
		$data['isAPI'] = true;
		$register = $this->auth_model->registerAccount($data);
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
