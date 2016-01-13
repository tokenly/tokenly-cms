<?php
namespace App\RSS;
class Feed_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		ob_end_clean();
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'blog':
					$newOutput = $this->container->blogFeed();
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
	
	protected function __install($moduleId)
	{
		parent::__install($moduleId);
		
		$getModule = $this->model->get('modules', $moduleId);
		$getApp = $this->model->get('apps', $getModule['appId']);
		$appId = $getApp['appId'];
		
		$meta = new \App\Meta_Model;
		$meta->updateAppMeta($appId, 'blog-feed-title', '', 'Blog Feed Title', 1);
		$meta->updateAppMeta($appId, 'blog-feed-description', '', 'Blog Feed Description', 1, 'textarea');
	}
	
	protected function blogFeed()
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
