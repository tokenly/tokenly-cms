<?php
namespace Drivers\Auth;
use App\ModControl, Util;
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
			
			
		}
		
		public function sync()
		{
			
			
		}
	
	
}
