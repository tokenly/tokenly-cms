<?php
class Slick_App_Dashboard_ForumCategory_Model extends Slick_Core_Model
{

	public function getCategoryForm($categoryId = 0)
	{
		$form = new Slick_UI_Form;
		
		$name = new Slick_UI_Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Category Name');
		$form->add($name);
		
		$slug = new Slick_UI_Textbox('slug');
		$slug->setLabel('Slug (blank to auto generate)');
		$form->add($slug);	
		
		$rank = new Slick_UI_Textbox('rank');
		$rank->setLabel('Order Rank');
		$form->add($rank);
		
		$description = new Slick_UI_Textarea('description', 'html-editor');
		$description->setLabel('Description');
		$form->add($description);

		return $form;
	}
	


	public function addCategory($data)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'rank' => false, 'description' => false);
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
		
		$add = $this->insert('forum_categories', $useData);
		if(!$add){
			throw new Exception('Error adding category');
		}
		
		return $add;
		
		
	}
		
	public function editCategory($id, $data)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'rank' => false, 'description' => false);
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
		
		
		$edit = $this->edit('forum_categories', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing category');
		}
		
		
		return true;
		
	}





}

?>
