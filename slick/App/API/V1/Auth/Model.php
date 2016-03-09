<?php
namespace App\API\V1;
use Core\Model, Util, App\Account\Auth_Model as AccountAuth;
class Auth_Model extends Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->api = true;
		$this->auth_model = new AccountAuth;
	}

	protected static function getUser($data)
	{
		$model = new AccountAuth;
		$api_model = new Auth_Model;
		if(!isset($data['authKey'])){
			http_response_code(401);
			throw new \Exception('Not logged in');
		}

		$get = $model->checkSession($data['authKey']);
		if(!$get){
			http_response_code(401);
			$api_model->logout($data);
			throw new \Exception('Invalid authentication key');
		}
		
		$profModel = new \App\Profile\User_Model;
		$getProf = $profModel->getUserProfile($get['userId'], $data['site']['siteId']);
		
		$activeTime = strtotime($get['lastActive']);
		$diff = time() - $activeTime;
		if($diff > 259200){ //temp changed to 3 days
			//force logout
			$api_model->logout($data);
			http_response_code(401);
			throw new \Exception('Authentication key expired');
		}
		
		$model->updateLastActive($get['userId']);
		
		return $getProf;	
	}
	
	protected function logout($data)
	{
		if(!isset($data['authKey'])){
			throw new \Exception('Not logged in');
		}
		else{
			$this->auth_model->clearSession($data['authKey']);
		}
		return true;
	}
}
