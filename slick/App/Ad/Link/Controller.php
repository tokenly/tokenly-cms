<?php
namespace App\Ad;
use Core, Util;
class Link_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Core\Model;
	}
	
	protected function init()
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
		$visited_trackers = Util\Session::get('visited_tracking_urls', array());

		//check if they have clicked this link already in current session
		if(!in_array($getLink['urlId'], $visited_trackers)){
			$unique = $this->container->checkUniqueClick($getLink['urlId']);
			$new_clicks = intval($getLink['clicks']) + 1;
			$new_unique = intval($getLink['unique_clicks']);
			$time = timestamp();
			
			//check if this is coming from an adspace
			$getAdspace = false;
			$ad_key = false;
			if(isset($_GET['a'])){
				$exp_a = explode('-', $_GET['a']);
				$adspaceId = intval($exp_a[0]);
				$getAdspace = $this->model->get('adspaces', $adspaceId);
				if(isset($exp_a[1])){
					$ad_key = intval($exp_a[1]);
				}
			}
			
			if($unique){
				//record unique click
				$new_unique += 1;
				$userId = 0;
				if($this->data['user']){
					$userId = $this->data['user']['userId'];
				}
				$request_url = 'N/A';
				if(isset($_SERVER['HTTP_REFERER'])){
					$request_url = strip_tags($_SERVER['HTTP_REFERER']);
					
					$clickData = array('urlId' => $getLink['urlId'], 'userId' => $userId,
									   'IP' => strip_tags($_SERVER['REMOTE_ADDR']), 'request_url' => $request_url,
									   'click_time' => $time);			
					

					if($getAdspace){
						$clickData['adspaceId'] = $getAdspace['adspaceId'];
					}				   		   
					$addClick = $this->model->insert('tracking_clicks', $clickData);		
				}
				
			}
			
			//record normal click
			$this->model->edit('tracking_urls', $getLink['urlId'], array('clicks' => $new_clicks, 'unique_clicks' => $new_unique,
																		 'last_click' => $time));	
																		 
			//record click to specific scheduled ad in adspace
			if(($ad_key !== false) AND $getAdspace){
				$ad_items = json_decode($getAdspace['items'], true);
				if(!is_array($ad_items)){
					$ad_items = array();
				}
				if(isset($ad_items[$ad_key])){
					if(!isset($ad_items[$ad_key]['stats'])){
						$ad_items[$ad_key]['stats'] = array('clicks' => 0, 'impressions' => 0);
					}
					$ad_items[$ad_key]['stats']['clicks']++;
					$this->model->edit('adspaces', $getAdspace['adspaceId'], array('items' => json_encode($ad_items)));
				}
			}		
						
			Util\Session::set('visited_tracking_urls', $getLink['urlId'], APPEND_ARRAY);
		}
		//send em' off
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
