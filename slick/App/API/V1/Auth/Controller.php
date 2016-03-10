<?php
namespace App\API\V1;
use \App\Account\Auth_Model as AccountAuth;
class Auth_Controller extends \Core\Controller
{
	public $methods = array('GET','POST');
	
	function __construct()
	{
		parent::__construct();
		$this->model = new Auth_Model;
		$this->auth_model = new AccountAuth;
	}
	
	protected function init($args = array())
	{
		$output = array();
		$this->args = $args;
		switch($this->useMethod){
			case 'POST':
				$output =  $this->container->authenticate();
				break;
			case 'GET':
				if(isset($this->args[1])){
					switch($this->args[1]){
						case 'logout':
							$output = $this->container->logout();
							break;
					}
				}
				else{
					$output = $this->container->getUser();
				}
				break;
			
		}
		return $output;
	}
	
	protected function authenticate()
	{
		$output = array();
		$model = $this->auth_model;
		if(isset($this->args['data']['force_native']) AND $this->args['data']['force_native']){
			//force the Auth\Native driver to be used, for legacy migration purposes
			$model = new \Drivers\Auth\Native_Model;
		}
		try{
			$this->args['data']['isAPI'] = true;
			$auth = $model->checkAuth($this->args['data']);
		}
		catch(\Exception $e){
			$output['error'] = $e->getMessage();
			return $output;
		}
		
		$output['result'] = $auth;
		return $output;
	}
	
	protected function getUser()
	{
		$output = array();
		$profModel = new \App\Profile\User_Model;
		
		try{
			$get = $this->model->getUser($this->args['data']);
		}
		catch(\Exception $e){
			$output['error'] = $e->getMessage();
			return $output;
		}
		$output['result'] = $get;
		return $output;
	}
	
	protected function logout()
	{
		$output = array();
		try{
			$logout = $this->model->logout($this->args['data']);
		}
		catch(\Exception $e){
			$output['error'] = $e->getMessage();
			return $output;
		}
		$output['result'] = 'Success';
		return $output;
	}
}
