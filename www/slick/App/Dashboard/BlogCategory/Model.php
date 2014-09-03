<?php
class Slick_App_Dashboard_BlogCategory_Model extends Slick_Core_Model
{

	public function getBlogCategoryForm($siteId, $categoryId = 0)
	{
		$form = new Slick_UI_Form;
		
		$name = new Slick_UI_Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Category Name');
		$form->add($name);
		
		$slug = new Slick_UI_Textbox('slug');
		$slug->setLabel('Slug (blank to auto generate)');
		$form->add($slug);	
		
		$parentId = new Slick_UI_Select('parentId');
		$getCategories = $this->getCategoryFormList($siteId, false, array(), 0, $categoryId);
		$parentId->addOption(0, '-Root-');
		foreach($getCategories as $cat){
			$parentId->addOption($cat['categoryId'], $cat['name']);
		}
		$parentId->setLabel('Parent');
		$form->add($parentId);
	
		$rank = new Slick_UI_Textbox('rank');
		$rank->setLabel('Order Rank');
		$form->add($rank);	
		
		$description = new Slick_UI_Textarea('description', 'html-editor');
		$description->setLabel('Description');
		$form->add($description);

		return $form;
	}
	
	public function getCategoryFormList($siteId, $cats = false, $output = array(), $indent = 0, $categoryId = 0)
	{
		if($cats === false){
			$getCats = $this->getCategories($siteId);
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
				$output = array_merge($this->getCategoryFormList($siteId, $cat['children'], $output, ($indent+1), $categoryId), $output);
			}
		}
		
		return $output;
	}
	
	
	public function getCategories($siteId, $parentId = 0, $menuMode = 0)
	{
		$thisUser = false;
		if(isset($_SESSION['accountAuth'])){
			$getUser = $this->get('users', $_SESSION['accountAuth'], array('userId'), 'auth');
			if($getUser){
				$thisUser = $getUser['userId'];
			}
		}
		$tca = new Slick_App_LTBcoin_TCA_Model;
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
		
		$get = $this->getAll('blog_categories', array('parentId' => $parentId, 'siteId' => $siteId), array(), 'rank', 'asc');
		foreach($get as $key => $row){
			$catTCA = $tca->checkItemAccess($thisUser, $catModule['moduleId'], $row['categoryId'], 'blog-category');
			if(!$catTCA){
				unset($get[$key]);
				continue;
			}	
			
			$getChildren = $this->getCategories($siteId, $row['categoryId'], $menuMode);
			if(count($getChildren) > 0){
				$get[$key]['children'] = $getChildren;
			}
			if($menuMode == 1){
				$get[$key]['target'] = '';
				$get[$key]['url'] = SITE_URL.'/blog/category/'.$row['slug'];
				$get[$key]['label'] = $row['name'];
			}
			
		}
	
		
		return $get;
		
	}
	


	public function addBlogCategory($data)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'parentId' => false, 'rank' => false, 'description' => false);
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
			$useData['slug'] = genURL($useData['name']);
		}
		
		$add = $this->insert('blog_categories', $useData);
		if(!$add){
			throw new Exception('Error adding category');
		}
		
		return $add;
		
		
	}
		
	public function editBlogCategory($id, $data)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'parentId' => false, 'rank' => false, 'description' => false);
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
			$useData['slug'] = genURL($useData['name']);
		}
		
		
		$edit = $this->edit('blog_categories', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing category');
		}
		
		
		return true;
		
	}
	
	public function getArchiveList($siteId)
	{
		$getPosts = $this->fetchAll('SELECT postId, publishDate 
									FROM blog_posts
									WHERE siteId = :siteId
									AND published = 1
									AND publishDate <= "'.timestamp().'"
									ORDER BY publishDate DESC',
									array(':siteId' => $siteId));
		$getSite = $this->get('sites', $siteId);
		$dates = array();
		foreach($getPosts as $post){
			$dKey = date('Y-m', strtotime($post['publishDate']));
			$year = date('Y', strtotime($post['publishDate']));
			$month = date('m', strtotime($post['publishDate']));
			if(!isset($dates[$dKey])){
				$dates[$dKey] = array('url' => $getSite['url'].'/blog/archive/'.$year.'/'.$month,
									  'target' => '',
									  'label' => date('F Y', strtotime($post['publishDate'])));
			}
		}
		
		return $dates;
		
	}
	
	





}

?>
