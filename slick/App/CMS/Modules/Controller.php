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
    
    protected function init()
    {
		$output = parent::init();
        
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->container->showModules();
					break;
				case 'add-app':
					$output = $this->container->addApp();
					break;
				case 'add-module':
					$output = $this->container->addModule();
					break;
				case 'edit-app':
					$output = $this->container->editApp();
					break;
				case 'edit-module':
					$output = $this->container->editModule();
					break;
				case 'delete-app':
					$output = $this->container->deleteApp();
					break;
				case 'delete-module':
					$output = $this->container->deleteModule();
					break;
				case 'settings':
					$output = $this->container->manageSettings();
					break;
				case 'perms':
					$output = $this->container->managePerms();
					break;
				default:
					$output = $this->container->showApps();
					break;
			}
		}
		else{
			$output = $this->container->showApps();
		}
		$output['template'] = 'admin';
        
        return $output;
    }
    
    protected function showApps()
    {
		$output = array('view' => 'appList');
		$getApps = $this->model->getAll('apps');
		$output['appList'] = $getApps;

		return $output;
		
	}
	
	protected function showModules()
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
	
	protected function addApp()
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
	
	protected function addModule()
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
	
	protected function editApp()
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
	
	protected function editModule()
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
	
	
	protected function deleteApp()
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
	
	protected function deleteModule()
	{
		if(isset($this->args[3])){
			$getModule = $this->model->get('modules', $this->args[3]);
			if($getModule){
				$delete = $this->model->delete('modules', $this->args[3]);
			}
		}
		redirect($this->site.$this->moduleUrl.'/view/'.$getModule['appId']);
	}
	
	protected function manageSettings()
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
					$output = $this->container->addAppSetting($output);
					break;
				case 'edit':
					$output = $this->container->editAppSetting($output);
					break;
				case 'delete':
					$output = $this->container->deleteAppSetting($output);
					break;
				
			}
			
		}
		return $output;
	}
	
	protected function addAppSetting($output)
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
	
	protected function editAppSetting($output)
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
	
	protected function deleteAppSetting($output)
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



	protected function managePerms()
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
					$output = $this->container->addAppPerm($output);
					break;
				case 'edit':
					$output = $this->container->editAppPerm($output);
					break;
				case 'delete':
					$output = $this->container->deleteAppPerm($output);
					break;
			}
		}
		
		return $output;
	}
	
	protected function addAppPerm($output)
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
	
	protected function editAppPerm($output)
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
	
	protected function deleteAppPerm($output)
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
