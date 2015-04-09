<?php
class Slick_App_Blog_Categories_Model extends Slick_Core_Model
{

	public function getBlogCategoryForm($appData, $categoryId = 0)
	{
		$form = new Slick_UI_Form;
		$form->setFileEnc();
		
		$blogId = new Slick_UI_Select('blogId');
		$blogId->setLabel('Choose Blog');
		$getBlogs = $this->getAll('blogs', array('siteId' => $appData['site']['siteId']));

		$blogPerms = array();
		$multiblog = new Slick_App_Blog_Multiblog_Model;
		foreach($getBlogs as $blog){
			$getRoles =  $multiblog->getBlogUserRoles($blog['blogId']);
			
			$is_admin = false;
			if($blog['userId'] == $appData['user']['userId'] OR $appData['perms']['canManageAllBlogs']){
				$is_admin = true;
			}
			else{
				foreach($getRoles as $rk => $role){
					if($role['userId'] == $appData['user']['userId'] AND $role['type'] == 'admin'){
						$is_admin = true;
					}
				}
			}
			$blogPerms[$blog['blogId']]['roles'] = $getRoles;
			$blogPerms[$blog['blogId']]['is_admin'] = $is_admin;
			if(!$is_admin){
				continue;
			}
			$blogId->addOption($blog['blogId'], $blog['name']);
		}
		$form->add($blogId);
		
		$name = new Slick_UI_Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Category Name');
		$form->add($name);
		
		$slug = new Slick_UI_Textbox('slug');
		$slug->setLabel('Slug (blank to auto generate)');
		$form->add($slug);	
		
		$parentId = new Slick_UI_Select('parentId');
		$getThisCat = false;
		$thisBlogId = 0;
		if($categoryId != 0){
			$getThisCat = $this->get('blog_categories', $categoryId);
			if($getThisCat){
				$thisBlogId = $getThisCat['blogId'];
			}
		}
		$getCategories = $this->getCategoryFormList($appData['site']['siteId'], false, array(), 0, $categoryId, $thisBlogId);
		$parentId->addOption(0, '-Root-');
		$firstBlogId = false;
		foreach($getCategories as $cat){
			if(!$blogPerms[$cat['blogId']]['is_admin']){
				continue;
			}
			$parentId->addOption($cat['categoryId'], $cat['name']);
			$parentId->addOptAttribute($cat['categoryId'], 'data-blog', $cat['blogId']);
			if(!$firstBlogId){
				$firstBlogId = $cat['blogId'];
			}
			if($categoryId == 0 AND $cat['blogId'] != $firstBlogId){
				$parentId->addOptAttribute($cat['categoryId'], 'hidden', 'hidden');
			}
		}
		$parentId->setLabel('Parent');
		$form->add($parentId);
	
		$rank = new Slick_UI_Textbox('rank');
		$rank->setLabel('Order Rank');
		$form->add($rank);	
		
		$description = new Slick_UI_Markdown('description', 'markdown');
		$description->setLabel('Description');
		$form->add($description);
		
		$image = new Slick_UI_File('image');
		$image->setLabel('Image');
		$form->add($image);
		
		$public = new Slick_UI_Checkbox('public', 'public');
		$public->setLabel('Public Category?');
		$public->setBool(1);
		$public->setValue(1);
		$form->add($public);

		return $form;
	}
	
