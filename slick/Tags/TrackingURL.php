<?php
namespace Tags;
use Core;
class TrackingURL
{
	function __construct($params = array())
	{
		$this->params = $params;
		$this->model = new Core\Model;
		$this->site = currentSite();
	}
	
	public function display()
	{
		if(!isset($this->params['id'])){
			return false;
		}
		$getLink = $this->model->get('tracking_urls', $this->params['id']);
		if(!$getLink OR $getLink['siteId'] != $this->site['siteId'] OR $getLink['active'] == 0){
			return false;
		}
		$output = '';
		if(isset($this->params['image'])){
			$output .= '<img src="'.$this->params['image'].'" alt="" />';
		}
		if(isset($this->params['text'])){
			$output .= $this->params['text'];
		}
		if($output == ''){
			$output = $getLink['url'];
		}
		$tracker = route('ad.tracking-links', '/'.$getLink['urlId']);
		$output = '<a href="'.$tracker.'" target="_blank" rel="nofollow" class="tracking-link">'.$output.'</a>';
		
		//attempt to record an impression
		if(!isset($_SESSION['viewed_ad_impressions'])){
			$_SESSION['viewed_ad_impressions'] = array();
		}
		if(!in_array($getLink['urlId'], $_SESSION['viewed_ad_impressions']) AND !botdetect()){
			$new_impressions = intval($getLink['impressions']) + 1;
			$this->model->edit('tracking_urls', $getLink['urlId'], array('impressions' => $new_impressions));
			$_SESSION['viewed_ad_impressions'][] = $getLink['urlId'];
		}
		
		return $output;
	}
	
}
