<?php
class Slick_App_Ad_Tracker_Model extends Slick_Core_Model
{
	public function getURLForm()
	{
		$form = new Slick_UI_Form;
		
		$url = new Slick_UI_Textbox('url');
		$url->setLabel('Destination URL');
		$url->addAttribute('required');
		$form->add($url);
		
		$active = new Slick_UI_Checkbox('active', 'active');
		$active->setLabel('Active?');
		$active->setBool(true);
		$active->setValue(1);
		$form->add($active);
		
		return $form;
	}
	
	public function addTrackingURL($data)
	{
		if(!isset($data['url']) OR trim($data['url']) == ''){
			throw new Exception('URL required');
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
		
		$insert = $this->insert('tracking_urls', $insertData);
		if(!$insert){
			throw new Exception('Error adding tracking URL');
		}
		
		$insertData['urlId'] = $insert;
		return $insertData;
	}
	
	public function editTrackingURL($id, $data)
	{
		if(!isset($data['url']) OR trim($data['url']) == ''){
			throw new Exception('URL required');
		}
		$active = 0;
		if(isset($data['active']) AND intval($data['active']) === 1){
			$active = 1;
		}
		
		$data['url'] = strip_tags($data['url']);
		
		$edit = $this->edit('tracking_urls', $id, array('url' => $data['url'], 'active' => $active));
		if(!$edit){
			throw new Exception('Error editing tracking URL');
		}
		return true;
	}
	
	
}
