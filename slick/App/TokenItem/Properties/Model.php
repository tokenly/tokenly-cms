<?php
namespace App\TokenItem;
use Core, UI, Util;
class Properties_Model extends Core\Model
{
	protected function getPropertyForm()
	{
		$form = new UI\Form;
		
		$label = new UI\Textbox('name');
		$label->setLabel('Property Name');
		$form->add($label);
			
		$desc = new UI\Markdown('description', 'markdown');
		$desc->setLabel('Description');
		$form->add($desc);
		
		$type = new UI\Select('type');
		$type->setLabel('Value Type');
		$type->addOption('text', 'Text field');
		$type->addOption('select', 'Dropdown Select');
		$form->add($type);
		
		$options = new UI\Textarea('options');
		$options->setLabel('Options');
		$options->addAttribute('placeholder', '(one per line)');
		$form->add($options);
		
		$rank = new UI\Textbox('rank');
		$rank->setLabel('Order Rank');
		$rank->setValue(0);
		$form->add($rank);		
		
		$active = new UI\Checkbox('active', 'active');
		$active->setLabel('Active?');
		$active->setBool(true);
		$active->setValue(1);
		$form->add($active);
		
		return $form;
	}
	
	protected function addItemProperty($data)
	{
		if(!isset($data['name']) OR trim($data['name']) == ''){
			throw new \Exception('Name required');
		}
		$active = 0;
		if(isset($data['active']) AND intval($data['active']) === 1){
			$active = 1;
		}
		
		$insertData = array();
		$insertData['name'] = trim(htmlentities($data['name']));
		$insertData['active'] = $active;
		if(isset($data['description'])){
			$insertData['description'] = trim(htmlentities($data['description']));
		}
		$valid_types = array('text', 'select');
		$insertData['type'] = 'text';
		if(isset($data['type']) AND in_array($data['type'], $valid_types)){
			$insertData['type'] = $data['type'];
		}
		if(isset($data['options'])){
			$insertData['options'] = trim($data['options']);
		}

		$insertData['created_at'] = timestamp();
		$insertData['updated_at'] = timestamp();
		
		if(isset($data['rank'])){
			$insertData['rank'] = intval($data['rank']);
		}

		
		$insert = $this->insert('token_itemPropertyTypes', $insertData);
		if(!$insert){
			throw new \Exception('Error adding property type');
		}
		
		$insertData['id'] = $insert;
		return $insertData;
	}
	
	protected function editItemProperty($id, $data)
	{
		if(!isset($data['name']) OR trim($data['name']) == ''){
			throw new \Exception('Name required');
		}
		$active = 0;
		if(isset($data['active']) AND intval($data['active']) === 1){
			$active = 1;
		}
		
		$updateData = array();
		$updateData['name'] = trim(htmlentities($data['name']));
		$updateData['active'] = $active;
		if(isset($data['description'])){
			$updateData['description'] = trim(htmlentities($data['description']));
		}
		$valid_types = array('text', 'select');

		$updateData['type'] = 'text';
		if(isset($data['type']) AND in_array($data['type'], $valid_types)){
			$updateData['type'] = $data['type'];
		}
		if(isset($data['options'])){
			$updateData['options'] = trim($data['options']);
		}

		$updateData['updated_at'] = timestamp();
		
		if(isset($data['rank'])){
			$updateData['rank'] = intval($data['rank']);
		}		

		$edit = $this->edit('token_itemPropertyTypes', $id, $updateData);
		if(!$edit){
			throw new \Exception('Error editing Property Type');
		}

		return true;
	}
	
	protected function getActivePropertyTypes()
	{
		$get = $this->fetchAll('SELECT * FROM token_itemPropertyTypes WHERE active = 1 ORDER BY rank ASC, name ASC');
		return $get;
	}
	
}