	public function getCategoryFormList($siteId, $cats = false, $output = array(), $indent = 0, $categoryId = 0, $blogId = 0)
	{
		if($cats === false){
			$getCats = $this->getCategories($siteId);
		}
		else{
			$getCats = $cats;
		}

		foreach($getCats as $cat){
			if($cat['categoryId'] == $categoryId OR ($blogId != 0 AND $cat['blogId'] != $blogId)){
				continue;
			}
			$indenter = '';
			if($indent !== false){
				for($i = 0; $i < $indent; $i++){
					$indenter .= '&nbsp;&nbsp;&nbsp;';
				}
				
			}
			$row = array('categoryId' => $cat['categoryId'], 'name' => $indenter.$cat['name'], 'parentId' => $cat['parentId'], 'blogId' => $cat['blogId']);
			$output[] = $row;
			if(isset($cat['children']) AND count($cat['children']) > 0){
				$newIndent = $indent;
				if($indent !== false){
					$newIndent = $indent+1;
				}
				$output = array_merge($this->getCategoryFormList($siteId, $cat['children'], $output, $newIndent, $categoryId, $blogId), $output);
			}
		}
		
		return $output;
	}
	
	
	public function getCategories($siteId, $parentId = 0, $menuMode = 0)
	{
		$thisUser = false;
		$accountModel = new Slick_App_Account_Home_Model;
		if(isset($_SESSION['accountAuth'])){
			$getUser = $accountModel->checkSession($_SESSION['accountAuth']);
			if($getUser){
				$thisUser = $getUser['userId'];
			}
		}
		$tca = new Slick_App_Tokenly_TCA_Model;
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
		$getSite = $this->get('sites', $siteId);
		
		$get = $this->getAll('blog_categories', array('parentId' => $parentId, 'siteId' => $siteId), array(), 'rank', 'asc');
		foreach($get as $key => $row){
			$getBlog = $this->get('blogs', $row['blogId']);
			if($getBlog['active'] == 0){
				unset($get[$key]);
				continue;
			}
			$row['blog'] = $getBlog;
			$get[$key] = $row;
			
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
				$get[$key]['url'] = $getSite['url'].'/blog/category/'.$row['slug'];
				$get[$key]['label'] = $row['name'];
				$get[$key]['value'] = $row['categoryId'];
			}
			
			if(isset($_SERVER['is_api'])){
				if($row['image'] != ''){
					$get[$key]['image'] = $getSite['url'].'/files/blogs/'.$row['image'];
				}
			}
		}
		return $get;
	}
	
	public function addBlogCategory($data, $user)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'parentId' => false, 'rank' => false, 'description' => false, 'public' => false, 'blogId' => true);
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
		
		if(intval($useData['public']) == 1){
			$useData['public'] = 1;
		}
		else{
			$useData['public'] = 0;
		}
		
		if($useData['parentId'] > 0){
			$getParent = $this->get('blog_categories', $useData['parentId']);
			if(!$getParent OR $getParent['blogId'] != $data['blogId']){
				throw new Exception('Invalid parent category');
			}
		}
		
		$multiblog = new Slick_App_Blog_Multiblog_Model;
		$getBlog = $multiblog->get('blogs', $data['blogId']);
		if(!$getBlog){
			throw new Exception('Invalid blog');
		}
		
		$getRoles =  $multiblog->getBlogUserRoles($data['blogId']);
		$is_admin = false;
		if($getBlog['userId'] == $user['userId']){
			$is_admin = true;
		}
		else{
			foreach($getRoles as $rk => $role){
				if($role['userId'] == $user['userId'] AND $role['type'] == 'admin'){
					$is_admin = true;
				}
			}
		}

		if(!$is_admin AND !$user['perms']['canManageAllBlogs']){
			throw new Exception('You do not have permission to create a category on this blog.');
		}		
		
		$add = $this->insert('blog_categories', $useData);
		if(!$add){
			throw new Exception('Error adding category');
		}
		
		$this->uploadImage($add);
		
		return $add;
		
		
	}
		
	public function editBlogCategory($id, $data)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'parentId' => false, 'rank' => false, 'description' => false, 'public' => false);
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
		
		if(intval($useData['public']) == 1){
			$useData['public'] = 1;
		}
		else{
			$useData['public'] = 0;
		}		
		
		$thisCategory = $this->get('blog_categories', $id);
		if($useData['parentId'] > 0){
			$getParent = $this->get('blog_categories', $useData['parentId']);
			if(!$getParent OR $getParent['blogId'] != $thisCategory['blogId'] OR $getParent['categoryId'] == $id OR $getParent['parentId'] == $id){
				throw new Exception('Invalid parent category');
			}
		}		
		
		$edit = $this->edit('blog_categories', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing category');
		}
		
		$this->uploadImage($id);
		
		return true;
		
	}
	
	public function uploadImage($categoryId)
	{
		if(isset($_FILES['image']['tmp_name']) AND trim($_FILES['image']['tmp_name']) != ''){
			$getApp = $this->get('apps', 'blog', array(), 'slug');
			$meta = new Slick_App_Meta_Model;
			$appMeta = $meta->appMeta($getApp['appId']);
			$fileName = md5('category-'.$categoryId.'-'.$_FILES['image']['name']).'.jpg';
			$image = new Slick_Util_Image;
			$imageWidth = 200;
			$imageHeight = 200;
			if(isset($appMeta['category-image-width'])){
				$imageWidth = intval($appMeta['category-image-width']);
			}
			if(isset($appMeta['category-image-height'])){
				$imageHeight = intval($appMeta['category-image-height']);
			}
			$saveImage = $image->resizeImage($_FILES['image']['tmp_name'], SITE_PATH.'/files/blogs/'.$fileName, $imageWidth, $imageHeight);
			if($saveImage){
				$this->edit('blog_categories', $categoryId, array('image' => $fileName));
			}
		}
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
