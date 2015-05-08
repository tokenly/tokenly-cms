<?php
namespace App\Account;
class Logout_Controller extends \App\ModControl
{
    function __construct()
    {
        parent::__construct();
        $this->model = new Home_Model;
    }
    
    public function init()
    {
		$output = parent::init();
		if(!isset($_SESSION['accountAuth'])){
			redirect($this->site.$this->data['app']['url']);
		}
		else{
			$this->model->clearSession($_SESSION['accountAuth']);
			unset($_SESSION['accountAuth']);
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
		return $output;
    }
}
