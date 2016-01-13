<?php
namespace App\CMS;
/*
 * @module-type = dashboard
 * @menu-label = Notification Pusher
 * 
 * */
class Notifier_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Notifier_Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';
		$output['view'] = 'index';
		$output['title'] = 'Notification Pusher';
		$output['error'] = '';
		$output['success'] = '';
		$output['form'] = $this->model->getNotifyForm();
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$notify = $this->model->sendNotification($data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$notify = false;
			}
			if($notify){
				$output['success'] = 'Notification sent!';
			}
		}
		return $output;
	}
}
