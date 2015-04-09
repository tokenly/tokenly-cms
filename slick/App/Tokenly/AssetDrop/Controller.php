<?php
/*
 * @module-type = dashboard
 * @menu-label = Asset Dropper
 * 
 * */
class Slick_App_Tokenly_AssetDrop_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_Tokenly_AssetDrop_Model;
		
	}
	
	public function init()
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
			catch(Exception $e){
				$output['error'] = $e->getMessage();
				$init = false;
			}
			
			if($init){
				$distModule = $this->model->get('modules', 'share-distribute', array(), 'slug');
				$this->redirect($this->site.'/'.$this->data['app']['url'].'/'.$distModule['url'].'/tx/'.$init['address']);
				return $output;
			}
			
		}
		
		
		return $output;
		
	}
	
}
