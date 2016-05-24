<?php
namespace Drivers\Auth;
use App\ModControl, Util, App\Profile;
class Native_Controller extends ModControl implements \Interfaces\AuthController
{
	
		function __construct()
		{
			parent::__construct();
			$this->model = new Native_Model;
		}
		
		public function init()
		{
			$output = parent::init();
			$this->output = $output;
			
			if(isset($this->args[2])){
				switch($this->args[2]){
					case 'logout':
						$output = $this->logout();
						break;
					case 'register':
						$output = $this->register();
						break;
					case 'verify':
						$output = $this->verify();
						break;
					case 'reset':
						$output = $this->reset();
						break;
					default:
						$output['view'] = '404';
						break;
					
				}
			}
			else{
				$output = $this->login();
			}
			
			return $output;
		}
		
		public function login()
		{
			$output = $this->output;
			
			//check if already logged in, redirect to dash home if so
			if($this->data['user']){
				redirect(route('account.account-home'));
				return $output;
			}
			
			if(isset($_COOKIE['rememberAuth'])){
				$log = $this->container->logRemembered();
				if($log){
					die();
				}
			}			
			
			$output['form'] = $this->model->getLoginForm();		
			$output['title'] = 'Login';	
			$output['view'] = 'login';
			
			if(posted()){
				//attempt login
				$data = $output['form']->grabData();
				try{
					$login = $this->model->checkAuth($data);
				}
				catch(\Exception $e){
					$login = false;
					Util\Session::flash('message', $e->getMessage(), 'alert-danger');
				}
				if($login){
					redirect(route('account.account-home'));
				}
			}
			

			return $output;
		}
		
		public function logout()
		{
			$sesh_auth = Util\Session::get('accountAuth');
			if(!$sesh_auth){
				redirect($this->site.$this->data['app']['url']);
			}
			else{
				$this->model->clearSession($sesh_auth);
				Util\Session::clear('accountAuth');
				if(isset($_COOKIE['rememberAuth'])){
					setcookie('rememberAuth', '', time()-3600,'/');
				}
				if(isset($_GET['r'])){
					redirect($this->site.$_GET['r']);
				}
				else{
					redirect($this->site.$this->data['app']['url']);
				}
			}
		}
		
		public function register()
		{
			$output = $this->output;
			
			$output['form'] = $this->model->getRegisterForm();
			$output['view'] = 'register';
			$output['title'] = 'Register';
			if(posted()){
				$data = $output['form']->grabData();
				try{
					$register = $this->model->registerAccount($data);
					
				}
				catch(\Exception $e){
					Util\Session::flash('message', $e->getMessage(), 'alert-error');
					$register = false;
				}
				
				if($register){
					$output['view'] = 'register-success';
				}
			}
			
			return $output;
		}
		
		public function sync()
		{
			
			
		}
		
		public function verify()
		{
			$output = $this->output;
			if(!isset($this->args[3]) OR trim($this->args[3]) == ''){
				$output['view'] = '404';
				return $output;
			}
			
			$getUser = $this->model->get('users', $this->args[3], array(), 'activate_code');
			if(!$getUser OR $getUser['activated'] == 1){
				$output['view'] = '404';
				return $output;
			}
			
			$this->model->edit('users', $getUser['userId'], array('activate_code' => '', 'activated' => 1));
			
			$output['view'] = 'verify-success';
			$output['title'] = 'Account Verified!';
			
			return $output;
		}
		
		
		public function reset()
		{
			$output = $this->output;
			
			if($this->data['user']){
				redirect(route('account.account-home'));
			}
			
			if(isset($this->args[3])){
				return $this->container->completeReset();
			}
			
			$output['title'] = 'Reset Password';
			$output['form'] = $this->model->getResetForm();
			$output['view'] = 'reset-form';
			
			if(posted()){
				$data = $output['form']->grabData();
				try{
					$sendReset = $this->model->sendPasswordReset($data);
				}
				catch(\Exception $e){
					Util\Session::flash('message', $e->getMessage(), 'alert-danger');
					$sendReset = false;
				}
				
				if($sendReset){
					Util\Session::flash('message', 'Password reset sent!', 'text-success');
				}
			}
			
			return $output;
		}
		
		protected function completeReset()
		{
			$output = $this->output;
			
			if(!isset($this->args[3])){
				$output['view'] = '404';
				return $output;
			}
			
			$url = $this->args[3];
			$getLink = $this->model->get('reset_links', $url, array(), 'url');

			if(!$getLink){
				$output['view'] = '404';
				return $output;
			}
			
			$reqTime = strtotime($getLink['requestTime']);
			$timeDiff = time() - $reqTime;
			$threshold = 7200;
			if($timeDiff > $threshold){
				$this->model->delete('reset_links', $getLink['resetId']);
				$output['view'] = '404';
				return $output;
			}
			
			$output['title'] = 'Reset Password';
			$output['view'] = 'reset-complete';
			$output['form'] = $this->model->getPassResetForm();
			$output['message'] = '';
			$profModel = new Profile\User_Model;
			$output['user'] = $profModel->getUserProfile($getLink['userId']);
			
			if(posted()){
				$data = $output['form']->grabData();
				$data['userId'] = $getLink['userId'];
				$data['resetId'] = $getLink['resetId'];
				
				try{
					$update = $this->model->completePassChange($data);
				}
				catch(\Exception $e){
					Util\Session::flash('message', $e->getMessage(), 'alert-danger');
					$update = false;
				}
				
				if($update){
					$output['view'] = 'reset-success';
					return $output;
				}
			}
			
			return $output;
		}
	

    protected static function logRemembered()
    {	
		$model = new Native_Model;
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

}
