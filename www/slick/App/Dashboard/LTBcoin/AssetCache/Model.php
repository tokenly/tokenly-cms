<?php
class Slick_App_Dashboard_LTBcoin_AssetCache_Model extends Slick_Core_Model
{
	public function addAssetForm()
	{
		$form = new Slick_UI_Form;
		$form->setFileEnc();
		
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
		
		$image = new Slick_UI_File('image');
		$image->setLabel('Logo');
		$form->add($image);
		
		return $form;
	}
	
	public function editAssetForm()
	{
		$form = new Slick_UI_Form;
		$form->setFileEnc();
		
		$ownerId = new Slick_UI_Select('ownerId');
		$ownerId->setLabel('Asset Owner');
		$ownerId->addOption(0, '[nobody]');
		$getUsers = $this->getAll('users');
		foreach($getUsers as $user){
			$ownerId->addOption($user['userId'], $user['username']);
		}
		$form->add($ownerId);
		
		$description = new Slick_UI_Textarea('description', 'markdown');
		$description->setLabel('Description (use markdown)');
		$form->add($description);
		
		$link = new Slick_UI_Textbox('link');
		$link->setLabel('Additional Info Link');
		$form->add($link);
		
		$image = new Slick_UI_File('image');
		$image->setLabel('Logo');
		$form->add($image);		
		
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
		
		if(isset($_FILES['image']['tmp_name']) AND trim($_FILES['image']['tmp_name']) != ''){
			if(!is_dir(SITE_PATH.'/files/tokens')){
				@mkdir(SITE_PATH.'/files/tokens');
			}
			$imageName = md5($add.$_FILES['image']['name']).'.jpg';
			$image = new Slick_Util_Image;
			$meta = new Slick_App_Meta_Model;
			$settings = $meta->appMeta('ltbcoin');
			$resize = $image->resizeImage($_FILES['image']['tmp_name'], SITE_PATH.'/files/tokens/'.$imageName, intval($settings['token-logo-width']), intval($settings['token-logo-height']));
			if($resize){
				$this->edit('xcp_assetCache', $add, array('image' => $imageName));
			}
		}		
		
		return $add;
	}
	
	public function editAsset($data)
	{
		$useData = array('description' => $data['description'], 'link' => $data['link']);
		if(isset($data['ownerId'])){
			$useData['ownerId'] = $data['ownerId'];
		}
		$edit = $this->edit('xcp_assetCache', $data['assetId'], $useData);
		if(!$edit){
			throw new Exception('Error editing asset cache');
		}
		
		if(isset($_FILES['image']['tmp_name']) AND trim($_FILES['image']['tmp_name']) != ''){
			if(!is_dir(SITE_PATH.'/files/tokens')){
				@mkdir(SITE_PATH.'/files/tokens');
			}
			$imageName = md5($data['assetId'].$_FILES['image']['name']).'.jpg';
			$image = new Slick_Util_Image;
			$meta = new Slick_App_Meta_Model;
			$settings = $meta->appMeta('ltbcoin');
			$resize = $image->resizeImage($_FILES['image']['tmp_name'], SITE_PATH.'/files/tokens/'.$imageName, intval($settings['token-logo-width']), intval($settings['token-logo-height']));
			if($resize){
				$this->edit('xcp_assetCache', $data['assetId'], array('image' => $imageName));
			}
		}
		
		return $edit;
	}


}
