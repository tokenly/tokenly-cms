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
		$data['isAPI'] = true;
		
		$register = $this->registerAccount($data);
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

?>
