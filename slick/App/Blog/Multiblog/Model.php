<?php
class Slick_App_Blog_Multiblog_Model extends Slick_Core_Model
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
		$sql = 'SELECT r.userRoleId, u.userId, u.username, u.email, u.slug, r.type, r.token
				FROM blog_roles r
				LEFT JOIN users u ON r.userId = u.userId
				WHERE r.blogId = :blogId
				ORDER BY r.type ASC, r.userId ASC';
		$get = $this->fetchAll($sql, array(':blogId' => $blogId));
		$scout = new Slick_App_Tokenly_AssetScout_Model;
		
		$roleList = array();
		$usedUsers = array();
		foreach($get as $k => $row){
			$subList = false;
			if($row['userId'] == 0 AND $row['token'] != ''){
				//get all users that hold some of this token
				try{
					$getTokenUsers = $scout->scoutAsset(array('asset' => $row['token']));
					
				}
				catch(Exception $e){
					$getTokenUsers = false;
				}
				if(!$getTokenUsers){
					continue;
				}
				foreach($getTokenUsers['list'] as $tokenUser){
					if(in_array($tokenUser['userId'], $usedUsers)){
						continue;
					}
					$usedUsers[] = $tokenUser['userId'];
					$tokenUser['type'] = $row['type'];
					$tokenUser['token_user'] = true;
					$tokenUser['token'] = $row['token'];
					$subList[] = $tokenUser;
				}
			}
			else{
				if(in_array($row['userId'], $usedUsers)){
					continue;
				}
				$usedUsers[] = $row['userId'];				
				$row['token_user'] = false;
			}
			$roleList[] = $row;
			if($subList){
				foreach($subList as $sub){
					$roleList[] = $sub;
				}
			}
		}
		
		if($includeOwner){
			$getBlog = $this->get('blogs', $blogId);
			$getUser = $this->get('users', $getBlog['userId'], array('userId', 'username', 'slug'));
			$getUser['type'] = 'owner';
			$getUser['token'] = '';
			$roleList[] = $getUser;
		}
		return $roleList;
	}

	public function getBlogRoleForm()
	{
		$form = new Slick_UI_Form;
		
		$id = new Slick_UI_Textbox('roleUserId');
		$id->setLabel('Add New Role');
		$id->addAttribute('placeholder', 'Username, User ID or token:MYTOKEN');
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
	
	public function addBlogRole($blogId, $userId, $type, $user)
	{
		
		$userId = trim($userId);
		$isUser = false;
		$expUserId = explode(':', $userId);
		$roleData = array('blogId' => $blogId, 'type' => $type, 'created_at' => timestamp());
		if(isset($expUserId[1]) AND $expUserId[0] == 'token'){
			$getRole = $this->getAll('blog_roles', array('token' => $expUserId[1], 'blogId' => $blogId));
			if(count($getRole) > 0){
				throw new Exception('Token already assigned a role!');
			}
			
			//add a tokenized role instead of single user
			$inventory = new Slick_App_Tokenly_Inventory_Model;
			$getAsset = $inventory->getAssetData($expUserId[1]);
			if(!$getAsset){
				throw new Exception('Invalid token name');
			}
			$roleData['token'] = $getAsset['asset'];
		}
		else{
			$get = $this->get('users', $userId, array(), 'username');
			if(!$get){
				$get = $this->get('users', intval($userId));
				if(!$get){
					throw new Exception('User not found');
				}
			}
			$getRole = $this->getAll('blog_roles', array('userId' => $get['userId'], 'blogId' => $blogId));
			if(count($getRole) > 0){
				throw new Exception('User already assigned a role!');
			}
			$roleData['userId'] = $get['userId'];
			$isUser = true;
		}
			
		$add =  $this->insert('blog_roles', $roleData);
		if(!$add){
			throw new Exception('Error adding user role');
		}
		
		if(!$isUser){
			//tokenized role, add in TCA rules
			$newsroom = $this->get('modules', 'blog-newsroom', array(), 'slug');
			$categories = $this->get('modules', 'blog-categories', array(), 'slug');
			$multiblogs = $this->get('modules', 'multi-blogs', array(), 'slug');
			$tca_data = array('userId' => $user['userId'], 'moduleId' => $newsroom['moduleId'],
							'itemId' => 0, 'itemType' => '', 'permId' => 0, 'asset' => $getAsset['asset'],
							'amount' => 0, 'op' => '>', 'stackOp' => 'OR', 'stackOrder' => 0, 'overrideable' => 1,
							'reference' => 'blog-role:'.$add);
							
			
			if($type == 'editor'){
				$newsroom_tca = $this->insert('token_access', $tca_data);
			}
			elseif($type == 'admin'){
				$newsroom_tca = $this->insert('token_access', $tca_data);
				
				$tca_data['moduleId'] = $categories['moduleId'];
				$categories_tca = $this->insert('token_access', $tca_data);
				
				$tca_data['moduleId'] = $multiblogs['moduleId'];
				$multiblogs_tca = $this->insert('token_access', $tca_data);														    															    
			}

		}
		else{
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
