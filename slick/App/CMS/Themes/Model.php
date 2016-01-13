<?php
namespace App\CMS;
use Core, UI;
class Themes_Model extends Core\Model
{

	protected function getThemeForm($themeId = 0)
	{
		$form = new UI\Form;
		
		$name = new UI\Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Theme Name');
		$form->add($name);
		
		$location = new UI\Textbox('location');
		$location->addAttribute('required');
		$location->setLabel('Location');
		$form->add($location);	


		$isDefault = new UI\Checkbox('active');
		$isDefault->setBool(1);
		$isDefault->setValue(1);
		$isDefault->setLabel('Enabled for this site?');
		$form->add($isDefault);
		
		return $form;
	}
	
	protected function addTheme($data)
	{
		$req = array('name', 'location');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new \Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		if(!is_dir(SITE_PATH.'/themes/'.$data['location'])){
			throw new \Exception('Theme not found (location does not exist)');
		}
		
		$add = $this->insert('themes', $useData);
		if(!$add){
			throw new \Exception('Error adding theme');
		}
		
		if(isset($data['siteId']) AND isset($data['active']) AND intval($data['active']) === 1){
			$this->edit('sites', $data['siteId'], array('themeId' => $add));
		}
		
		return $add;
	}
		
	protected function editTheme($id, $data)
	{
		$req = array('name', 'location');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new \Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}

		if(!is_dir(SITE_PATH.'/themes/'.$data['location'])){
			throw new \Exception('Theme not found (location does not exist)');
		}

		if(isset($data['siteId']) AND isset($data['active'])){
			if(intval($data['active']) === 1){
				$this->edit('sites', $data['siteId'], array('themeId' => $id));
			}
			else{
				$getSite = $this->get('sites', $data['siteId']);
				if($getSite['themeId'] == $id){
					//return to default theme
					$this->edit('sites', $data['siteId'], array('themeId' => 1));
				}
			}
		}

		$edit = $this->edit('themes', $id, $useData);
		if(!$edit){
			throw new \Exception('Error editing theme');
		}
		return true;
	}
}
