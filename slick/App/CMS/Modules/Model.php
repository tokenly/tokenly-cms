<?php
class Slick_App_CMS_Modules_Model extends Slick_Core_Model
{

	public function getAppForm($appId = 0)
	{
		$form = new Slick_UI_Form;
		
		$name = new Slick_UI_Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('App Name');
		$form->add($name);
		
		$slug = new Slick_UI_Textbox('slug');
		$slug->addAttribute('required');
		$slug->setLabel('Slug');
		$form->add($slug);
		
		$location = new Slick_UI_Textbox('location');
		$location->addAttribute('required');
		$location->setLabel('Controller Class Location');
		$form->add($location);	

		$url = new Slick_UI_Textbox('url');
		$url->setLabel('URL');
		$form->add($url);	

		$active = new Slick_UI_Checkbox('active');
		$active->setBool(1);
		$active->setValue(1);
		$active->setLabel('Active?');
		$form->add($active);
		
		if($appId != 0){
			$default = new Slick_UI_Select('defaultModule');
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

	public function getModuleForm()
	{
		$form = new Slick_UI_Form;
		
		$name = new Slick_UI_Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Module Name');
		$form->add($name);
		
		$slug = new Slick_UI_Textbox('slug');
		$slug->addAttribute('required');
		$slug->setLabel('Slug');
		$form->add($slug);
		
		$location = new Slick_UI_Textbox('location');
		$location->addAttribute('required');
		$location->setLabel('Controller Class Location');
		$form->add($location);	

		$url = new Slick_UI_Textbox('url');
		$url->setLabel('URL');
		$form->add($url);	

		$active = new Slick_UI_Checkbox('active');
		$active->setBool(1);
		$active->setValue(1);
		$active->setLabel('Active?');
		$form->add($active);
		
		$checkAccess = new Slick_UI_Checkbox('checkAccess');
		$checkAccess->setBool(1);
		$checkAccess->setValue(1);
		$checkAccess->setLabel('Check Group Access');
		$form->add($checkAccess);
		
		
		
		return $form;
	}

	
	public function addApp($data)
	{
		$req = array('name' => true, 'slug' => true, 'active' => true, 'location' => true, 'url' => false);
		$useData = array();
		foreach($req as $key => $required){
			if(!isset($data[$key])){
				if($required){
					throw new Exception(ucfirst($key).' required');
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
			throw new Exception('Error adding app');
		}
		
		$class = 'Slick_App_'.$data['location'].'_Controller';
		$class = new $class;
		$class->__install($add);
		
		return $add;
		
		
	}
		
	public function editApp($id, $data)
	{
		$req = array('name' => true, 'slug' => true, 'active' => true, 'location' => true, 'url' => false);
		$useData = array();
		foreach($req as $key => $required){
			if(!isset($data[$key])){
				if($required){
					throw new Exception(ucfirst($key).' required');
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
			throw new Exception('Error editing app');
		}
		
		return true;
		
	}

	public function addModule($appId, $data)
	{
		$getApp = $this->get('apps', $appId);
		if(!$getApp){
			throw new Exception('App not found');
		}
		
		$req = array('name', 'slug', 'active', 'location', 'url', 'checkAccess');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$getApp = $this->get('apps', $appId);
		if(!$getApp){
			throw new Exception('App not found');
		}
		
		$useData['appId'] = $appId;
		
		$add = $this->insert('modules', $useData);
		if(!$add){
			throw new Exception('Error adding module');
		}
		
		$class = 'Slick_App_'.$getApp['location'].'_'.$data['location'].'_Controller';
		$class = new $class;
		$class->__install($add);
		
		return $add;
		
	}
	
	public function editModule($id, $data)
	{
		$req = array('name', 'slug', 'active', 'location', 'url', 'checkAccess');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$edit = $this->edit('modules', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing module');
		}
		
		return true;
		
	}
	
	public function getAppSettingForm()
	{
		$form = new Slick_UI_Form;
		
		$key = new Slick_UI_Textbox('metaKey');
		$key->setLabel('Meta Key');
		$key->addAttribute('required');
		$form->add($key);
		
		$label = new Slick_UI_Textbox('label');
		$label->setLabel('Label');
		$form->add($label);
		
		$type = new Slick_UI_Select('type');
		$type->setLabel('Field Type');
		$type->addOption('textbox', 'Textbox');
		$type->addOption('textarea', 'Textarea');
		$type->addOption('select', 'Select');
		$form->add($type);
		
		$options = new Slick_UI_Textarea('options');
		$options->setLabel('Options (1 per line)');
		$form->add($options);
		
		$value = new Slick_UI_Textarea('metaValue');
		$value->setLabel('Current Value');
		$form->add($value);
		
		return $form;
		
	}
	
	public function addAppSetting($data)
	{
		$data = checkRequiredFields($data, array('metaKey'));
		
		$insert = $this->insert('app_meta', $data);
		if(!$insert){
			throw new Exception('Error adding app setting');
		}
		
		return $insert;
		
	}
	
	public function editAppSetting($id, $data)
	{
		$data = checkRequiredFields($data, array('metaKey'));
		
		$edit = $this->edit('app_meta', $id, $data);
		if(!$edit){
			throw new Exception('Error editing app setting');
		}
		
		return true;
	}


	public function getAppPermForm()
	{
		$form = new Slick_UI_Form;
		
		$key = new Slick_UI_Textbox('permKey');
		$key->setLabel('Permission Key');
		$key->addAttribute('required');
		$form->add($key);
		
		return $form;
		
	}
	
	public function addAppPerm($data)
	{
		$data = checkRequiredFields($data, array('permKey'));
		
		$insert = $this->insert('app_perms', $data);
		if(!$insert){
			throw new Exception('Error adding app permission key');
		}
		
		return $insert;
		
	}
	
	public function editAppPerm($id, $data)
	{
		$data = checkRequiredFields($data, array('permKey'));
		
		$edit = $this->edit('app_perms', $id, $data);
		if(!$edit){
			throw new Exception('Error editing app permission key');
		}
		
		return true;
	}
}

?>
