<?php
namespace App\Account;
/*
 * @module-type = dashboard
 * @menu-label = Profile
 * 
 * */
class Profile_Controller extends \App\ModControl
{
	public $args;
	public $data;
	
    function __construct()
    {
        parent::__construct();
        $this->model = new Profile_Model;
    }
    
    protected function init()
    {
		$output = parent::init();
		$output['user'] = Auth_Model::userInfo();
	
		if(!$output['user']){
			redirect($this->data['site']['url']);
		}
		
		$thisUser = $output['user'];
		$output['adminView'] = false;
		
		if(isset($this->args[2]) AND trim($this->args[2]) != ''){
			//check for account module access
			$accountModule = $this->model->get('modules', 'accounts', array(), 'slug');
			if($accountModule){
				$checkAccess = \App\AppControl::checkModuleAccess($accountModule['moduleId'], false);
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
			catch(\Exception $e){
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
		$meta = new \App\Meta_Model;
		$output['avatar'] = $meta->getUserMeta($thisUser['userId'], 'avatar');
				
		return $output;
    }
}
