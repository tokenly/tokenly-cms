<?php
namespace App\Tokenly;
use Util;
/*
 * @module-type = dashboard
 * @menu-label = Asset Scouter
 * 
 * */
class AssetScout_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new AssetScout_Model;
	}
	
	protected function init()
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
			catch(\Exception $e){
				Util\Session::flash('message', $e->getMessage(), 'text-error');
				$output['scout'] = false;
			}
		}
		
		return $output;
	}
}
