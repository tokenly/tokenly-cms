<?php
namespace Tags;
use Core\Model, App\View;
class DisplayAdspace extends Model
{
	function __construct()
	{
		parent::__construct();	
		$this->view = new View;
		$this->site = currentSite();
	}
	
	public function display()
	{
		if(!isset($this->params['slug']) AND !isset($this->params['id'])){
			return false;
		}
		$found_adspace = false;
		if(isset($this->params['id'])){
			$found_adspace = $this->get('adspaces', $this->params['id']);
		}
		if(isset($this->params['slug']) AND !$found_adspace){
			$found_adspace = $this->get('adspaces', trim($this->params['slug']), array(), 'slug'); 
		}
		if(!$found_adspace OR $found_adspace['active'] == 0){
			return false;
		}
		$adspace = $found_adspace;
		$max_items = intval($adspace['maxItems']);
		$items = json_decode($adspace['items'], true);
		if(!is_array($items)){
			$items = array();
		}
		$orig_items = $items;
		$usable = array();
		$to_archive = array();
		$time = time();
		foreach($items as $k => $item){
			if(!isset($item['archived']) OR $item['archived'] == 0){
				if($time >= $item['start_date'] AND $time <= $item['end_date']){
					$getUrl = $this->get('tracking_urls', $item['urlId']);
					if($getUrl AND $getUrl['active'] == 1){
						$item['data'] = $getUrl;
						$item['true_key'] = $k;
						$usable[] = $item;
					}
				}
				elseif($time > $item['end_date']){
					//auto archive this
					$item['true_key'] = $k;
					$to_archive[] = $item;
				}
			}
		}

		$final_list = array();
		if(count($usable) > $max_items){
			for($i = 0; $i < $max_items; $i++){
				$count = count($usable);
				if($count <= 0){
					break;
				}
				$choose_key = mt_rand(0, ($count - 1));
				$final_list[] = $usable[$choose_key];
				unset($usable[$choose_key]);
				$usable = array_values($usable);
			}
		}
		else{
			$final_list = $usable;
		}
		
		if(count($final_list) == 0){
			return false;
		}
		
		//quickly archive any expired ads
		$update_list = false;
		foreach($to_archive as $arch_item){
			foreach($orig_items as $ok => $orig_item){
				if($ok == $arch_item['true_key']){
					$orig_items[$ok]['archived'] = 1;
					$update_list = true;
				}
			}
		}
		
		if($update_list){
			$this->edit('adspaces', $adspace['adspaceId'], array('items' => json_encode($orig_items)));
		}
		
		ob_start();
		?>
<div style="margin-left: auto; margin-right: auto; margin-bottom: 0px; line-height: 0; overflow: hidden; position: relative; display: block; text-align: center; width: <?= $adspace['width'] ?>px; height: <?= $adspace['height'] ?>px;">		
<?php

\Core\Model::$cacheMode = false;
foreach($final_list as $item){
	$tracker = new TrackingURL;
	$tracker->params = array('id' => $item['urlId'], 'image' => $this->site['url'].'/files/ads/'.$item['data']['image'],
							 'adspace' => $adspace['adspaceId'], 'ad_key' => $item['true_key']);
	echo '<span class="adspace-item">'.$tracker->display().'</span>';
}
\Core\Model::$cacheMode = true;
?>
</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
	
}
