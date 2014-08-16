<?php
class Slick_App_Dashboard_ForumBoard_Model extends Slick_Core_Model
{

	public function getBoardForm($siteId)
	{
		$form = new Slick_UI_Form;
		
		$getCats = $this->getAll('forum_categories', array('siteId' => $siteId), array(), 'rank', 'asc');
		$categoryId = new Slick_UI_Select('categoryId');
		$categoryId->setLabel('Board Category');
		$categoryId->addOption(0, '[none]');
		foreach($getCats as $cat){
			$categoryId->addOption($cat['categoryId'], $cat['name']);
		}
		$form->add($categoryId);
		
		$name = new Slick_UI_Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Board Name');
		$form->add($name);
		
		$slug = new Slick_UI_Textbox('slug');
		$slug->setLabel('Slug / URL (blank to auto generate)');
		$form->add($slug);	
		
		$rank = new Slick_UI_Textbox('rank');
		$rank->setLabel('Order Rank');
		$form->add($rank);
		
		$active = new Slick_UI_Checkbox('active');
		$active->setLabel('Board Active?');
		$active->setBool(1);
		$active->setValue(1);
		$form->add($active);
		
		$description = new Slick_UI_Textarea('description', 'html-editor');
		$description->setLabel('Description');
		$form->add($description);

		return $form;
	}
	


	public function addBoard($data)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'rank' => false, 'description' => false, 'active' => false, 'categoryId' => false);
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
		
		if(!isset($useData['slug']) OR trim($useData['slug']) == ''){
			$useData['slug'] = genURL($useData['name']);
		}
		
		$add = $this->insert('forum_boards', $useData);
		if(!$add){
			throw new Exception('Error adding board');
		}
		
		return $add;
		
		
	}
		
	public function editBoard($id, $data)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'rank' => false, 'description' => false, 'active' => false, 'categoryId' => false);
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
		
		if(!isset($useData['slug']) OR trim($useData['slug']) == ''){
			$useData['slug'] = genURL($useData['name']);
		}
		
		
		$edit = $this->edit('forum_boards', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing board');
		}
		
		
		return true;
		
	}





}

?>
