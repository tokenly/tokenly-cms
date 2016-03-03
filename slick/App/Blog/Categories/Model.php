<?php
namespace App\Blog;
use Core, UI, Util, App\Tokenly, App\Account;
class Categories_Model extends Core\Model
{

	protected function getBlogCategoryForm($appData, $categoryId = 0)
	{
		$form = new UI\Form;
		$form->setFileEnc();
		
		$blogId = new UI\Select('blogId');
		$blogId->setLabel('Choose Blog');
		$getBlogs = $this->getAll('blogs', array('siteId' => $appData['site']['siteId']));

		$blogPerms = array();
		$multiblog = new Multiblog_Model;
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
		
		$name = new UI\Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Category Name');
		$form->add($name);
		
		$slug = new UI\Textbox('slug');
		$slug->setLabel('Slug (blank to auto generate)');
		$form->add($slug);	
		
		$parentId = new UI\Select('parentId');
		$getThisCat = false;
		$thisBlogId = 0;
		if($categoryId != 0){
			$getThisCat = $this->get('blog_categories', $categoryId);
			if($getThisCat){
				$thisBlogId = $getThisCat['blogId'];
			}
		}
		$getCategories = $this->container->getCategoryFormList($appData['site']['siteId'], false, array(), 0, $categoryId, $thisBlogId);
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
	
		$rank = new UI\Textbox('rank');
		$rank->setLabel('Order Rank');
		$form->add($rank);	
		
		$description = new UI\Markdown('description', 'markdown');
		$description->setLabel('Description');
		$form->add($description);
		
		$image = new UI\File('image');
		$image->setLabel('Image');
		$form->add($image);
		
		$public = new UI\Checkbox('public', 'public');
		$public->setLabel('Public Category?');
		$public->setBool(1);
		$public->setValue(1);
		$form->add($public);

		return $form;
	}
	
	protected function getCategoryFormList($siteId, $cats = false, $output = array(), $indent = 0, $categoryId = 0, $blogId = 0)
	{
		if($cats === false){
			$getCats = $this->container->getCategories($siteId);
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
				$output = array_merge($this->container->getCategoryFormList($siteId, $cat['children'], $output, $newIndent, $categoryId, $blogId), $output);
			}
		}
		
		return $output;
	}
	
	
	protected function getCategories($siteId, $parentId = 0, $menuMode = 0, $use_tca = true)
	{
		$thisUser = false;
		$accountModel = new Account\Auth_Model;
		$sesh_auth = Util\Session::get('accountAuth');
		if($sesh_auth){
			$getUser = $accountModel->checkSession($sesh_auth);
			if($getUser){
				$thisUser = $getUser['userId'];
			}
		}
		$tca = new Tokenly\TCA_Model;
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
			
			if($use_tca){
				$catTCA = $tca->checkItemAccess($thisUser, $catModule['moduleId'], $row['categoryId'], 'blog-category');
				$blogTCA = $tca->checkItemAccess($thisUser, $catModule['moduleId'], $row['blogId'], 'multiblog');
				if(!$catTCA OR !$blogTCA){
					unset($get[$key]);
					continue;
				}	
			}
			
			$getChildren = $this->container->getCategories($siteId, $row['categoryId'], $menuMode, $use_tca);
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
	
	protected function addBlogCategory($data, $user)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'parentId' => false, 'rank' => false, 'description' => false, 'public' => false, 'blogId' => true);
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
		
		if(intval($useData['public']) == 1){
			$useData['public'] = 1;
		}
		else{
			$useData['public'] = 0;
		}
		
		if($useData['parentId'] > 0){
			$getParent = $this->get('blog_categories', $useData['parentId']);
			if(!$getParent OR $getParent['blogId'] != $data['blogId']){
				throw new \Exception('Invalid parent category');
			}
		}
		
		$multiblog = new Multiblog_Model;
		$getBlog = $multiblog->get('blogs', $data['blogId']);
		if(!$getBlog){
			throw new \Exception('Invalid blog');
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
			throw new \Exception('You do not have permission to create a category on this blog.');
		}		
		
		$add = $this->insert('blog_categories', $useData);
		if(!$add){
			throw new \Exception('Error adding category');
		}
		
		$this->container->uploadImage($add);
		
		return $add;
		
		
	}
		
	protected function editBlogCategory($id, $data)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'parentId' => false, 'rank' => false, 'description' => false, 'public' => false);
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
				throw new \Exception('Invalid parent category');
			}
		}		
		
		$edit = $this->edit('blog_categories', $id, $useData);
		if(!$edit){
			throw new \Exception('Error editing category');
		}
		
		$this->container->uploadImage($id);
		
		return true;
		
	}
	
	protected function uploadImage($categoryId)
	{
		if(isset($_FILES['image']['tmp_name']) AND trim($_FILES['image']['tmp_name']) != ''){
			$getApp = $this->get('apps', 'blog', array(), 'slug');
			$meta = new \App\Meta_Model;
			$appMeta = $meta->appMeta($getApp['appId']);
			$fileName = md5('category-'.$categoryId.'-'.$_FILES['image']['name']).'.jpg';
			$image = new Util\Image;
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
	
	protected function getArchiveList($siteId)
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
