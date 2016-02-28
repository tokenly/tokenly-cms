<?php
namespace App\Account;
use App\API\V1, Util;
class Home_Controller extends \App\ModControl
{	
    function __construct()
    {
        parent::__construct();
        $this->model = new Home_Model;
    }
    
    protected function init()
    {
		$output = parent::init();
		$sesh_auth = Util\Session::get('accountAuth');
		if(!$sesh_auth){
			if(isset($_COOKIE['rememberAuth'])){
				$log = $this->container->logRemembered();
				if($log){
					die();
				}
			}
			
			if(isset($this->args[1])){
				switch($this->args[1]){
					case 'verify';
						$output = $this->container->verifyAccount($output);
						break;
					case 'success':
						$output = $this->container->showSuccess($output);
						break;
					default:
						$output['view'] = '404';
						break;
				}
				return $output;
			}
			
			$output['view'] = 'auth';
			$output['loginForm'] = $this->model->getLoginForm();
			if(intval($this->data['app']['meta']['disableRegister']) === 1){
				$output['registerForm'] = false;
			}
			else{
				$output['registerForm'] = $this->model->getRegisterForm();
			}
			$output['loginMessage'] = '';
			$output['registerMessage'] = '';
			
			if(posted()){
				if(!isset($_POST['submit-type'])){
					die();
				}
				
				if($_POST['submit-type'] == 'login'){
					//dd($this->container);
					$output = $this->container->login($output);
				}
				elseif($output['registerForm']){
					$output = $this->container->register($output);
				}
			}
			
		}
		else{
			try{
				$userInfo = V1\Auth_Model::getUser(array('authKey' => $sesh_auth, 'site' => $this->data['site']));
			}
			catch(\Exception $e){
				redirect($this->site.$this->data['app']['url'].'/auth/logout');
			}
			if(isset($_REQUEST['r'])){
				redirect($this->site.$_GET['r']);
			}
			else{
				redirect(route('account.dash-home'));
			}			
		}
		$output['title'] = 'Log In';
		return $output;
    }
    
    protected function login($output)
    {
		$data = $output['loginForm']->grabData();
		$data['site'] = $this->data['site'];
		
		try{
			$login = $this->model->checkAuth($data);
		}
		catch(\Exception $e){
			$output['loginMessage'] = $e->getMessage();
			$login = false;
		}
		if($login){
			if(isset($_REQUEST['r'])){
				redirect($this->site.$_GET['r']);
			}
			else{
				redirect(route('account.dash-home'));
			}
		}
		return $output;
	}
	
	protected function register($output)
	{
		if(intval($this->data['app']['meta']['disableRegister']) == 1){
			return $output;
		}
		
		$data = $output['registerForm']->grabData();
		$data['site'] = $this->data['site']['domain'];
		
		try{
			$register = $this->model->registerAccount($data);
		}
		catch(\Exception $e){
			$output['registerMessage'] = $e->getMessage();
			$register = false;
		}
		
		if($register){
			redirect($this->site.$this->data['app']['url'].'/success');
		}
		
		return $output;
	}
    
    protected static function logRemembered()
    {	
		$model = new Home_Model;
		if(!isset($_COOKIE['rememberAuth'])){
			return false;
		}
		
		$auth = $_COOKIE['rememberAuth'];
		$expAuth = explode(':', $auth);
		if(count($expAuth) != 3){
			setcookie('rememberAuth', '', time()-3600, '/');
			return false;
		}
		$checksum = md5($expAuth[0].':'.$expAuth[1]);
		if($checksum != $expAuth[2]){
			setcookie('rememberAuth', '', time()-3600, '/');
			return false;
		}
		
		$decodeId = base64_decode($expAuth[1]);
		$get = $model->get('users', $decodeId);
		if(!$get){
			setcookie('rememberAuth', '', time()-3600, '/');
			return false;
		}
		
		$passHash = hash('sha256', $get['password'].$get['username']);
		if($passHash != $expAuth[0]){
			setcookie('rememberAuth', '', time()-3600, '/');
			return false;
		}

		$model->generateAuthToken($get['userId']);
		$url = $_SERVER['REQUEST_URI'];
		header('Location: '.$url);

		return true;
		
	}
    
    protected function verifyAccount($output)
    {
		if(!isset($this->args[2]) OR trim($this->args[2]) == ''){
			$output['view'] = '404';
			return $output;
		}
		
		$getUser = $this->model->get('users', $this->args[2], array(), 'activate_code');
		if(!$getUser OR $getUser['activated'] == 1){
			$output['view'] = '404';
			return $output;
		}
		
		$this->model->edit('users', $getUser['userId'], array('activate_code' => '', 'activated' => 1));
		
		$output['view'] = 'verify-success';
		
		return $output;
	}
	
	protected function showSuccess($output)
	{
		$output['view'] = 'register-success';
		$output['title'] = 'Register Account';
		
		return $output;
	}
}
