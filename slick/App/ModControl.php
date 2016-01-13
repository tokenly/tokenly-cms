<?php
namespace App;
use Core;
class ModControl extends Core\Controller
{
    public $data = array();
    public $args = array();
	
	function __construct()
	{
		parent::__construct();
		$this->model = new Core\Model;
	}
	
	protected function init()
	{
		$output = array();
		if(intval($this->data['module']['checkAccess']) === 1){
			AppControl::checkModuleAccess($this->data['module']['moduleId']);
		}
		$dashModel = app_class('dashboard', 'model');
		$isDash = $dashModel->checkModuleIsDash($this->data['module']['moduleId']);
		if($isDash){
			if($this->data['app']['slug'] != 'dashboard'){
				$dashApp = get_app('dashboard');
				if(count($this->args) == 0){
					redirect($this->site.$dashApp['url'].'/');
				}
				else{
					redirect($this->site.$dashApp['url'].'/'.join('/', $this->args).'/');
				}
				
			}
		}
		return $output;
	}
	
	protected function __install($moduleId)
	{
		$getModule = $this->model->get('modules', $moduleId);
		if(!$getModule){
			return false;
		}
		
		if($getModule['checkAccess'] == 1){
			$getRoot = $this->model->get('groups', 'root-admin', array(), 'slug');
			if($getRoot){
				$this->model->insert('group_access', array('groupId' => $getRoot['groupId'], 'moduleId' => $moduleId));
			}
		}
		return $getModule;
	}
}
