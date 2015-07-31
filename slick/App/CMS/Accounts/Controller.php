<?php
namespace App\CMS;
/*
 * @module-type = dashboard
 * @menu-label = Manage Accounts
 * 
 * */
use App\Profile, App\Account;
class Accounts_Controller extends \App\ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        $this->model = new Accounts_Model;
    }
    
    public function init()
    {
		$output = parent::init();
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'delete':
					$output = $this->deleteUser();
					break;
				case 'view':
					$output = $this->viewUser();
					break;
				default:
					$output = $this->listUsers();
					break;
			}
		}
		else{
			$output = $this->listUsers();
		}
		$output['template'] = 'admin';

        return $output;
    }
    
    private function listUsers()
    {
		$output = array('view' => 'list');
		$output['searchForm'] = $this->model->getSearchForm();
		$output['message'] = '';
		if(posted()){
			$data = $output['searchForm']->grabData();
			$getUser = $this->model->fetchAll('SELECT userId, username, email, regDate,
														 lastAuth, lastActive
												FROM users
												WHERE username LIKE :username
												ORDER BY userId DESC',
												array(':username' => '%'.$data['username'].'%'));
			if(!$getUser OR count($getUser) == 0){
				$output['message'] = 'User not found';
			}
			else{
				if(count($getUser) === 1){
					redirect($this->site.$this->moduleUrl.'/view/'.$getUser[0]['userId']);
				}
				else{
					$output['users'] = $getUser;
					return $output;
				}
			}
		}
		$get = $this->model->getAll('users', array(), array('userId', 'username', 'email', 'regDate', 'lastAuth', 'lastActive'), 'userId');
		$output['users'] = $get;
		
		return $output;
	}
	
	private function deleteUser()
	{
		if(isset($this->args[3])){
			$user = Account\Home_Model::userInfo();
			if($this->args[3] != $user['userId']){
				$get = $this->model->get('users', $this->args[3]);
				if($get){
					$this->model->delete('users', $this->args[3]);
				}
			}
		}
		redirect($this->site.$this->moduleUrl);
	}
	
	private function viewUser()
	{
		$output = array();
		if(!isset($this->args[3])){
			redirect($this->site.$this->moduleUrl);
		}
		
		$get = $this->model->get('users', $this->args[3], array('userId', 'username', 'email', 'regDate', 'lastAuth', 'lastActive', 'slug'));
		if(!$get){
			redirect($this->site.$this->moduleUrl);
		}
		
		$output['form'] = $this->model->accountForm();
		if(posted()){
			try{
				$update = $this->model->updateAccount($this->args[3], $output['form']->grabData());
			}
			catch(\Exception $e){
				$output['message'] = $e->getMessage();
				$update = false;
			}
			if($update){
				$output['message'] = 'User updated successfully!';
			}
		}
		
		$meta = $this->model->getAll('user_meta', array('userId' => $this->args[3]));
		$get['meta'] = array();
		foreach($meta as $row){
			$get['meta'][$row['metaKey']] = $row['metaValue'];
		}
		
		$groups = $this->model->getAll('group_users', array('userId' => $this->args[3]));
		$get['groups'] = array();
		$groupIds = array();
		foreach($groups as $row){
			$getGroup = $this->model->get('groups', $row['groupId']);
			$get['groups'][$row['groupId']] = $getGroup['name'];
			$groupIds[] = $row['groupId'];
		}
		
		$profile = new Profile\User_Model;
		$get['profile'] = $profile->getUserProfile($get['userId'], $this->data['site']['siteId']);
		$get['profile'] = $get['profile']['profile'];
		
		$output['thisUser'] = $get;
		$output['view'] = 'view';

		$output['form']->setValues(array('groups' => $groupIds));
		return $output;
	}
}
