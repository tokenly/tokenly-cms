<?php
namespace App\Tokenly;
/*
 * @module-type = dashboard
 * @menu-label = Asset Dropper
 * 
 * */
class AssetDrop_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new AssetDrop_Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		$output['view'] = 'index';
		$output['template'] = 'admin';
		
		$output['error'] = '';
		$output['form'] = $this->model->getDropperForm($this->data);
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$init = $this->model->initDrop($data, $this->data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$init = false;
			}
			
			if($init){
				$distModule = $this->model->get('modules', 'share-distribute', array(), 'slug');
				redirect($this->site.$this->data['app']['url'].'/'.$distModule['url'].'/tx/'.$init['address']);
			}
		}
		return $output;
	}
}
