<?php
/*
 * @module-type = dashboard
 * @menu-label = Asset Scouter
 * 
 * */
class Slick_App_Tokenly_AssetScout_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_Tokenly_AssetScout_Model;
	}
	
	public function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';
		$output['view'] = 'index';
		$output['form'] = $this->model->getScoutForm();
		$output['scout'] = false;
		
		if(isset($_GET['asset'])){
			$asset = $_GET['asset'];
			$data = array('asset' => $asset);
			try{
				$output['scout'] = $this->model->scoutAsset($data);
			}
			catch(Exception $e){
				Slick_Util_Session::flash('message', $e->getMessage(), 'text-error');
				$output['scout'] = false;
			}
		}
		
		return $output;
	}
	
	
}
