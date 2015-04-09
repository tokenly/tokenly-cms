<?php
class Slick_App_Account_Logout_Controller extends Slick_App_ModControl
{
    function __construct()
    {
        parent::__construct();
        $this->model = new Slick_App_Account_Home_Model;
    }
    
    public function init()
    {
		$output = parent::init();
		if(!isset($_SESSION['accountAuth'])){
			$this->redirect($this->site.'/'.$this->data['app']['url']);
		}
		else{
			$this->model->clearSession($_SESSION['accountAuth']);
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
