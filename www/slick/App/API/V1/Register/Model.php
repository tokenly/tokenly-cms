<?php
class Slick_App_API_V1_Register_Model extends Slick_App_Account_Home_Model
{
	function __construct()
	{
		parent::__construct();
		$this->api = true;
	}
	
	
	public function createAccount($data)
	{
		if(isset($data['authKey'])){
			throw new Exception('Cannot create an account while logged in');
		}
		
		$register = $this->registerAccount($data);
		if($register){
			$auth = new Slick_App_API_V1_Auth_Model;
			$getAuth = $this->get('users', $register, array('auth'));
			$getUser =  $auth->getUser(array('authKey' => $getAuth['auth'], 'site' => $data['site']));
			$getUser['auth'] = $getAuth['auth'];
			return $getUser;
		}
		return false;
	}
	

	
	
}

?>
