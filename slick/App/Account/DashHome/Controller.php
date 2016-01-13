<?php
namespace App\Account;
use App\API\V1;
/*
 * @module-type = dashboard
 * @menu-label = Dashboard
 * 
 * */
class DashHome_Controller extends \App\ModControl
{

    function __construct()
    {
        parent::__construct();
        $this->model = new DashHome_Model;
    }
    
    protected function init()
    {
		if(isset($_SESSION['accountAuth'])){
			try{
				$userInfo = V1\Auth_Model::getUser(array('authKey' => $_SESSION['accountAuth'], 'site' => $this->data['site']));
			}
			catch(\Exception $e){
				redirect($this->site.'account/logout');
			}
		}
		else{
			redirect($this->site);
		}
		
		$output = parent::init();
		$output['view'] = 'index';
		$output['template'] = 'admin';
        
        return $output;
    }
}
