<?php
namespace App\CMS;
/*
 * @module-type = dashboard
 * @menu-label = System Settings
 * 
 * */
class Settings_Controller extends \App\ModControl
{
    public $data = array();
    public $args = array();
	
	function __construct()
	{
		parent::__construct();
		$this->model = new Settings_Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		$getSettings = $this->model->getSettings();
		$form = $this->model->getSettingsForm($getSettings);
		$output['view'] = 'form';
		$output['form'] = $form;
		$output['template'] = 'admin';
		
		if(posted()){
			$data = $form->grabData();
			$edit = $this->model->editSettings($data);
			if(!$edit){
				$output['message'] = 'Error editing site settings';
				return $output;
			}
			$output['message'] = 'Settings updated!';
			$getSettings = $this->model->getSettings();
			$form = $this->model->getSettingsForm($getSettings);
			$output['form'] = $form;
		}
		return $output;
	}
}
