<?php
namespace App\CMS;
/*
 * @module-type = dashboard
 * @menu-label = App Settings
 * 
 * */
use Util;
class AppSettings_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new AppSettings_Model;
		$this->meta = new \App\Meta_Model;
	}
	
	protected function init()
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
			catch(\Exception $e){
				$edit = false;
				Util\Session::flash('message', $e->getMessage(), 'error');	
			}
			
			if($edit){
				Util\Session::flash('message', 'Settings updated!', 'success');	
			}
			redirect($this->site.$this->moduleUrl);
		}
		return $output;
	}
}
