<?php
namespace Drivers\Auth;
use App\ModControl, Util, App\Profile, App\Meta_Model;
class Tokenpass_Controller extends ModControl implements \Interfaces\AuthController
{
	
	function __construct()
	{
		parent::__construct();
		$this->model = new Tokenpass_Model;
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
					case 'callback':
						$output = $this->container->callback();
						break;
					case 'sync':
						$output = $this->sync();
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
		if($this->data['user']){
			redirect(route('account.account-home'));
			return $output;
		}
		
		$url = $this->model->getAuthUrl();
		redirect($url);
		return $output;
	}

	public function logout()
	{
		$sesh_auth = Util\Session::get('accountAuth');
		if(!$sesh_auth){
			redirect(route('account.auth'));
		}
		else{
			$this->model->clearSession($sesh_auth);
			if(isset($_GET['r'])){
				redirect($this->site.$_GET['r']);
			}
			else{
				redirect($this->site);
			}
		}
	}
	
	public function register()
	{
		redirect(TOKENPASS_URL.'/auth/register?redirect_uri='.$this->site);
		die();
	}
	
	public function sync()
	{
		$user = $this->data['user'];
		if($user){
			if(class_exists('\\App\\Tokenly\\Address_Model')){
				//sync coin addresses
				$this->model->syncAddresses($user);
			}
		}
		if(isset($_GET['r'])){
			redirect(urldecode($_GET['r']));
		}
		else{
			redirect(route('account.account-home'));
		}
		die();
	}
	
	public function verify()
	{
		//no email verification function needed, handled by TokenPass
		$output = $this->output;
		$output['view'] = '404';
		return $output;
	}
	
	public function reset()
	{
		//no reset function needed, handled by TokenPass
		$output = $this->output;
		$output['view'] = '404';
		return $output;
	}
	
	protected function callback()
	{
		$output = $this->output;
		$output['title'] = 'Login';
		
		if(isset($_GET['error']) AND isset($_GET['error_description'])){
			Util\Session::flash('message', urldecode($_GET['error_description']), 'alert-danger');
			$output['view'] = 'auth-error';
			return $output;
		}
		
		if(!isset($_GET['code'])){
			Util\Session::flash('message', 'Code not set', 'alert-danger');
			$output['view'] = 'auth-error';
			return $output;
		}
		
		//check that their state is still valid
		$used_state = $this->model->getState();
		$this_state = false;
		if(isset($_GET['state'])){
			$this_state = $_GET['state'];
		}
		if(!$used_state OR $this_state != $used_state){
			Util\Session::flash('message', 'Invalid state', 'alert-danger');
			$output['view'] = 'auth-error';
			return $output;			
		}
		
		//get fresh auth token from their auth code
		$auth_token = $this->model->getAuthToken($_GET['code']);
		if(!$auth_token){
			Util\Session::flash('message', 'Failed authorizing user', 'alert-danger');
			$output['view'] = 'auth-error';
			return $output;				
		}
		
		//get data on authorized user from TokenPass
		$oauth_user = $this->model->getOAuthUser($auth_token);
		if(!$oauth_user){
			Util\Session::flash('message', 'Could not get user', 'alert-danger');
			$output['view'] = 'auth-error';
			return $output;					
		}
		
		if($oauth_user['email_is_confirmed'] == 0){
			Util\Session::flash('message', 'Please verify your account email address before signing in. Visit your <a href="'.TOKENPASS_URL.'" target="_blank">TokenPass account</a> to resend the verification email.', 'alert-danger');
			$output['view'] = 'auth-error';
			return $output;			
		}
		
		//check if user in system
		$get_user = $this->model->findTokenPassUser($oauth_user['id']);
		if($get_user){
			//in system already
			$this->model->makeSession($get_user['userId'], $auth_token);
		}
		else{
			//check if username and email already in system... merge or create new account
			$mergable = $this->model->findMergableUser($oauth_user);
			if($mergable){
				//merge existing account
				$meta = new Meta_Model;
				$meta->updateUserMeta($mergable['userId'], 'tokenly_uuid', $oauth_user['id']);
				$this->model->makeSession($mergable['userId'], $auth_token);
				
			}
			else{
				//create new account
				try{
					$gen_user = $this->model->generateUser($oauth_user);
				}
				catch(\Exception $e)
				{
					Util\Session::flash('message', 'Error signing up user: '.$e->getMessage(), 'alert-danger');
					$output['view'] = 'auth-error';
					return $output;							
				}
				$this->model->makeSession($gen_user, $auth_token);
			}
		}
		
		redirect(route('account.auth').'/sync');
		return $output;
	}
	
}
