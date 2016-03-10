<?php
namespace App\API\V1;
class Account_Controller extends \Core\Controller
{
	public $methods = array('GET', 'PATCH');
	
	function __construct()
	{
		parent::__construct();
		$this->model = new \App\Account\Settings_Model;
		
	}
	
	protected function init($args = array())
	{
		$this->args = $args;
		$output = array();

		try{
			$this->user = Auth_Model::getUser($this->args['data']);
		}
		catch(\Exception $e){
			http_response_code(403);
			$output['error'] = $e->getMessage();
			return $output;
		}

		if(isset($this->args[1])){
			switch($this->args[1]){
				case 'settings':
					$output = $this->container->settings();
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
	
	protected function settings()
	{
		$output = array();
		
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'update':
					$output = $this->container->updateSettings();
					break;
				default:
					http_response_code(400);
					$output['error'] = 'Invalid request';
					return $output;
			}
		}
		else{
			$output = $this->container->getSettings();
		}
		
		return $output;
		
	}
	
	protected function getSettings($noCheck = false)
	{
		$output = array();
		if($this->useMethod != 'GET' AND !$noCheck){
			http_response_code(400);
			$output['error'] = 'Invalid request method';
			$output['methods'] = array('GET');
			return $output;
		}
		
		$getApp = $this->model->get('apps', 'account', array(), 'slug');
		$meta = new \App\Meta_Model;
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
	
	protected function updateSettings()
	{
		$output = array();
		if($this->useMethod != 'PATCH'){
			http_response_code(400);
			$output['error'] = 'Invalid request method';
			$output['methods'] = array('PATCH');
			return $output;
		}
		
		$getApp = $this->model->get('apps', 'account', array(), 'slug');
		$meta = new \App\Meta_Model;
		$appSettings = $meta->appMeta($getApp['appId']);
		$getApp['meta'] = $appSettings;
		

		$getSettings = $this->container->getSettings(true);
		$this->useMethod = 'PATCH';

		try{
			$update = $this->model->updateSettings($this->user, $this->args['data'], true);
		}
		catch(\Exception $e){
			http_response_code(401);
			$output['error'] = $e->getMessage();
			return $output;
		}
		
		if(!$update){
			http_response_code(400);
			$output['error'] = 'Error updating settings';
			return $output;
		}
		
		$output['result'] = 'success';
		$output['settings'] = $this->container->getSettings(true);
		$output['settings'] = $output['settings']['settings'];
		
		return $output;
	}
}
