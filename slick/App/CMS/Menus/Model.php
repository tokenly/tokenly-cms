<?php
namespace App\CMS;
use Core, UI, Util;
class Menus_Model extends Core\Model
{
	protected function getMenuForm($menuId = 0)
	{
		$form = new UI\Form;
	
		$name = new UI\Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Menu Name');
		$form->add($name);
		
		$slug = new UI\Textbox('slug');
		$slug->addAttribute('required');
		$slug->setLabel('Slug');
		$form->add($slug);	

		return $form;
	}
	
	protected function addMenu($data)
	{
		$req = array('name', 'slug', 'siteId');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new \Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$add = $this->insert('menus', $useData);
		if(!$add){
			throw new \Exception('Error adding menu');
		}
		
		return $add;
	}
		
	protected function editMenu($id, $data)
	{
		$req = array('name', 'slug', 'siteId');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new \Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$edit = $this->edit('menus', $id, $useData);
		if(!$edit){
			throw new \Exception('Error editing menu');
		}
		return true;
	}
}
