<?php
namespace App\CMS;
/*
 * @module-type = dashboard
 * @menu-label = Apps & Modules
 * 
 * */
class Modules_Controller extends \App\ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        
        $this->model = new Modules_Model;
    }
    
    public function init()
    {
		$output = parent::init();
        
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->showModules();
					break;
				case 'add-app':
					$output = $this->addApp();
					break;
				case 'add-module':
					$output = $this->addModule();
					break;
				case 'edit-app':
					$output = $this->editApp();
					break;
				case 'edit-module':
					$output = $this->editModule();
					break;
				case 'delete-app':
					$output = $this->deleteApp();
					break;
				case 'delete-module':
					$output = $this->deleteModule();
					break;
				case 'settings':
					$output = $this->manageSettings();
					break;
				case 'perms':
					$output = $this->managePerms();
					break;
				default:
					$output = $this->showApps();
					break;
			}
		}
		else{
			$output = $this->showApps();
		}
		$output['template'] = 'admin';
        
        return $output;
    }
    
    private function showApps()
    {
		$output = array('view' => 'appList');
		$getApps = $this->model->getAll('apps');
		$output['appList'] = $getApps;

		return $output;
		
	}
	
	private function showModules()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getApp = $this->model->get('apps', $this->args[3]);
		if(!$getApp){
			redirect($this->site.$this->moduleUrl);
		}
		
		$output = array('view' => 'moduleList');
		$getModules = $this->model->getAll('modules', array('appId' => $this->args[3]));
		$output['moduleList'] = $getModules;
		$output['getApp'] = $getApp;
		
		return $output;
	}
	
	private function addApp()
	{
		$output = array('view' => 'appForm');
		$output['form'] = $this->model->getAppForm();
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->addApp($data);
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
	
	private function addModule()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getApp = $this->model->get('apps', $this->args[3]);
		if(!$getApp){
			redirect($this->site.$this->moduleUrl);
		}

		$output = array('view' => 'moduleForm');
		$output['form'] = $this->model->getModuleForm();
		$output['formType'] = 'Add';
		$output['getApp'] = $getApp;
		
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->addModule($this->args[3], $data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->site.$this->moduleUrl.'/view/'.$this->args[3]);
			}
		}
		return $output;
	}
	
	private function editApp()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getApp = $this->model->get('apps', $this->args[3]);
		if(!$getApp){
			redirect($this->site.$this->moduleUrl);
		}
		
		$output = array('view' => 'appForm');
		$output['form'] = $this->model->getAppForm();
		$output['formType'] = 'Edit';
		
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->editApp($this->args[3], $data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->site.$this->moduleUrl);
			}
			
		}
		$output['form']->setValues($getApp);
		
		return $output;
	}
	
	private function editModule()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getModule = $this->model->get('modules', $this->args[3]);
		if(!$getModule){
			redirect($this->site.$this->moduleUrl);
		}
		
		$output = array('view' => 'moduleForm');
		$output['form'] = $this->model->getModuleForm();
		$output['formType'] = 'Edit';
		$output['getApp'] = $this->model->get('apps', $getModule['appId']);
		
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->editModule($this->args[3], $data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->site.$this->moduleUrl.'/view/'.$getModule['appId']);
			}
			
		}
		$output['form']->setValues($getModule);
		
		return $output;
	}
	
	
	private function deleteApp()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getApp = $this->model->get('apps', $this->args[3]);
		if(!$getApp){
			redirect($this->site.$this->moduleUrl);
		}
		
		$delete = $this->model->delete('apps', $this->args[3]);
		$settingModule = $this->model->get('modules', 'app-settings', array(), 'slug');
		if($settingModule){
			$this->model->sendQuery('DELETE FROM dash_menu WHERE moduleId = :moduleId AND params = :params',
									array(':moduleId' => $settingModule['moduleId'], ':params' => '/'.$getApp['slug']));
		}
		redirect($this->site.$this->moduleUrl);
	}
	
	private function deleteModule()
	{
		if(isset($this->args[3])){
			$getModule = $this->model->get('modules', $this->args[3]);
			if($getModule){
				$delete = $this->model->delete('modules', $this->args[3]);
			}
		}
		redirect($this->site.$this->moduleUrl.'/view/'.$getModule['appId']);
	}
	
	public function manageSettings()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getApp = $this->model->get('apps', $this->args[3]);
		if(!$getApp){
			redirect($this->site.$this->moduleUrl);
		}
		
		$output = array('view' => 'appSettings');
		$output['thisApp'] = $getApp;
		$output['appSettings'] = $this->model->getAll('app_meta', array('appId' => $getApp['appId'], 'isSetting' => 1));		
		
		if(isset($this->args[4])){
			switch($this->args[4]){
				case 'add':
					$output = $this->addAppSetting($output);
					break;
				case 'edit':
					$output = $this->editAppSetting($output);
					break;
				case 'delete':
					$output = $this->deleteAppSetting($output);
					break;
				
			}
			
		}
		return $output;
	}
	
	public function addAppSetting($output)
	{
		
		$output['view'] = 'appSettingForm';
		$output['form'] = $this->model->getAppSettingForm();
		$output['formTitle'] = 'Add New Setting';
		$output['error'] = '';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['appId'] = $output['thisApp']['appId'];
			$data['isSetting'] = 1;			
			try{
				$add = $this->model->addAppSetting($data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/settings/'.$output['thisApp']['appId']);
			}
		}
		
		return $output;
	}
	
	public function editAppSetting($output)
	{
		if(!isset($this->args[5])){
			$output['view'] = '404';
			return $output;
		}
		
		$getSetting = $this->model->get('app_meta', $this->args[5]);
		if(!$getSetting OR $getSetting['isSetting'] == 0){
			$output['view'] = '404';
			return $output;
		}
		
		$output['thisSetting'] = $getSetting;
		$output['view'] = 'appSettingForm';
		$output['form'] = $this->model->getAppSettingForm();
		$output['formTitle'] = 'Edit Setting';
		$output['error'] = '';
		
		if(posted()){
			$data = $output['form']->grabData();		
			try{
				$edit = $this->model->editAppSetting($getSetting['appMetaId'], $data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$edit = false;
			}
			
			if($edit){
				redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/settings/'.$output['thisApp']['appId']);
			}
		}
		
		$output['form']->setValues($getSetting);
		return $output;
	}
	
	public function deleteAppSetting($output)
	{
		if(!isset($this->args[5])){
			$output['view'] = '404';
			return $output;
		}
		
		$getSetting = $this->model->get('app_meta', $this->args[5]);
		if(!$getSetting OR $getSetting['isSetting'] == 0){
			$output['view'] = '404';
			return $output;
		}
		
		$delete = $this->model->delete('app_meta', $getSetting['appMetaId']);
		redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/settings/'.$output['thisApp']['appId']);
		
		return $output;
	}



	public function managePerms()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getApp = $this->model->get('apps', $this->args[3]);
		if(!$getApp){
			redirect($this->site.$this->moduleUrl);
		}
		
		$output = array('view' => 'appPerms');
		$output['thisApp'] = $getApp;
		$output['appPerms'] = $this->model->getAll('app_perms', array('appId' => $getApp['appId']));		
		
		if(isset($this->args[4])){
			switch($this->args[4]){
				case 'add':
					$output = $this->addAppPerm($output);
					break;
				case 'edit':
					$output = $this->editAppPerm($output);
					break;
				case 'delete':
					$output = $this->deleteAppPerm($output);
					break;
			}
		}
		
		return $output;
	}
	
	public function addAppPerm($output)
	{
		
		$output['view'] = 'appPermForm';
		$output['form'] = $this->model->getAppPermForm();
		$output['formTitle'] = 'Add New Permission Key';
		$output['error'] = '';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['appId'] = $output['thisApp']['appId'];
				
			try{
				$add = $this->model->addAppPerm($data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/perms/'.$output['thisApp']['appId']);
			}
		}
		
		return $output;
	}
	
	public function editAppPerm($output)
	{
		if(!isset($this->args[5])){
			$output['view'] = '404';
			return $output;
		}
		
		$getPerm = $this->model->get('app_perms', $this->args[5]);
		if(!$getPerm){
			$output['view'] = '404';
			return $output;
		}
		
		$output['thisPerm'] = $getPerm;
		$output['view'] = 'appPermForm';
		$output['form'] = $this->model->getAppPermForm();
		$output['formTitle'] = 'Edit Permission Key';
		$output['error'] = '';
		
		if(posted()){
			$data = $output['form']->grabData();		
			try{
				$edit = $this->model->editAppPerm($getPerm['permId'], $data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$edit = false;
			}
			
			if($edit){
				redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/perms/'.$output['thisApp']['appId']);
			}
		}
		
		$output['form']->setValues($getPerm);
		
		return $output;
	}
	
	public function deleteAppPerm($output)
	{
		if(!isset($this->args[5])){
			$output['view'] = '404';
			return $output;
		}
		
		$getPerm = $this->model->get('app_perms', $this->args[5]);
		if(!$getPerm){
			$output['view'] = '404';
			return $output;
		}
		
		$delete = $this->model->delete('app_perms', $getPerm['permId']);
		redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/perms/'.$output['thisApp']['appId']);
		return $output;
	}
}
