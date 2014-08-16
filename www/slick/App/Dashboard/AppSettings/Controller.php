<?php
class Slick_App_Dashboard_AppSettings_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_Dashboard_AppSettings_Model;
	}
	
	public function init()
	{
		$output = parent::init();
		if(!isset($this->args[2])){
			$output['view'] = '404';
			return $output;
		}
		
		$getApp = $this->model->get('apps', $this->args[2], array(), 'slug');
		if(!$getApp){
			$output['view'] = '404';
			return $output;
		}
		
		$getSettings = $this->model->getAll('app_meta', array('appId' => $getApp['appId'], 'isSetting' => 1));

		$form = $this->model->getSettingsForm($getSettings);
		$output['view'] = 'form';
		$output['form'] = $form;
		$output['template'] = 'admin';
		$output['thisApp'] = $getApp;
		$output['appSettings'] = $this->model->appMeta($getApp['appId'], $this->data['site']['siteId']);
		
		if(posted()){
			$data = $form->grabData();
			$edit = $this->model->editSettings($getApp['appId'], $data);
			
			if(!$edit){
				$output['message'] = 'Error editing site settings';

				return $output;
			}
			
			$output['message'] = 'Settings updated!';
			$getSettings = $this->model->getAll('app_meta', array('appId' => $getApp['appId'], 'isSetting' => 1));
			$form = $this->model->getSettingsForm($getSettings);
			$output['form'] = $form;
		}

		return $output;
	}
	
	
}


