<?php
namespace App\CMS;
use Core, UI, Util;
class Modules_Model extends Core\Model
{

	protected function getAppForm($appId = 0)
	{
		$form = new UI\Form;
		
		$name = new UI\Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('App Name');
		$form->add($name);
		
		$slug = new UI\Textbox('slug');
		$slug->addAttribute('required');
		$slug->setLabel('Slug');
		$form->add($slug);
		
		$location = new UI\Textbox('location');
		$location->addAttribute('required');
		$location->setLabel('Controller Class Location');
		$form->add($location);	

		$url = new UI\Textbox('url');
		$url->setLabel('URL');
		$form->add($url);	

		$active = new UI\Checkbox('active');
		$active->setBool(1);
		$active->setValue(1);
		$active->setLabel('Active?');
		$form->add($active);
		
		if($appId != 0){
			$default = new UI\Select('defaultModule');
			$default->setLabel('Default Module');
			$default->addOption('0', 'Choose Module');
			$getModules = $this->getAll('modules', array('appId' => $appId));
			foreach($getModules as $module){
				$default->addOption($module['moduleId'], $module['name']);
			}
			$form->add($default);
		}

		return $form;
	}

	protected function getModuleForm()
	{
		$form = new UI\Form;
		
		$name = new UI\Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Module Name');
		$form->add($name);
		
		$slug = new UI\Textbox('slug');
		$slug->addAttribute('required');
		$slug->setLabel('Slug');
		$form->add($slug);
		
		$location = new UI\Textbox('location');
		$location->addAttribute('required');
		$location->setLabel('Controller Class Location');
		$form->add($location);	

		$url = new UI\Textbox('url');
		$url->setLabel('URL');
		$form->add($url);	

		$active = new UI\Checkbox('active');
		$active->setBool(1);
		$active->setValue(1);
		$active->setLabel('Active?');
		$form->add($active);
		
		$checkAccess = new UI\Checkbox('checkAccess');
		$checkAccess->setBool(1);
		$checkAccess->setValue(1);
		$checkAccess->setLabel('Check Group Access');
		$form->add($checkAccess);
		
		
		
		return $form;
	}

	
	protected function addApp($data)
	{
		$req = array('name' => true, 'slug' => true, 'active' => true, 'location' => true, 'url' => false);
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
		
		$add = $this->insert('apps', $useData);
		if(!$add){
			throw new \Exception('Error adding app');
		}
		
		$class = '\\App\\'.$data['location'].'\\Controller';
		$class = new $class;
		$class->__install($add);
		
		return $add;

	}
		
	protected function editApp($id, $data)
	{
		$req = array('name' => true, 'slug' => true, 'active' => true, 'location' => true, 'url' => false);
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
		
		$edit = $this->edit('apps', $id, $useData);
		if(!$edit){
			throw new \Exception('Error editing app');
		}
		
		return true;
	}

	protected function addModule($appId, $data)
	{
		$getApp = $this->get('apps', $appId);
		if(!$getApp){
			throw new \Exception('App not found');
		}
		
		$req = array('name', 'slug', 'active', 'location', 'url', 'checkAccess');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new \Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$getApp = $this->get('apps', $appId);
		if(!$getApp){
			throw new \Exception('App not found');
		}
		
		$useData['appId'] = $appId;
		
		$add = $this->insert('modules', $useData);
		if(!$add){
			throw new \Exception('Error adding module');
		}
		
		$class = '\\App\\'.$getApp['location'].'\\'.$data['location'].'_Controller';
		$class = new $class;
		if(method_exists($class, '__install')){
			$class->__install($add);
		}
		
		return $add;
		
	}
	
	protected function editModule($id, $data)
	{
		$req = array('name', 'slug', 'active', 'location', 'url', 'checkAccess');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new \Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$edit = $this->edit('modules', $id, $useData);
		if(!$edit){
			throw new \Exception('Error editing module');
		}
		return true;
	}
	
	protected function getAppSettingForm()
	{
		$form = new UI\Form;
		
		$key = new UI\Textbox('metaKey');
		$key->setLabel('Meta Key');
		$key->addAttribute('required');
		$form->add($key);
		
		$label = new UI\Textbox('label');
		$label->setLabel('Label');
		$form->add($label);
		
		$type = new UI\Select('type');
		$type->setLabel('Field Type');
		$type->addOption('textbox', 'Textbox');
		$type->addOption('textarea', 'Textarea');
		$type->addOption('select', 'Select');
		$form->add($type);
		
		$options = new UI\Textarea('options');
		$options->setLabel('Options (1 per line)');
		$form->add($options);
		
		$value = new UI\Textarea('metaValue');
		$value->setLabel('Current Value');
		$form->add($value);
		
		return $form;
		
	}
	
	protected function addAppSetting($data)
	{
		$data = checkRequiredFields($data, array('metaKey'));
		
		$insert = $this->insert('app_meta', $data);
		if(!$insert){
			throw new \Exception('Error adding app setting');
		}
		
		return $insert;
	}
	
	protected function editAppSetting($id, $data)
	{
		$data = checkRequiredFields($data, array('metaKey'));
		
		$edit = $this->edit('app_meta', $id, $data);
		if(!$edit){
			throw new \Exception('Error editing app setting');
		}
		
		return true;
	}


	protected function getAppPermForm()
	{
		$form = new UI\Form;
		
		$key = new UI\Textbox('permKey');
		$key->setLabel('Permission Key');
		$key->addAttribute('required');
		$form->add($key);
		
		return $form;
	}
	
	protected function addAppPerm($data)
	{
		$data = checkRequiredFields($data, array('permKey'));
		
		$insert = $this->insert('app_perms', $data);
		if(!$insert){
			throw new \Exception('Error adding app permission key');
		}
		
		return $insert;
	}
	
	protected function editAppPerm($id, $data)
	{
		$data = checkRequiredFields($data, array('permKey'));
		
		$edit = $this->edit('app_perms', $id, $data);
		if(!$edit){
			throw new \Exception('Error editing app permission key');
		}
		return true;
	}
}
