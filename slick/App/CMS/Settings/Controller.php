<?php
/*
 * @module-type = dashboard
 * @menu-label = System Settings
 * 
 * */
class Slick_App_CMS_Settings_Controller extends Slick_App_ModControl
{
    public $data = array();
    public $args = array();
	
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_CMS_Settings_Model;

	}
	
	public function init()
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


?>


