<?php
class Slick_App_CMS_Menus_Model extends Slick_Core_Model
{

	public function getMenuForm($menuId = 0)
	{
		$form = new Slick_UI_Form;
		
		$name = new Slick_UI_Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Menu Name');
		$form->add($name);
		
		$slug = new Slick_UI_Textbox('slug');
		$slug->addAttribute('required');
		$slug->setLabel('Slug');
		$form->add($slug);	

		return $form;
	}
	


	public function addMenu($data)
	{
		$req = array('name', 'slug', 'siteId');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$add = $this->insert('menus', $useData);
		if(!$add){
			throw new Exception('Error adding menu');
		}
		
		return $add;
		
		
	}
		
	public function editMenu($id, $data)
	{
		$req = array('name', 'slug', 'siteId');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		
		$edit = $this->edit('menus', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing menu');
		}
		
		
		return true;
		
	}





}

?>
