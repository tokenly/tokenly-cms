<?php
class Slick_App_Dashboard_PageTags_Model extends Slick_Core_Model
{

	public function getTagForm($tagId = 0)
	{
		$form = new Slick_UI_Form;
		
		$tag = new Slick_UI_Textbox('tag');
		$tag->addAttribute('required');
		$tag->setLabel('Tag');
		$form->add($tag);
		
		$class = new Slick_UI_Textbox('class');
		$class->addAttribute('required');
		$class->setLabel('Class');
		$form->add($class);	


		return $form;
	}
	


	public function addTag($data)
	{
		$req = array('tag', 'class');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$add = $this->insert('page_tags', $useData);
		if(!$add){
			throw new Exception('Error adding tag');
		}
		
		return $add;
		
		
	}
		
	public function editTag($id, $data)
	{
		$req = array('tag', 'class');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		
		$edit = $this->edit('page_tags', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing tag');
		}
		
		
		return true;
		
	}





}

?>
