<?php
namespace App\Forum;
use Core, UI, Util;
class Categories_Model extends Core\Model
{
	protected function getCategoryForm($categoryId = 0)
	{
		$form = new UI\Form;
		
		$name = new UI\Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Category Name');
		$form->add($name);
		
		$slug = new UI\Textbox('slug');
		$slug->setLabel('Slug (blank to auto generate)');
		$form->add($slug);	
		
		$rank = new UI\Textbox('rank');
		$rank->setLabel('Order Rank');
		$form->add($rank);
		
		$description = new UI\Textarea('description', 'html-editor');
		$description->setLabel('Description');
		$form->add($description);

		return $form;
	}

	protected function addCategory($data)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'rank' => false, 'description' => false);
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
		
		if(!isset($useData['slug']) OR trim($useData['slug']) == ''){
			$useData['slug'] = genURL($useData['name']);
		}
		
		$add = $this->insert('forum_categories', $useData);
		if(!$add){
			throw new \Exception('Error adding category');
		}
		
		return $add;
	}
		
	protected function editCategory($id, $data)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'rank' => false, 'description' => false);
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
		
		if(!isset($useData['slug']) OR trim($useData['slug']) == ''){
			$useData['slug'] = genURL($useData['name']);
		}
		
		$edit = $this->edit('forum_categories', $id, $useData);
		if(!$edit){
			throw new \Exception('Error editing category');
		}
		return true;
	}
}
