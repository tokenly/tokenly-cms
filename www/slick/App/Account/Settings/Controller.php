<?php
class Slick_App_Account_Settings_Controller extends Slick_App_ModControl
{
	public $args;
	public $data;
	
    function __construct()
    {
        parent::__construct();
        $this->model = new Slick_App_Account_Settings_Model;
    }
    
    public function init()
    {
		$output = parent::init();
		$output['user'] = Slick_App_Account_Home_Model::userInfo();
	
		if(!$output['user']){
			$this->redirect('/');
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
		
		if(!$output['adminView'] AND isset($this->args[2]) AND $this->args[2] == 'delete'){
			return $this->deleteAccount($output['user']);
		}
		
		
		$output['form'] = $this->model->getSettingsForm($thisUser, $this->data['app'], $output['adminView']);
		
		if(posted()){
			$data = $output['form']->grabData();
			
			try{
				$update = $this->model->updateSettings($thisUser, $data, $this->data['app'], false, $output['adminView']);
			}
			catch(Exception $e){
				$output['message'] = $e->getMessage();
				$update = false;
			}
			
			if($update){
				$output['message'] = 'Account Settings updated!';
			}
			
		}
		
		$getSettings = $this->model->getSettingsInfo($thisUser);
		
		$dropGroup = $this->model->get('groups', 'drop-list', array(), 'slug');
		$getSettings['dropList'] = 0;
		if($dropGroup){
			$inGroup = $this->model->getAll('group_users', array('userId' => $this->data['user']['userId'], 'groupId' => $dropGroup['groupId']));
			if($inGroup AND count($inGroup) > 0){
				$getSettings['dropList'] = 1;
			}
		}
		
		$getTokenVal = $this->model->fetchSingle('SELECT * FROM user_profileVals WHERE userId = :userId AND fieldId = :fieldId',
										array(':userId' => $this->data['user']['userId'], ':fieldId' => PRIMARY_TOKEN_FIELD));
		if($getTokenVal){
			$getSettings['field-'.PRIMARY_TOKEN_FIELD] = $getTokenVal['value'];
		}
		
		$output['form']->setValues($getSettings);
		if(isset($data) AND isset($update) AND $update){
			unset($data['curPassword']);
			unset($data['password']);
			unset($data['password2']);
			$output['form']->setValues($data);
		}
		$meta = new Slick_App_Meta_Model;
		$output['avatar'] = $meta->getUserMeta($thisUser['userId'], 'avatar');
		$output['view'] = 'form';
		$output['template'] = 'admin';
		$output['title'] = 'Account Settings';
		$output['thisUser'] = $thisUser;
		return $output;
    }
    
	private function deleteAccount($user)
	{
		$output = array();
		
		$output['form'] = $this->model->getDeleteForm();
		$output['user'] = $user;
		
		if(posted()){
			$data = $output['form']->grabData();
			
			try{
				$delete = $this->model->deleteAccount($user, $data);
			}
			catch(Exception $e){
				$output['message'] = $e->getMessage();
				$delete = false;
			}
			
			if($delete){
				$output['view'] = 'delete-success';
				$output['template'] = 'default';
				$output['title'] = 'Delete Account Permanently';
				return $output;
			}
		}
		
		$output['view'] = 'delete';
		$output['template'] = 'admin';
		$output['title'] = 'Delete Account Permanently';
		
		return $output;
	}
    
    
    
    
    
}
