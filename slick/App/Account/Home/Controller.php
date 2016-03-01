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
		if($this->data['user']){
			redirect(route('account.dash-home'));
		}
		else{
			redirect(route('account.auth'));
		}
		die();
    }
}
