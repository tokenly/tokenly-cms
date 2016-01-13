<?php
namespace App\RSS;
class Controller extends \App\AppControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Model;
	}
	
	protected function init()
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
