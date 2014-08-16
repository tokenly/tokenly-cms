<?php
class Slick_App_RSS_Controller extends Slick_App_AppControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_RSS_Model;
		
	}
	
	public function init()
	{
		$output = parent::init();
	
		if(!$this->module){
			$output['view'] = 'index';
			$output['title'] = 'RSS Feeds';
			$data = array('site' => $this->site);
			$output['form'] = $this->model->getCustomizeForm($data);
		}
		
		return $output;
	}
	
	
}
