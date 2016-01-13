<?php
namespace App\Tokenly;
/*
 * @module-type = dashboard
 * @menu-label = Asset Cache
 * 
 * */
class AssetCache_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new AssetCache_Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';
		$this->data['perms'] = \App\Meta_Model::getUserAppPerms($this->data['user']['userId'], 'tokenly');
		$output['perms'] = $this->data['perms'];
		
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'add':
					$output = $this->container->addAsset($output);
					break;
				case 'edit':
					$output = $this->container->editAsset($output);
					break;
				default:
					$output['view'] = '404';
					break;
			}
		}
		else{
			$output = $this->container->viewAssetList($output);
		}
		return $output;
	}
	
	protected function viewAssetList($output)
	{
		$output['view'] = 'index';
		$output['assetList'] = $this->model->getAll('xcp_assetCache', array(), array(), 'asset', 'asc');
		
		if(!$this->data['perms']['canViewAllAssets']){
			foreach($output['assetList'] as $key => $asset){
				if($asset['ownerId'] != $this->data['user']['userId']){
					unset($output['assetList'][$key]);
				}
			}
		}
		return $output;
	}
	
	protected function addAsset($output)
	{
		if(!$this->data['perms']['canViewAllAssets']){
			$output['view'] = '403';
			return $output;
		}
		$output['view'] = 'add';
		$output['form'] = $this->model->addAssetForm();
		$output['message'] = '';
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->addAsset($data);
			}
			catch(\Exception $e){
				$add = false;
				$output['message'] = $e->getMessage();
			}
			if($add){
				redirect($this->site.$this->moduleUrl);
			}
		}
		return $output;
	}
	
	protected function editAsset($output)
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
		if(!$this->data['perms']['canViewAllAssets'] AND $getAsset['ownerId'] != $this->data['user']['userId']){
			$output['view'] = '403';
			return $output;
		}
		$output['view'] = 'edit';
		$output['form'] = $this->model->editAssetForm();
		if(!$this->data['perms']['canChangeAssetOwner']){
			$output['form']->remove('ownerId');
		}
		$output['message'] = '';
		$output['thisAsset'] = $getAsset;
		if(posted()){
			$data = $output['form']->grabData();
			$data['assetId'] = $getAsset['assetId'];
			try{
				$edit = $this->model->editAsset($data);
			}
			catch(\Exception $e){
				$edit = false;
				$output['message'] = $e->getMessage();
			}
			if($edit){
				redirect($this->site.$this->moduleUrl);
			}
		}
		$output['form']->setValues($getAsset);
		return $output;
	}
}
