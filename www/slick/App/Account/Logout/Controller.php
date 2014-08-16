<?php
class Slick_App_Account_Logout_Controller extends Slick_App_ModControl
{
	public $args;
	public $data;
	
    function __construct()
    {
        parent::__construct();
        $this->model = new Slick_Core_Model;
        
        
    }
    
    public function init()
    {
		$output = parent::init();
		if(!isset($_SESSION['accountAuth'])){
			$this->redirect($this->site.'/'.$this->data['app']['url']);
		}
		else{
			$user = $this->model->get('users', $_SESSION['accountAuth'], array('userId', 'username', 'email', 'lastAuth', 'auth', 'regDate'), 'auth');
			if($user){
				$this->model->edit('users', $user['userId'], array('auth' => ''));
			}
			
			unset($_SESSION['accountAuth']);
			if(isset($_COOKIE['rememberAuth'])){
				setcookie('rememberAuth', '', time()-3600,'/');
			}
			if(isset($_GET['r'])){
				$this->redirect($this->site.$_GET['r']);
			}
			else{
				$this->redirect($this->site.'/'.$this->data['app']['url']);
			}
		}
		
		return $output;
    }
    

    
    
    
    
    
}
