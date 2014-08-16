<?php
class Slick_App_API_V1_Account_Controller extends Slick_Core_Controller
{
	public $methods = array('GET', 'PATCH');
	
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_Account_Settings_Model;
		
	}
	
	public function init($args = array())
	{
		$this->args = $args;
		$output = array();

		try{
			$this->user = Slick_App_API_V1_Auth_Model::getUser($this->args['data']);
		}
		catch(Exception $e){
			http_response_code(403);
			$output['error'] = $e->getMessage();
			return $output;
		}

		if(isset($this->args[1])){
			switch($this->args[1]){
				case 'settings':
					$output = $this->settings();
					break;
				default:
					http_response_code(400);
					$output['error'] = 'Invalid request';
					return $output;
			}
		}
		else{
			http_response_code(400);
			$output['error'] = 'Invalid request';
			return $output;
		}
		
		return $output;
	}
	
	private function settings()
	{
		$output = array();
		
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'update':
					$output = $this->updateSettings();
					break;
				default:
					http_response_code(400);
					$output['error'] = 'Invalid request';
					return $output;
			}
		}
		else{
			$output = $this->getSettings();
		}
		
		return $output;
		
	}
	
	private function getSettings($noCheck = false)
	{
		$output = array();
		if($this->useMethod != 'GET' AND !$noCheck){
			http_response_code(400);
			$output['error'] = 'Invalid request method';
			$output['methods'] = array('GET');
			return $output;
		}
		
		$getApp = $this->model->get('apps', 'account', array(), 'slug');
		$meta = new Slick_App_Meta_Model;
		$appSettings = $meta->appMeta($getApp['appId']);
		$getForm = $this->model->getSettingsForm($this->user, array('meta' => $appSettings));
		
		$settingList = array();
		$fields = $getForm->getFields();

		$excludeVal = array('password', 'password2', 'curPassword');
		foreach($fields as $field){
			$row = array('name' => $field['name'], 'label' => strip_tags($field['label']));
			if(!in_array($field['name'], $excludeVal)){
				if(isset($this->user[$field['name']])){
					$row['value'] = $this->user[$field['name']];
				}
			}
			
			$settingList[] = $row;
		}
		
		$output['settings'] = $settingList;
		
		return $output;
	}
	
	private function updateSettings()
	{
		$output = array();
		if($this->useMethod != 'PATCH'){
			http_response_code(400);
			$output['error'] = 'Invalid request method';
			$output['methods'] = array('PATCH');
			return $output;
		}
		
		$getApp = $this->model->get('apps', 'account', array(), 'slug');
		$meta = new Slick_App_Meta_Model;
		$appSettings = $meta->appMeta($getApp['appId']);
		$getApp['meta'] = $appSettings;
		

		$getSettings = $this->getSettings(true);
		$this->useMethod = 'PATCH';

		$getSettings = $getSettings['settings'];
		
		$useData = array();
		foreach($getSettings as $setting){
			if(isset($this->args['data'][$setting['name']])){
				$useData[$setting['name']] = $this->args['data'][$setting['name']];
			}
		}
		
		try{
			$update = $this->model->updateSettings($this->user, $useData, $getApp, true);
		}
		catch(Exception $e){
			http_response_code(400);
			$output['error'] = $e->getMessage();
			return $output;
		}
		
		if(!$update){
			http_response_code(400);
			$output['error'] = 'Error updating settings';
			return $output;
		}
		
		$output['result'] = 'success';
		$output['settings'] = $this->getSettings(true);
		$output['settings'] = $output['settings']['settings'];
		
		return $output;
	}
	
	
	
	
}
?>
