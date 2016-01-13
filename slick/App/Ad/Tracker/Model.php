<?php
namespace App\Ad;
use Core, UI, Util;
class Tracker_Model extends Core\Model
{
	protected function getURLForm()
	{
		$form = new UI\Form;
		$form->setFileEnc();
		
		$label = new UI\Textbox('label');
		$label->setLabel('Label');
		$form->add($label);
				
		$url = new UI\Textbox('url');
		$url->setLabel('Destination URL');
		$url->addAttribute('required');
		$form->add($url);
		
		$image = new UI\File('image');
		$image->setLabel('Image');
		$form->add($image);
		
		$active = new UI\Checkbox('active', 'active');
		$active->setLabel('Active?');
		$active->setBool(true);
		$active->setValue(1);
		$form->add($active);
		
		return $form;
	}
	
	protected function addTrackingURL($data)
	{
		if(!isset($data['url']) OR trim($data['url']) == ''){
			throw new \Exception('URL required');
		}
		$active = 0;
		if(isset($data['active']) AND intval($data['active']) === 1){
			$active = 1;
		}
		
		$insertData = array();
		$insertData['url'] = strip_tags($data['url']);
		$insertData['active'] = $active;
		$insertData['siteId'] = $data['siteId'];
		$insertData['userId'] = $data['userId'];
		$insertData['created_at'] = timestamp();
		if(isset($data['label'])){
			$insertData['label'] = $data['label'];
		}
		
		$insert = $this->insert('tracking_urls', $insertData);
		if(!$insert){
			throw new \Exception('Error adding tracking URL');
		}
		
		$this->container->uploadImage($insert);
		
		$insertData['urlId'] = $insert;
		return $insertData;
	}
	
	protected function editTrackingURL($id, $data)
	{
		if(!isset($data['url']) OR trim($data['url']) == ''){
			throw new \Exception('URL required');
		}
		$active = 0;
		if(isset($data['active']) AND intval($data['active']) === 1){
			$active = 1;
		}
		
		$data['url'] = strip_tags($data['url']);
		
		$updateData = array('url' => $data['url'], 'active' => $active);
		if(isset($data['label'])){
			$updateData['label'] = $data['label'];
		}
		
		$edit = $this->edit('tracking_urls', $id, $updateData);
		if(!$edit){
			throw new \Exception('Error editing tracking URL');
		}
		
		$this->container->uploadImage($id);
		
		return true;
	}
	
	protected function uploadImage($urlId)
	{
		if(isset($_FILES['image']['tmp_name']) AND trim($_FILES['image']['tmp_name']) != ''){
			$ext = 'jpg';
			if(isset($_FILES['image']['type'])){
				switch($_FILES['image']['type']){
					case 'image/jpg':
					case 'image/jpeg':
						$ext = 'jpg';
						break;
					case 'image/gif':
						$ext = 'gif';
						break;
					case 'image/png':
						$ext = 'png';
						break;
				}
			}
			$fileName = 'ad-'.md5($urlId.'-'.$_FILES['image']['name']).'.'.$ext;
			$dir = SITE_PATH.'/files/ads';
			if(!is_dir($dir)){
				@mkdir($dir, 755);
			}
			$move = move_uploaded_file($_FILES['image']['tmp_name'], $dir.'/'.$fileName);
			if(!$move){
				throw new \Exception('Error uploading image');
			}
			$save = $this->edit('tracking_urls', $urlId, array('image' => $fileName));
			if(!$save){
				throw new \Exception('Error saving uploaded image');
			}
			return true;
		}
		
	}
}
