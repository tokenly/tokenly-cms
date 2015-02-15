<?php
class Slick_App_Dashboard_Blog_Multiblog_Model extends Slick_Core_Model
{

	public function getBlogForm($siteId)
	{
		$form = new Slick_UI_Form;
		$form->setFileEnc();
		
		$name = new Slick_UI_Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Blog Title');
		$form->add($name);
		
		$slug = new Slick_UI_Textbox('slug');
		$slug->setLabel('Slug (leave blank to auto generate)');
		$form->add($slug);		
				
		$ownerId = new Slick_UI_Select('userId');
		$ownerId->setLabel('Blog Owner');
		$ownerId->addOption(0, '[nobody]');
		$getUsers = $this->getAll('users');
		foreach($getUsers as $user){
			$ownerId->addOption($user['userId'], $user['username']);
		}
		$form->add($ownerId);
		
		$description = new Slick_UI_Markdown('description', 'markdown');
		$description->setLabel('Description (use markdown)');
		$form->add($description);		
		
		$image = new Slick_UI_File('image');
		$image->setLabel('Image');
		$form->add($image);		
		
		$active = new Slick_UI_Checkbox('active');
		$active->setLabel('Blog Active?');
		$active->setBool(1);
		$active->setValue(1);
		$form->add($active);		
		
		return $form;
	}
	


	public function addBlog($data)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'description' => false, 'active' => false);
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
		$useData['slug'] = $this->checkURLExists($useData['slug']);
		$useData['name'] = strip_tags($useData['name']);
		$useData['description'] = strip_tags($useData['description']);
		$useData['created_at'] = timestamp();
		$useData['updated_at'] = $useData['created_at'];
		
		if(isset($data['userId'])){
			$useData['userId'] = $data['userId'];
		}
		
		$add = $this->insert('blogs', $useData);
		if(!$add){
			throw new Exception('Error creating blog');
		}
		
		$this->uploadImage($add);
		
		return $add;
	}
	
	public function checkURLExists($url, $ignore = 0, $count = 0)
	{
		$useurl = $url;
		if($count > 0){
			$useurl = $url.'-'.$count;
		}
		$get = $this->get('blogs', $useurl, array('blogId', 'slug'), 'slug');
		if($get AND $get['blogId'] != $ignore){
			//url exists already, search for next level of url
			$count++;
			return $this->checkURLExists($url, $ignore, $count);
		}
		
		if($count > 0){
			$url = $url.'-'.$count;
		}

		return $url;
	}	
		
	public function editBlog($id, $data)
	{
		$req = array('name' => true, 'slug' => false, 'description' => false, 'active' => false);
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
		$useData['slug'] = $this->checkURLExists($useData['slug'], $id);
		$useData['name'] = strip_tags($useData['name']);
		$useData['description'] = strip_tags($useData['description']);
		
		if(isset($data['userId'])){
			$useData['userId'] = $data['userId'];
		}
		
		$edit = $this->edit('blogs', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing blog');
		}
		
		$this->uploadImage($id);
			
		return true;
	}


	public function getBlogUserRoles($blogId, $includeOwner = false)
	{
		$sql = 'SELECT u.userId, u.username, u.email, u.slug, r.type
				FROM blog_roles r
				LEFT JOIN users u ON r.userId = u.userId
				WHERE r.blogId = :blogId
				ORDER BY u.username ASC';
		$get = $this->fetchAll($sql, array(':blogId' => $blogId));
		if($includeOwner){
			$getBlog = $this->get('blogs', $blogId);
			$getUser = $this->get('users', $getBlog['userId'], array('userId', 'username', 'slug'));
			$getUser['type'] = 'owner';
			$get[] = $getUser;
		}
		return $get;
	}

	public function getBlogRoleForm()
	{
		$form = new Slick_UI_Form;
		
		$id = new Slick_UI_Textbox('roleUserId');
		$id->setLabel('Add New Role');
		$id->addAttribute('placeholder', 'Username or User ID');
		$form->add($id);
		
		$type = new Slick_UI_Select('roleType');
		$type->setLabel('Type');
		$type->addOption('writer', 'Writer');
		$type->addOption('independent-writer', 'Independent Writer');
		$type->addOption('editor', 'Editor');
		$type->addOption('admin', 'Blog Admin');
		$form->add($type);
		
		$form->setSubmitText('Add Role');
		
		return $form;
	}
	
	public function addBlogRole($blogId, $userId, $type)
	{
		
		$userId = trim($userId);
		$get = $this->get('users', $userId, array(), 'username');
		if(!$get){
			$get = $this->get('users', intval($userId));
			if(!$get){
				throw new Exception('User not found');
			}
		}
		$userId = $get['userId'];
		
		$getRole = $this->getAll('blog_roles', array('userId' => $userId, 'blogId' => $blogId));
		
		if(count($getRole) > 0){
			throw new Exception('User already assigned a role!');
		}
		
		$add =  $this->insert('blog_roles', array('userId' => $get['userId'], 'blogId' => $blogId, 'type' => $type, 'created_at' => timestamp()));
		if(!$add){
			throw new Exception('Error adding user role');
		}
		
		if($type == 'editor'){
			$editorGroup = $this->get('groups', 'blog-editor', array(), 'slug');
			if($editorGroup){
				$userEditor = $this->fetchSingle('SELECT * FROM group_users WHERE userId = :userId AND groupId = :groupId',
												 array(':userId' => $get['userId'], ':groupId' => $editorGroup['groupId']));
				if(!$userEditor){
					$this->insert('group_users', array('userId' => $get['userId'], 'groupId' => $editorGroup['groupId']));
				}
			}
		}
		elseif($type == 'admin'){
			$ownerGroup = $this->get('groups', 'blog-owner', array(), 'slug');
			if($ownerGroup){
				$userOwner = $this->fetchSingle('SELECT * FROM group_users WHERE userId = :userId AND groupId = :groupId',
												 array(':userId' => $get['userId'], ':groupId' => $ownerGroup['groupId']));
				if(!$userOwner){
					$this->insert('group_users', array('userId' => $get['userId'], 'groupId' => $ownerGroup['groupId']));
				}
			}			
		}
		
		return $add;
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
				$this->edit('blogs', $categoryId, array('image' => $fileName));
			}
		}
	}
}

?>
