<?php
namespace App\Account;
use Util;
class Logout_Controller extends \App\ModControl
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
		return $output;
    }
}
