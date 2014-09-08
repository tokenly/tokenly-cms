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
				
		
	}
	
}
