<?php
class Slick_App_RSS_Feed_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_RSS_Model;
		
	}
	
	public function init()
	{
		$output = parent::init();
		ob_end_clean();
		
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'blog':
					$newOutput = $this->blogFeed();
					break;
				default:
					$output['view'] = '404';
					return $output;
			}
			$output = array_merge($newOutput, $output);
			return $output;
		}
		else{
			$output['view'] = '404';
			return $output;
		}
		
		return $output;
	}
	
	public function __install($moduleId)
	{
		parent::__install($moduleId);
		
		$getModule = $this->model->get('modules', $moduleId);
		$getApp = $this->model->get('apps', $getModule['appId']);
		$appId = $getApp['appId'];
		
		$meta = new Slick_App_Meta_Model;
		$meta->updateAppMeta($appId, 'blog-feed-title', '', 'Blog Feed Title', 1);
		$meta->updateAppMeta($appId, 'blog-feed-description', '', 'Blog Feed Description', 1, 'textarea');
		
	}
	
	private function blogFeed()
	{
		ob_end_clean();
		
		header('Content-type: application/xml');
		$data = $_REQUEST;
		unset($data['params']);
		$data['site'] = $this->data['site'];

		$this->model->getBlogFeed($data);

		die();
	}
	
}
