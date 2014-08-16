<?php
class Slick_App_Dashboard_LTBcoin_AssetCache_Model extends Slick_Core_Model
{
	public function addAssetForm()
	{
		$form = new Slick_UI_Form;
		
		$asset = new Slick_UI_Textbox('asset');
		$asset->setLabel('Asset Name');
		$asset->addAttribute('required');
		$form->add($asset);
		
		$description = new Slick_UI_Textarea('description', 'markdown');
		$description->setLabel('Description (use markdown)');
		$form->add($description);
		
		$link = new Slick_UI_Textbox('link');
		$link->setLabel('Additional Info Link');
		$form->add($link);		
		
		return $form;
	}
	
	public function editAssetForm()
	{
		$form = new Slick_UI_Form;
		
		$description = new Slick_UI_Textarea('description', 'markdown');
		$description->setLabel('Description (use markdown)');
		$form->add($description);
		
		$link = new Slick_UI_Textbox('link');
		$link->setLabel('Additional Info Link');
		$form->add($link);
		
		return $form;
	}
	
	public function addAsset($data)
	{
		if(trim($data['asset']) == ''){
			throw new Exception('Asset name required');
		}
		$checkAsset = $this->get('xcp_assetCache', strtoupper($data['asset']), array('assetId'), 'asset');
		if($checkAsset){
			throw new Exception('Asset already exists in cache');
		}
		$xcp = new Slick_API_Bitcoin(XCP_CONNECT);
		try{
			$getAsset = $xcp->get_asset_info(array('assets' => array(strtoupper($data['asset']))));
		}
		catch(Exception $e){
			throw new Exception('Failed getting asset info');
		}
		if(count($getAsset) == 0){
			throw new Exception('Asset not found');
		}
		$getAsset = $getAsset[0];
		$divisible = 0;
		if($getAsset['divisible']){
			$divisible = 1;
		}
		if(trim($data['description']) == ''){
			$data['description'] = $getAsset['description'];
		}
		$add = $this->insert('xcp_assetCache', array('asset' => strtoupper($data['asset']), 'divisible' => $divisible, 'lastChecked' => timestamp(),
											'description' => $data['description'], 'link' => $data['link']));
		if(!$add){
			throw new Exception('Error adding to cache');
		}
		return $add;
	}
	
	public function editAsset($data)
	{
		$useData = array('description' => $data['description'], 'link' => $data['link']);
		$edit = $this->edit('xcp_assetCache', $data['assetId'], $useData);
		if(!$edit){
			throw new Exception('Error editing asset cache');
		}
		return $edit;
	}


}
