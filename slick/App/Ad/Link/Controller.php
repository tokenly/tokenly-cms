<?php
namespace App\Ad;
use Core;
class Link_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Core\Model;
	}
	
	public function init()
	{
		$output = parent::init();
		if(!isset($this->args[2])){
			$output['view'] = '404';
			return $output;
		}
		$getLink = $this->model->get('tracking_urls', $this->args[2]);
		if(!$getLink OR $getLink['active'] == 0 OR $getLink['siteId'] != $this->data['site']['siteId']){
			$output['view'] = '404';
			return $output;
		}
		if(!isset($_SESSION['visited_tracking_urls'])){
			$_SESSION['visited_tracking_urls'] = array();
		}	
		if(!in_array($getLink['urlId'], $_SESSION['visited_tracking_urls'])){
			$unique = $this->checkUniqueClick($getLink['urlId']);
			$new_clicks = intval($getLink['clicks']) + 1;
			$new_unique = intval($getLink['unique_clicks']);
			$time = timestamp();
			if($unique){
				$new_unique += 1;
				$userId = 0;
				if($this->data['user']){
					$userId = $this->data['user']['userId'];
				}
				$request_url = 'N/A';
				if(isset($_SERVER['HTTP_REFERER'])){
					$request_url = strip_tags($_SERVER['HTTP_REFERER']);
				}
				$clickData = array('urlId' => $getLink['urlId'], 'userId' => $userId,
								   'IP' => strip_tags($_SERVER['REMOTE_ADDR']), 'request_url' => $request_url,
								   'click_time' => $time);					   
				$addClick = $this->model->insert('tracking_clicks', $clickData);
			}
			$this->model->edit('tracking_urls', $getLink['urlId'], array('clicks' => $new_clicks, 'unique_clicks' => $new_unique,
																		 'last_click' => $time));																 
			$_SESSION['visited_tracking_urls'][] = $getLink['urlId'];
		}
		redirect($getLink['url']);
	}
	
	protected function checkUniqueClick($urlId)
	{
		$getClicks = $this->model->getAll('tracking_clicks', array('urlId' => $urlId, 'IP' => $_SERVER['REMOTE_ADDR']));
		if(count($getClicks) == 0){
			return true;
		}
		return false;
	}
}
