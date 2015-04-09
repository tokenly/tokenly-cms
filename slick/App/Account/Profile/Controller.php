<?php
/*
 * @module-type = dashboard
 * @menu-label = My Profile
 * 
 * */
class Slick_App_Account_Profile_Controller extends Slick_App_ModControl
{
	public $args;
	public $data;
	
    function __construct()
    {
        parent::__construct();
        $this->model = new Slick_App_Account_Profile_Model;
        
        
    }
    
    public function init()
    {
		$output = parent::init();
		$output['user'] = Slick_App_Account_Home_Model::userInfo();
	
		if(!$output['user']){
			$this->redirect($this->data['site']['url']);
			return false;
		}
		
		$thisUser = $output['user'];
		$output['adminView'] = false;
		
		if(isset($this->args[2]) AND trim($this->args[2]) != ''){
			//check for account module access
			$accountModule = $this->model->get('modules', 'accounts', array(), 'slug');
			if($accountModule){
				$checkAccess = Slick_App_AppControl::checkModuleAccess($accountModule['moduleId'], false);
				if($checkAccess){
					$thisUser = $this->model->get('users', $this->args[2], array('userId', 'username', 'slug', 'email'));
					if(!$thisUser){
						$output['view'] = '404';
						return $output;
					}
					$thisUser['groups'] = $this->model->getAll('group_users', array('userId' => $thisUser['userId']));
					$output['adminView'] = true;
				}
			}
		}
		

		$output['form'] = $this->model->getProfileForm($thisUser, $this->data['site']['siteId'], $this->data['app']);
		
		if(posted()){
			$data = $output['form']->grabData();
			
			try{
				$update = $this->model->updateProfile($thisUser, $data);
			}
			catch(Exception $e){
				$output['message'] = $e->getMessage();
				$update = false;
			}
			
			if($update){
				$output['message'] = 'Profile updated!';
			}
			
		}
		
		$getProfile = $this->model->getProfileInfo($thisUser);
		$output['form']->setValues($getProfile);
		$output['thisUser'] = $thisUser;
		$output['view'] = 'form';
		$output['template'] = 'admin';
		$output['title'] = 'Edit My Profile';
		$meta = new Slick_App_Meta_Model;
		$output['avatar'] = $meta->getUserMeta($thisUser['userId'], 'avatar');
				
		return $output;
    }
    

    
    
    
    
    
}
