<?php
class Slick_App_CMS_ProfileFields_Model extends Slick_Core_Model
{

	public function getFieldForm($fieldId = 0)
	{
		$form = new Slick_UI_Form;
		
		$label = new Slick_UI_Textbox('label');
		$label->addAttribute('required');
		$label->setLabel('Label');
		$form->add($label);
		
		$slug = new Slick_UI_Textbox('slug');
		$slug->setLabel('Slug');
		$form->add($slug);		
		
		$type = new Slick_UI_Select('type');
		$type->setLabel('Field Type');
		$type->addOption('textbox', 'Textbox');
		$type->addOption('textarea', 'Textarea');
		$type->addOption('select', 'Select Dropdown');
		$form->add($type);
		
		$options = new Slick_UI_Textarea('options');
		$options->setLabel('Options (if dropdown) - 1 per line');
		$form->add($options);
		
		$public = new Slick_UI_Checkbox('public', 'public');
		$public->setLabel('Publicly viewable field?');
		$public->setBool(1);
		$public->setValue(1);
		$form->add($public);
		
		$active = new Slick_UI_Checkbox('active', 'active');
		$active->setLabel('Active?');
		$active->setBool(1);
		$active->setValue(1);
		$form->add($active);

		$rank = new Slick_UI_Textbox('rank');
		$rank->setLabel('Order Rank');
		$form->add($rank);
		
		$getGroups = $this->getAll('groups');
		$groups = new Slick_UI_CheckboxList('groups');
		foreach($getGroups as $group){
			$groups->addOption($group['groupId'], $group['name']);
		}
		$groups->setLabel('Groups');
		$groups->setLabelDir('R');
		$form->add($groups);
		
		return $form;
	}
	


	public function addField($data)
	{
		$req = array('label' => true, 'type' => true, 'options' => false, 'public' => false, 'active' => false, 'siteId' => true, 'rank' => false, 'slug' => false);
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
		
		if(trim($useData['slug']) == ''){
			$useData['slug'] = genURL($useData['label']);
		}		
		
		$add = $this->insert('profile_fields', $useData);
		if(!$add){
			throw new Exception('Error adding field');
		}
		
		if(isset($data['groups'])){
			foreach($data['groups'] as $group){
				$this->insert('profile_fieldGroups', array('fieldId' => $add, 'groupId' => $group));
			}
		}
		
		return $add;
		
		
	}
		
	public function editField($id, $data)
	{
		$req = array('label' => true, 'type' => true, 'options' => false, 'public' => false, 'active' => false, 'siteId' => true, 'rank' => false, 'slug' => false);
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
		
		if(trim($useData['slug']) == ''){
			$useData['slug'] = genURL($useData['label']);
		}
		
		$edit = $this->edit('profile_fields', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing field');
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

?>
