<?php
namespace Tags;
use Core;
class HitCounter
{
	public $params = array();
	
	function __construct()
	{
		$this->model = new Core\Model;
	}
	
	public function display()
	{
		$output = '';
		$prefix = '';
		if(isset($this->params['prefix'])){
			$prefix = trim($this->params['prefix']);
		}
		$statKey = $prefix.'_hits';
		$getStat = $this->model->get('stats', $statKey, array(), 'statKey');
		$hits = 0;
		if($getStat){
			$hits = intval($getStat['statValue']);
		}
		
		if(!isset($_SESSION['has_hit_'.$prefix]) AND !botdetect()){
			$newHits = $hits+1;
			if($getStat){
				$this->model->edit('stats', $getStat['statId'], array('statValue' => $newHits));
			}
			else{
				$this->model->insert('stats', array('statKey' => $statKey, 'statValue' => $newHits));
			}
			$_SESSION['has_hit_'.$prefix] = true;
		}
		
		$output = '<div class="hit-counter"><strong>'.$hits.' <span>'.pluralize('Visit', $hits, true).'</span></strong></div>';
		
		return $output;
	}
	
}
