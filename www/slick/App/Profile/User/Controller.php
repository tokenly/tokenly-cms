<?php
class Slick_App_Profile_User_Controller extends Slick_App_ModControl
{
	public $args;
	public $data;
	
    function __construct()
    {
        parent::__construct();
        $this->model = new Slick_App_Profile_User_Model;
        
        
    }
    
    public function init()
    {
		$output = parent::init();
		
		if(!isset($this->args[2])){
			$this->redirect($this->data['site']['url']);
			return false;
		}
		
		
		$getProfile = $this->model->getUserProfile($this->args[2], $this->data['site']['siteId']);
		if(!$getProfile){
			$output['view'] = '404';
			$output['title'] = '404 Page Not Found';
			http_response_code(404);
			return $output;
		}
		
		$output['profile'] = $getProfile;
		$output['view'] = 'profile';
		$output['title'] = 'User Info';
		
		return $output;
		
		
	}
	
	
}
