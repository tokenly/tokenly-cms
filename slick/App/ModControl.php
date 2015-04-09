<?php
class Slick_App_ModControl extends Slick_Core_Controller
{
    public $data = array();
    public $args = array();
	
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_Core_Model;
	}
	
	public function init()
	{
		$output = array();
		if(intval($this->data['module']['checkAccess']) === 1){
			Slick_App_AppControl::checkModuleAccess($this->data['module']['moduleId']);
		}
		$dashModel = app_class('dashboard', 'model');
		$isDash = $dashModel->checkModuleIsDash($this->data['module']['moduleId']);
		if($isDash){
			if($this->data['app']['slug'] != 'dashboard'){
				$dashApp = get_app('dashboard');
				$this->redirect($this->site.'/'.$dashApp['url'].$this->moduleUrl);
				die();
			}
		}
		return $output;
	}
	
	public function __install($moduleId)
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
