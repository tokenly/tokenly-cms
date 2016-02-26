<?php
namespace App\API\V1;
use App\Account, Util;
class Auth_Model extends Account\Home_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->api = true;
	}

	protected static function getUser($data)
	{
		$model = new Auth_Model;
		if(!isset($data['authKey'])){
			http_response_code(401);
			throw new \Exception('Not logged in');
		}

		$get = $model->checkSession($data['authKey']);
		if(!$get){
			http_response_code(401);
			$model->logout($data);
			throw new \Exception('Invalid authentication key');
		}
		
		$profModel = new \App\Profile\User_Model;
		$getProf = $profModel->getUserProfile($get['userId'], $data['site']['siteId']);
		
		$activeTime = strtotime($get['lastActive']);
		$diff = time() - $activeTime;
		if($diff > 259200){ //temp changed to 3 days
			//force logout
			$model->logout($data);
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
			$this->container->clearSession($data['authKey']);
			Util\Session::clear('accountAuth');
			if(isset($_COOKIE['rememberAuth'])){
				setcookie('rememberAuth', '', time()-3600,'/');
			}
		}
		return true;
	}
}
