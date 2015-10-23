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
		
		$link_extra = '';
		if(isset($this->params['adspace'])){
			$link_extra = '?a='.$this->params['adspace'];
			if(isset($this->params['ad_key'])){
				$link_extra .= '-'.$this->params['ad_key'];
			}
		}
		
		$tracker = route('ad.tracking-links', '/'.$getLink['urlId']);
		$output = '<a href="'.$tracker.$link_extra.'" target="_blank" rel="nofollow" class="tracking-link">'.$output.'</a>';
		
		//attempt to record an impression
		if(!isset($_SESSION['viewed_ad_impressions'])){
			$_SESSION['viewed_ad_impressions'] = array();
		}

		if(!in_array($getLink['urlId'], $_SESSION['viewed_ad_impressions']) AND !botdetect()){
			$new_impressions = intval($getLink['impressions']) + 1;
			$this->model->edit('tracking_urls', $getLink['urlId'], array('impressions' => $new_impressions));
			$_SESSION['viewed_ad_impressions'][] = $getLink['urlId'];
			if(isset($this->params['adspace']) AND isset($this->params['ad_key'])){
				//update impressions stat for specific schedule ad on adspace
				$adspace = $this->model->get('adspaces', $this->params['adspace']);
				if($adspace){
					$items = json_decode($adspace['items'], true);
					$ad_key = intval($this->params['ad_key']);
					if(!is_array($items)){
						$items = array();
					}
					if(isset($items[$ad_key])){
						if(!isset($items[$ad_key]['stats'])){
							$items[$ad_key]['stats'] = array('clicks' => 0, 'impressions' => 0);
						}
						$items[$ad_key]['stats']['impressions']++;
						$this->model->edit('adspaces', $adspace['adspaceId'], array('items' => json_encode($items)));
					}
				}
			}
		}
		
		return $output;
	}
	
}
