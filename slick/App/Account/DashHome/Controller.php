<?php
/*
 * @module-type = dashboard
 * @menu-label = Account Dashboard Home
 * 
 * */
class Slick_App_Account_DashHome_Controller extends Slick_App_ModControl
{

    function __construct()
    {
        parent::__construct();
        
        $this->model = new Slick_App_Account_DashHome_Model;

    }
    
    public function init()
    {
		if(isset($_SESSION['accountAuth'])){
			try{
				$userInfo = Slick_App_API_V1_Auth_Model::getUser(array('authKey' => $_SESSION['accountAuth'], 'site' => $this->data['site']));
			}
			catch(Exception $e){
				$this->redirect($this->site.'/account/logout');
				return true;
			}
		}
		else{
			$this->redirect($this->site);
			return true;
		}
		
		$output = parent::init();
		$output['view'] = 'index';
		$output['template'] = 'admin';
        
        return $output;
    }


}

?>
