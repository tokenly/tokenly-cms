<?php
namespace App\Store;
use Core, UI, Util;
class Categories_Model extends Core\Model
{
	protected function getStoreCategoryForm($siteId, $categoryId = 0)
	{
		$form = new UI\Form;
		
		$name = new UI\Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Category Name');
		$form->add($name);
		
		$slug = new UI\Textbox('slug');
		$slug->setLabel('Slug (blank to auto generate)');
		$form->add($slug);	
		
		$parentId = new UI\Select('parentId');
		$getCategories = $this->container->getCategoryFormList($siteId, false, array(), 0, $categoryId);
		$parentId->addOption(0, '-Root-');
		foreach($getCategories as $cat){
			$parentId->addOption($cat['categoryId'], $cat['name']);
		}
		$parentId->setLabel('Parent');
		$form->add($parentId);
	
		$rank = new UI\Textbox('rank');
		$rank->setLabel('Order Rank');
		$form->add($rank);	
		
		$description = new UI\Textarea('description', 'html-editor');
		$description->setLabel('Description');
		$form->add($description);
		
		$active = new UI\Checkbox('active');
		$active->setBool(1);
		$active->setValue(1);
		$active->setLabel('Active?');
		$form->add($active);

		return $form;
	}
	
	protected function getCategoryFormList($siteId, $cats = false, $output = array(), $indent = 0, $categoryId = 0)
	{
		if($cats === false){
			$getCats = $this->container->getCategories($siteId);
		}
		else{
			$getCats = $cats;
		}
		foreach($getCats as $cat){
			if($cat['categoryId'] == $categoryId){
				continue;
			}
			$indenter = '';
			for($i = 0; $i < $indent; $i++){
				$indenter .= '&nbsp;&nbsp;&nbsp;';
			}
			$row = array('categoryId' => $cat['categoryId'], 'name' => $indenter.$cat['name'], 'parentId' => $cat['parentId']);
			$output[] = $row;
			if(isset($cat['children'])){
				$output = array_merge($this->container->getCategoryFormList($siteId, $cat['children'], $output, ($indent+1), $categoryId), $output);
			}
		}
		return $output;
	}
	
	
	protected function getCategories($siteId, $parentId = 0, $menuMode = 0)
	{
		$get = $this->getAll('store_categories', array('parentId' => $parentId, 'siteId' => $siteId), array(), 'rank', 'asc');
		foreach($get as $key => $row){
			$getChildren = $this->container->getCategories($siteId, $row['categoryId'], $menuMode);
			if(count($getChildren) > 0){
				$get[$key]['children'] = $getChildren;
			}
			if($menuMode == 1){
				$get[$key]['target'] = '';
				$get[$key]['url'] = SITE_URL.'/store/category/'.$row['slug'];
				$get[$key]['label'] = $row['name'];
			}
			
		}
		return $get;
	}

	protected function addStoreCategory($data)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'parentId' => false, 'rank' => false, 'description' => false, 'active' => false);
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
			$useData['slug'] = genURL($useData['name']);
		}
		
		$add = $this->insert('store_categories', $useData);
		if(!$add){
			throw new \Exception('Error adding category');
		}
		
		return $add;
	}
		
	protected function editStoreCategory($id, $data)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'parentId' => false, 'rank' => false, 'description' => false, 'active' => false);
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
			$useData['slug'] = genURL($useData['name']);
		}
		
		$edit = $this->edit('store_categories', $id, $useData);
		if(!$edit){
			throw new \Exception('Error editing category');
		}
		return true;
	}
}
