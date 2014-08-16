<?php
class Slick_App_API_V1_Auth_Model extends Slick_App_Account_Home_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->api = true;
	}

	public static function getUser($data)
	{
		$model = new Slick_App_API_V1_Auth_Model;
		if(!isset($data['authKey'])){
			http_response_code(401);
			throw new Exception('Not logged in');
		}

		
		$get = $model->get('users', $data['authKey'], array('userId', 'lastActive'), 'auth');
		if(!$get){
			http_response_code(401);
			$model->logout($data);
			throw new Exception('Invalid authentication key');
		}
		
		$profModel = new Slick_App_Profile_User_Model;
		$getProf = $profModel->getUserProfile($get['userId'], $data['site']['siteId']);
		

		$activeTime = strtotime($get['lastActive']);
		$diff = time() - $activeTime;
		if($diff > 7200){ //2 hours
			//force logout
			$model->logout($data);
			http_response_code(401);
			throw new Exception('Authentication key expired');
		}
		
		$model->updateLastActive($get['userId']);
		
		
		return $getProf;	
		
	}
	
	public function logout($data)
	{
		if(!isset($data['authKey'])){
			throw new Exception('Not logged in');
		}
		else{

			$user = $this->get('users', $data['authKey'], array('userId', 'username', 'email', 'lastAuth', 'auth', 'regDate'), 'auth');
			if($user){
				$this->edit('users', $user['userId'], array('auth' => ''));
			}
			unset($_SESSION['accountAuth']);
			if(isset($_COOKIE['rememberAuth'])){
				setcookie('rememberAuth', '', time()-3600,'/');
			}
		}
		return true;
		
	}

}
