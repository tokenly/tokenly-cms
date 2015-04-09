<?php
/*
 * @module-type = dashboard
 * @menu-label = App Settings
 * 
 * */
class Slick_App_CMS_AppSettings_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_CMS_AppSettings_Model;
		$this->meta = new Slick_App_Meta_Model;
	}
	
	public function init()
	{
		$output = parent::init();
		
		$apps = $this->data['site']['apps'];
		foreach($apps as &$app){
			$app['settings'] = $this->model->getAll('app_meta', array('isSetting' => 1, 'appId' => $app['appId']));
			$app['form'] = $this->model->getSettingsForm($app['settings']);
		}
		$output['apps'] = $apps;
		$output['view'] = 'form';
		$output['template'] = 'admin';
		
		if(posted()){
			$data = $_POST;
			try{
				$edit = $this->model->editSettings($data, $apps);
			}
			catch(Exception $e){
				$edit = false;
				Slick_Util_Session::flash('message', $e->getMessage(), 'error');	
			}
			
			if($edit){
				Slick_Util_Session::flash('message', 'Settings updated!', 'success');	
			}
			$this->redirect($this->site.$this->moduleUrl);
			die();
		}

		return $output;
	}
	
	
}


