<?php
namespace App\Account;
use App\API\V1, Util;
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
		$sesh_auth = Util\Session::get('accountAuth');
		if($sesh_auth){
			try{
				$userInfo = V1\Auth_Model::getUser(array('authKey' => $sesh_auth, 'site' => $this->data['site']));
			}
			catch(\Exception $e){
				redirect($this->site.'account/auth/logout');
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
