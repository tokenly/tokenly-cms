<?php
class Slick_App_Dashboard_LTBcoin_AssetCache_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_Dashboard_LTBcoin_AssetCache_Model;
	}
	
	public function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'add':
					$output = $this->addAsset($output);
					break;
				case 'edit':
					$output = $this->editAsset($output);
					break;
				default:
					$output['view'] = '404';
					break;
			}
		}
		else{
			$output = $this->viewAssetList($output);
		}
		return $output;
	}
	
	private function viewAssetList($output)
	{
		$output['view'] = 'index';
		$output['assetList'] = $this->model->getAll('xcp_assetCache', array(), array(), 'asset', 'asc');
		
		
		return $output;
	}
	
	private function addAsset($output)
	{
		$output['view'] = 'add';
		$output['form'] = $this->model->addAssetForm();
		$output['message'] = '';
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->addAsset($data);
			}
			catch(Exception $e){
				$add = false;
				$output['message'] = $e->getMessage();
			}
			if($add){
				$this->redirect($this->site.'/'.$this->moduleUrl);
			}
		}
		return $output;
	}
	
	private function editAsset($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $output;
		}
		$getAsset = $this->model->get('xcp_assetCache', strtoupper($this->args[3]), array(), 'asset');
		if(!$getAsset){
			$output['view'] = '404';
			return $output;
		}
		$output['view'] = 'edit';
		$output['form'] = $this->model->editAssetForm();
		$output['message'] = '';
		$output['thisAsset'] = $getAsset;
		if(posted()){
			$data = $output['form']->grabData();
			$data['assetId'] = $getAsset['assetId'];
			try{
				$edit = $this->model->editAsset($data);
			}
			catch(Exception $e){
				$edit = false;
				$output['message'] = $e->getMessage();
			}
			if($edit){
				$this->redirect($this->site.'/'.$this->moduleUrl);
			}
		}
		$output['form']->setValues($getAsset);
		return $output;
	}
	
}
