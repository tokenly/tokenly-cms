<?php
namespace App\CMS;
/*
 * @module-type = dashboard
 * @menu-label = Manage Groups
 * 
 * */
class Groups_Controller extends \App\ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        $this->model = new Groups_Model;
    }
    
    public function init()
    {
		$output = parent::init();
        
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->showGroups();
					break;
				case 'add':
					$output = $this->addGroup();
					break;
				case 'edit':
					$output = $this->editGroup();
					break;
				case 'delete':
					$output = $this->deleteGroup();
					break;
				default:
					$output = $this->showGroups();
					break;
			}
		}
		else{
			$output = $this->showGroups();
		}
		$output['template'] = 'admin';
        return $output;
    }
    
    private function showGroups()
    {
		$output = array('view' => 'list');
		$getGroups = $this->model->getAll('groups');
		$output['groupList'] = $getGroups;

		return $output;
	}
	
	
	private function addGroup()
	{
		$output = array('view' => 'form');
		$output['form'] = $this->model->getGroupForm();
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->addGroup($data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->site.$this->moduleUrl);
			}
		}	
		return $output;
	}
	
	private function editGroup()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getGroup = $this->model->get('groups', $this->args[3]);
		if(!$getGroup){
			redirect($this->site.$this->moduleUrl);
		}
		
		$output = array('view' => 'form');
		$getModules = $this->model->getGroupModules($this->args[3], 1);
		$getGroup['moduleAccess'] = $getModules;
		$getSites = $this->model->getGroupSites($this->args[3], 1);
		$getGroup['siteAccess'] = $getSites;
		$output['form'] = $this->model->getGroupForm($this->args[3]);
		$output['formType'] = 'Edit';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->editGroup($this->args[3], $data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->site.$this->moduleUrl);
			}
		}
		$getPerms = $this->model->getAll('group_perms', array('groupId' => $getGroup['groupId']));
		foreach($getPerms as $perm){
			$perm = $this->model->get('app_perms', $perm['permId']);
			if(!isset($getGroup['perms-'.$perm['appId']])){
				$getGroup['perms-'.$perm['appId']] = array();
			}
			$getGroup['perms-'.$perm['appId']][] = $perm['permId'];
		}
		$output['form']->setValues($getGroup);
		
		return $output;
	}

	private function deleteGroup()
	{
		if(isset($this->args[3])){
			if($this->model->count('groups') > 1){
				$getGroup = $this->model->get('groups', $this->args[3]);
				if($getGroup){
					$delete = $this->model->delete('groups', $this->args[3]);
				}
			}
		}
		redirect($this->site.$this->moduleUrl);
	}
}
