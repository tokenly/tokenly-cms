<?php
namespace App\CMS;
use Core, UI, Util;
class ProfileFields_Model extends Core\Model
{

	protected function getFieldForm($fieldId = 0)
	{
		$form = new UI\Form;
		
		$label = new UI\Textbox('label');
		$label->addAttribute('required');
		$label->setLabel('Label');
		$form->add($label);
		
		$slug = new UI\Textbox('slug');
		$slug->setLabel('Slug');
		$form->add($slug);		
		
		$type = new UI\Select('type');
		$type->setLabel('Field Type');
		$type->addOption('textbox', 'Textbox');
		$type->addOption('textarea', 'Textarea');
		$type->addOption('select', 'Select Dropdown');
		$form->add($type);
		
		$options = new UI\Textarea('options');
		$options->setLabel('Options (if dropdown) - 1 per line');
		$form->add($options);
		
		$public = new UI\Checkbox('public', 'public');
		$public->setLabel('Publicly viewable field?');
		$public->setBool(1);
		$public->setValue(1);
		$form->add($public);
		
		$active = new UI\Checkbox('active', 'active');
		$active->setLabel('Active?');
		$active->setBool(1);
		$active->setValue(1);
		$form->add($active);

		$rank = new UI\Textbox('rank');
		$rank->setLabel('Order Rank');
		$form->add($rank);
		
		$getGroups = $this->getAll('groups');
		$groups = new UI\CheckboxList('groups');
		foreach($getGroups as $group){
			$groups->addOption($group['groupId'], $group['name']);
		}
		$groups->setLabel('Groups');
		$groups->setLabelDir('R');
		$form->add($groups);
		
		return $form;
	}
	
	protected function addField($data)
	{
		$req = array('label' => true, 'type' => true, 'options' => false, 'public' => false, 'active' => false, 'siteId' => true, 'rank' => false, 'slug' => false);
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
		
		if(trim($useData['slug']) == ''){
			$useData['slug'] = genURL($useData['label']);
		}		
		
		$add = $this->insert('profile_fields', $useData);
		if(!$add){
			throw new \Exception('Error adding field');
		}
		
		if(isset($data['groups'])){
			foreach($data['groups'] as $group){
				$this->insert('profile_fieldGroups', array('fieldId' => $add, 'groupId' => $group));
			}
		}
		
		return $add;
	}
		
	protected function editField($id, $data)
	{
		$req = array('label' => true, 'type' => true, 'options' => false, 'public' => false, 'active' => false, 'siteId' => true, 'rank' => false, 'slug' => false);
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
		
		if(trim($useData['slug']) == ''){
			$useData['slug'] = genURL($useData['label']);
		}
		
		$edit = $this->edit('profile_fields', $id, $useData);
		if(!$edit){
			throw new \Exception('Error editing field');
		}

		if(isset($data['groups'])){
			$this->delete('profile_fieldGroups', $id, 'fieldId');
			foreach($data['groups'] as $group){
				$this->insert('profile_fieldGroups', array('fieldId' => $id, 'groupId' => $group));
			}
		}
		return true;
	}
}
