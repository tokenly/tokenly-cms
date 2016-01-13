<?php
namespace App\CMS;
use Core, UI, Util;
class Sites_Model extends Core\Model
{

	protected function getSiteForm($siteId = 0)
	{
		$form = new UI\Form;
		$form->setFileEnc();
		
		$name = new UI\Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Site Name');
		$form->add($name);
		
		$domain = new UI\Textbox('domain');
		$domain->addAttribute('required');
		$domain->setLabel('Domain');
		$form->add($domain);	

		$url = new UI\Textbox('url');
		$url->addAttribute('required');
		$url->setLabel('URL');
		$form->add($url);	

		$isDefault = new UI\Checkbox('isDefault');
		$isDefault->setBool(1);
		$isDefault->setValue(1);
		$isDefault->setLabel('Default site?');
		$form->add($isDefault);

		$image = new UI\File('image');
		$image->setLabel('Site Image');
		$form->add($image);

		if($siteId != 0){
			$apps = new UI\CheckboxList('apps');
			$apps->setLabel('Site Apps');
			$apps->setLabelDir('R');
			$getGroups = $this->getAll('apps');
			foreach($getGroups as $app){
				$apps->addOption($app['appId'], $app['name']);
			}
			
			$form->add($apps);
		}
		return $form;
	}

	protected function addSite($data)
	{
		$req = array('name', 'isDefault', 'domain', 'url');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new \Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$add = $this->insert('sites', $useData);
		if(!$add){
			throw new \Exception('Error adding site');
		}
		
		$this->container->updateSiteImage($add);
		
		return $add;
	}
		
	protected function editSite($id, $data)
	{
		$req = array('name', 'isDefault', 'domain', 'url');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new \Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$edit = $this->edit('sites', $id, $useData);
		if(!$edit){
			throw new \Exception('Error editing site');
		}
		
		$this->delete('site_apps', $id, 'siteId');
		foreach($data['apps'] as $app){
			$this->insert('site_apps', array('siteId' => $id, 'appId' => $app));
		}
		
		$this->container->updateSiteImage($id);
		
		return true;
	}
	
	protected function getSiteApps($siteId)
	{
		$get = $this->getAll('site_apps', array('siteId' => $siteId));
		$output = array();
		foreach($get as $row){
			$output[] = $row['appId'];
		}
		
		return $output;
	}

	protected function updateSiteImage($id)
	{
		if(isset($_FILES['image']['tmp_name']) AND trim($_FILES['image']['tmp_name']) != false){

			$name = $id.'-'.hash('sha256', $_FILES['image']['name'].$id).'.jpg';
			$path = SITE_PATH.'/files/sites/'.$name;
			$resize = Util\Image::resizeImage($_FILES['image']['tmp_name'], $path, 0, 0);
			if($resize){
				$update = $this->edit('sites', $id, array('image' => $name));
				if($update){
					return true;
				}
			}
			
		}
		return false;
	}
}
