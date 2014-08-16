<?php
class Slick_App_Dashboard_Notifier_Controller extends SLick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_Dashboard_Notifier_Model;
	}
	
	public function init()
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
			catch(Exception $e){
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
