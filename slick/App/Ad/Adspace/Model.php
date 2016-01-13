<?php
namespace App\Ad;
use Core\Model, Util, UI;

class Adspace_Model extends Model
{
	protected function getAdspaceForm()
	{
		$form = new UI\Form;

		$label = new UI\Textbox('label');
		$label->setLabel('Label');
		$label->addAttribute('required');
		$form->add($label);
		
		$slug = new UI\Textbox('slug');
		$slug->setLabel('Slug (leave blank to autogenerate)');
		$form->add($slug);				
		
		$width = new UI\Textbox('width');
		$width->setLabel('Width (px)');
		$width->addAttribute('required');
		$form->add($width);
		
		$height = new UI\Textbox('height');
		$height->setLabel('Height (px)');
		$height->addAttribute('required');
		$form->add($height);	
		
		$maxItems = new UI\Select('maxItems');
		$maxItems->setLabel('Maxmimum # of ads to display');
		for($i = 1; $i <= 20; $i++){
			$maxItems->addOption($i, $i);
		}	
		$form->add($maxItems);
		
		
		$active = new UI\Checkbox('active');
		$active->setLabel('Active?');
		$active->setBool(1);
		$active->setValue(1);
		$form->add($active);
		
		return $form;
	}
	
	protected function getAdForm()
	{
		$form = new UI\Form;
		
		$hidden = new UI\Hidden('new-ad');
		$hidden->setValue(true);
		$form->add($hidden);
		
		$ad =  new UI\Select('ad');
		$ad->setLabel('Choose Tracking URL');
		$getAds = $this->getAll('tracking_urls', array(), array(), 'urlId', 'desc');
		foreach($getAds as $getAd){
			if(trim($getAd['image']) == ''){
				//no image assigned to this, skip it
				continue;
			}
			$ad_label = '[#'.$getAd['urlId'].'] '.$getAd['url'];
			if(trim($getAd['label']) != ''){
				$ad_label = $getAd['label'].' - '.$ad_label;
			}
			$ad->addOption($getAd['urlId'], $ad_label);
		}
		$form->add($ad);
		
		$start = new UI\Textbox('start_date');
		$start->addClass('datetimepicker');
		$start->setLabel('Start Date');
		$start->addAttribute('required');
		$form->add($start);
		
		$end = new UI\Textbox('end_date');
		$end->addClass('datetimepicker');
		$end->setLabel('End Date');
		$end->addAttribute('required');
		$form->add($end);		
		
		$form->setSubmitText('Add Advertisement');
		
		return $form;
	}
	
	protected function getEditAdForm()
	{
		$form = new UI\Form;
		
		$hidden = new UI\Hidden('edit-ad');
		$hidden->setValue(true);
		$form->add($hidden);
			
		$start = new UI\Textbox('start_date');
		$start->addClass('datetimepicker');
		$start->setLabel('Start Date');
		$start->addAttribute('required');
		$form->add($start);
		
		$end = new UI\Textbox('end_date');
		$end->addClass('datetimepicker');
		$end->setLabel('End Date');
		$end->addAttribute('required');
		$form->add($end);		
		
		$form->setSubmitText('Edit Adspace Advertisement');
		
		return $form;
	}
	
	protected function createAdspace($data)
	{
		$req = array('label' => true, 'width' => true, 'height' => true, 'maxItems' => true, 'slug' => false); 
		$useData = array();
		foreach($req as $key => $required){
			if(!isset($data[$key])){
				if($required){
					throw new \Exception(ucfirst($key).' required');
				}
				else{
					$useData[$key] = '';
				}
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$useData['active'] = 0;
		if(isset($data['active']) AND intval($data['active']) == 1){
			$useData['active'] = 1;
		}
		
		if(trim($useData['slug']) == ''){
			$useData['slug'] = genURL($useData['label']);
		}
		
		$timestamp = timestamp();
		$useData['updated_at'] = $timestamp;
		$useData['created_at'] = $timestamp;
		
		$add = $this->insert('adspaces', $useData);
		if(!$add){
			throw new \Exception('Error saving adspace');
		}
		
		return $add;
	}
	
	protected function editAdspace($id, $data)
	{
		$req = array('label' => true, 'width' => true, 'height' => true, 'maxItems' => true, 'slug' => false); 
		$useData = array();
		foreach($req as $key => $required){
			if(!isset($data[$key])){
				if($required){
					throw new \Exception(ucfirst($key).' required');
				}
				else{
					$useData[$key] = '';
				}
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$useData['active'] = 0;
		if(isset($data['active']) AND intval($data['active']) == 1){
			$useData['active'] = 1;
		}
		
		if(trim($useData['slug']) == ''){
			$useData['slug'] = genURL($useData['label']);
		}
	
		$useData['updated_at'] = timestamp();
		
		$edit = $this->edit('adspaces', $id, $useData);
		if(!$edit){
			throw new \Exception('Error saving adspace');
		}
		
		return true;
	}
	
	protected function addUrlToAdspace($adspace, $data)
	{
		if(!is_array($adspace['items'])){
			$items = json_decode($adspace['items'], true);
			if(!is_array($items)){
				$items = array();
			}
		}
		else{
			$items = $adspace['items'];
		}
		
		$getAd = false;
		if(isset($data['ad'])){
			$getAd = $this->get('tracking_urls', $data['ad']);
		}
		if(!$getAd){
			throw new \Exception('Tracking URL not found');
		}
		
		if(!isset($data['start_date']) OR !isset($data['end_date'])){
			throw new \Exception('Start and end dates must be set');
		}
		
		$useData = array();
		$useData['urlId'] = $getAd['urlId'];
		$useData['start_date'] = strtotime($data['start_date']);
		$useData['end_date'] = strtotime($data['end_date']);
		$useData['archived'] = 0;
		array_unshift($items, $useData);
		
		$edit = $this->edit('adspaces', $adspace['adspaceId'], array('items' => json_encode($items)));
		if(!$edit){
			throw new \Exception('Error saving advertisement to adspace');
		}
		
		return true;
		
	}
	
	protected function deleteUrlFromAdspace($adspace, $idx)
	{
		if(!is_array($adspace['items'])){
			$items = json_decode($adspace['items'], true);
			if(!is_array($items)){
				$items = array();
			}
		}
		else{
			$items = $adspace['items'];
		}
		
		if(isset($items[$idx])){
			unset($items[$idx]);
			$items = array_values($items);
			
			$edit = $this->edit('adspaces', $adspace['adspaceId'], array('items' => json_encode($items)));
			if(!$edit){
				throw new \Exception('Error deleting advertisement from adspace');
			}
			return true;
		}
		else{
			throw new \Exception('Ad not found');
		}
	}
	
	protected function editAdspaceAd($adspace, $idx, $data)
	{
		if(!is_array($adspace['items'])){
			$items = json_decode($adspace['items'], true);
			if(!is_array($items)){
				$items = array();
			}
		}
		else{
			$items = $adspace['items'];
		}
		
		if(isset($items[$idx])){
			if(isset($data['start_date'])){
				$items[$idx]['start_date'] = strtotime($data['start_date']);
			}
			if(isset($data['end_date'])){
				$items[$idx]['end_date'] = strtotime($data['end_date']);
			}			
			
			$items[$idx]['active'] = $active;
			
			$edit = $this->edit('adspaces', $adspace['adspaceId'], array('items' => json_encode($items)));
			if(!$edit){
				throw new \Exception('Error updating advertisement settings');
			}
			return true;
		}
		else{
			throw new \Exception('Ad not found');
		}
	}
	
	
}
