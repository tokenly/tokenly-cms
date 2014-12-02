<?php
class Slick_App_Dashboard_Blog_Submissions_Model extends Slick_Core_Model
{

	public function getPostForm($postId = 0, $siteId, $andUseMeta = true)
	{
		$getPost = false;
		if($postId != 0){
			$getPost = $this->get('blog_posts', $postId);
		}
		
		$form = new Slick_UI_Form;
		$form->setFileEnc();
		
		if(!$getPost OR $getPost['formatType'] == 'markdown'){
			$excerpt = new Slick_UI_Markdown('excerpt', 'markdown');
			$excerpt->setLabel('Excerpt');
			
			$content = new Slick_UI_Markdown('content', 'markdown');
			$content->setLabel('Content');
		}
		else{
			$excerpt = new Slick_UI_Textarea('excerpt', 'mini-editor');
			$excerpt->setLabel('Excerpt');

			$content = new Slick_UI_Textarea('content', 'html-editor');
			$content->setLabel('Content');
		}		
		
		$title = new Slick_UI_Textbox('title');
		$title->addAttribute('required');
		$title->setLabel('Post Title');
		$form->add($title);
		
		$form->add($content);
		
		$autoGen = new Slick_UI_Checkbox('autogen-excerpt');
		$autoGen->setBool(1);
		$autoGen->setValue(1);
		$autoGen->setLabel('Create custom post excerpt');
		$form->add($autoGen);
		
		$form->add($excerpt);
		
		$url = new Slick_UI_Textbox('url');
		$url->setLabel('URL');
		$form->add($url);	
		
		$author = new Slick_UI_Select('userId');
		$getUsers = $this->getAll('users', array(), array('userId', 'username'));
		foreach($getUsers as $writer){
			$author->addOption($writer['userId'], $writer['username']);
		}
		$author->setLabel('Author');
		$form->add($author);
		
		$editor = new Slick_UI_Select('editedBy');
		$editor->addOption(0, '[nobody]');
		foreach($getUsers as $writer){
			$editor->addOption($writer['userId'], $writer['username']);
		}
		$editor->setLabel('Editor');
		$form->add($editor);		
		
		$status = new Slick_UI_Select('status');
		$status->addOption('draft', 'Draft');
		$status->addOption('ready', 'Ready for Publishing');
		$status->addOption('editing', 'Editing/Processing');
		$status->addOption('published', 'Published');
		$status->setLabel('Post Status');
		$form->add($status);
		
		$formatType = new Slick_UI_Select('formatType');
		$formatType->addOption('markdown', 'Markdown');
		$formatType->addOption('wysiwyg', 'WYSIWYG');
		$formatType->setLabel('Formatting Type (Save/Submit to change)');
		$form->add($formatType);

		/*$featured = new Slick_UI_Checkbox('featured');
		$featured->setLabel('Featured');
		$featured->setBool(1);
		$featured->setValue(1);
		$form->add($featured);*/


		$pubTime = new Slick_UI_Textbox('publishDate', 'datetimepicker');
		$pubTime->setLabel('Publish Date/Time');
		$form->add($pubTime);
		/*$pubTime = new Slick_UI_DateTime('publishDate');
		$pubTime->setLabel('Publish Date/Time');
		$pubTime->setMinYear(2013);
		$pubTime->setMaxYear(date('Y') + 5);
		$form->add($pubTime);*/
		
		$app = $this->get('apps', 'blog', array(), 'slug');
		$metaModel = new Slick_App_Meta_Model;
		$app['meta'] = $metaModel->appMeta($app['appId']);
		
		/*$image = new Slick_UI_File('image');
		$image->setLabel('Featured Image ('.$app['meta']['featuredWidth'].'x'.$app['meta']['featuredHeight'].')');
		$form->add($image);*/
        
		$coverImage = new Slick_UI_File('coverImage');
		$coverImage->setLabel('Cover Image ('.$app['meta']['coverWidth'].'x'.$app['meta']['coverHeight'].')');
		$form->add($coverImage);

		$categories = new Slick_UI_CheckboxList('categories');
		$categories->setLabel('Categories');
		$categories->setLabelDir('R');
		$catModel = new Slick_App_Dashboard_BlogCategory_Model;
		$getCats = $catModel->getCategoryFormList($siteId);
		foreach($getCats as $cat){
			$categories->addOption($cat['categoryId'], $cat['name']);
		}
		$form->add($categories);
		
		if($andUseMeta){
			$getMetaTypes = $this->getAll('blog_postMetaTypes', array('siteId' => $siteId, 'hidden' => 0, 'active' => 1), array(), 'rank', 'asc');
			foreach($getMetaTypes as $field){
				$slug = 'meta_'.$field['metaTypeId'];
				switch($field['type']){
					case 'textbox':
						$elem = new Slick_UI_Textbox($slug);
						break;
					case 'textarea':
						$elem = new Slick_UI_Textarea($slug);
						break;
					case 'select':
						$elem = new Slick_UI_Select($slug);
						$options = explode("\n", $field['options']);
						foreach($options as $option){
							$option = trim($option);
							$elem->addOption($option, $option);
						}
						break;
				}

				$elem->setLabel($field['label']);
				
				if($postId != 0){
					$getVal = $this->fetchSingle('SELECT * FROM blog_postMeta WHERE postId = :id AND metaTypeId = :typeId',
												array(':id' => $postId, ':typeId' => $field['metaTypeId']));
					if($getVal){
						$elem->setValue($getVal['value']);
					}
				}
				
				$form->add($elem);
			}
		}
		
	
		$notes = new Slick_UI_Textarea('notes');
		$notes->setLabel('Notes');
		$form->add($notes);
		
		$form->setSubmitText('Save & Submit');
		
		return $form;
	}
	
	public function updatePostCategories($postId, $cats)
	{
		$this->delete('blog_postCategories', $postId, 'postId');
		if(!is_array($cats)){
			$update = $this->insert('blog_postCategories', array('postId' => $postId, 'categoryId' => $cats));
			if(!$update){
				return false;
			}
		}
		else{
			foreach($cats as $cat){
				$update = $this->insert('blog_postCategories', array('postId' => $postId, 'categoryId' => $cat));
				if(!$update){
					return false;
				}
			}
		}
		
		return true;
	}
	


	public function updatePostImage($id, $type = 'image')
	{
		if(isset($_FILES[$type]['tmp_name']) AND trim($_FILES[$type]['tmp_name']) != false){
			$app = $this->get('apps', 'blog', array(), 'slug');
			$metaModel = new Slick_App_Meta_Model;
			$app['meta'] = $metaModel->appMeta($app['appId']);
            switch($type){
                case 'image':
                    $width = $app['meta']['featuredWidth'];
                    $height = $app['meta']['featuredHeight'];                
                    break;
                case 'coverImage':
                    $width = $app['meta']['coverWidth'];
                    $height = $app['meta']['coverHeight'];                    
                    break;
            }

		
			$name = $id.'-'.hash('sha256', $_FILES[$type]['name'].$id.$type).'.jpg';
			$path = SITE_PATH.'/files/blogs/'.$name;
			$resize = Slick_Util_Image::resizeImage($_FILES[$type]['tmp_name'], $path, $width, $height);
			if($resize){
				$update = $this->edit('blog_posts', $id, array($type => $name));
				if($update){
					return true;
				}
			}
			
		}
		return false;
		
	}

	public function addPost($data, $appData)
	{
		//check required fields
		$req = array('title' => true, 'url' => false, 'siteId' => true, 'status' => true,
					 'content' => false, 'userId' => true, 'publishDate' => true, 'excerpt' => false, 'featured' => false, 'formatType' => false,
					 'notes' => false);
		$useData = array();
		foreach($req as $key => $required){
			if(!isset($data[$key]) OR (isset($data[$key]) AND trim($data[$key]) == '')){
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
		
		//setup URL and prep sql data
		if(trim($useData['url']) == ''){
			$useData['url'] = $useData['title'];
		}
		$useData['url'] = genURL($useData['url']);
		$useData['url'] = $this->checkURLExists($useData['url']);
		$useData['postDate'] = timestamp();
		$useData['editTime'] = $useData['postDate'];
			
		
		//legacy status stuff, get rid of later
		$useData['published'] = 0;
		$useData['ready'] = 0;
		switch($useData['status']){
			case 'draft':
				//keep both at 0 ^
				break;
			case 'ready':
				$useData['ready'] = 1;
				break;
			case 'published':
				$useData['published'] = 1;
				break;
		}
		//unset($useData['status']);
		
		//temporary editor stuff
		if($appData['perms']['canChangeEditor'] AND isset($data['editedBy'])){
			$useData['editedBy'] = intval($data['editedBy']);
		}
		elseif(($appData['perms']['canSetEditStatus'] OR $appData['perms']['canChangeEditor'])
			AND ($useData['status'] == 'published' OR $useData['status'] == 'editing')){
			$useData['editedBy'] = $appData['user']['userId'];
		}		
		
		//perform insertion
		$add = $this->insert('blog_posts', $useData);
		if(!$add){
			throw new Exception('Error adding post');
		}
		
		//insert first version
		$versionData = array('type' => 'blog-post', 'itemId' => $add, 'userId' => $appData['user']['userId'],
							 'content' => json_encode(array('content' => $useData['content'], 'excerpt' => $useData['excerpt'])),
							 'versionDate' => $useData['postDate'], 'num' => 1, 'formatType' => $useData['formatType']);
		$addVersion = $this->insert('content_versions', $versionData);
		if($addVersion){
			$this->edit('blog_posts', $add, array('version' => $addVersion));
		}
		
		//setup categories
		if(isset($data['categories'])){
			$this->updatePostCategories($add, $data['categories']);
		}
		
		//setup images
		$this->updatePostImage($add);
		$this->updatePostImage($add, 'coverImage');
		
		
		//check if publishing right away
		if($useData['published'] == 1){
			$blogApp = $this->get('apps', 'blog', array(), 'slug');
			$postApp = $this->get('modules', 'blog-post', array(), 'slug');
			mention($useData['content'], '%username% has mentioned you in a 
					<a href="'.$appData['site']['url'].'/'.$blogApp['url'].'/'.$postApp['url'].'/'.$useData['url'].'">blog post.</a>',
					$useData['userId'], $add, 'blog-post-mention');
		}
		elseif($useData['ready'] == 1){
			$this->notifyEditorsOnReady($add, $appData);
		}
		
		//setup any custom meta fields used
		foreach($data as $key => $val){
			if(is_array($val) OR trim($val) == ''){
				continue;
			}
			$fieldId = intval(str_replace('meta_', '', $key));
			$getField = $this->get('blog_postMetaTypes', $fieldId);
			if(!$getField){
				continue;
			}

			$insertData = array('value' => strip_tags(trim($val)));
			$insertData['postId'] = $add;
			$insertData['metaTypeId'] = $fieldId;
			$update = $this->insert('blog_postMeta', $insertData);
		}

		return $add;
	}

	
	private function notifyEditorsOnReady($postId, $appData)
	{
		$getPost = $this->get('blog_posts', $postId);
		$blogApp = $this->get('apps', 'blog', array(), 'slug');
		$postMod = $this->get('modules', 'blog-post', array(), 'slug');
		if(!$getPost || !$blogApp){
			return false;
		}
		
		$getPerm = $this->getAll('app_perms', array('appId' => $blogApp['appId'], 'permKey' => 'canPublishPost'));
		if(!$getPerm || count($getPerm) == 0){
			return false;
		}
		$perm = $getPerm[0];
		
		$permGroups = $this->getAll('group_perms', array('permId' => $perm['permId']));
		
		$editors = array();
		foreach($permGroups as $group){
			$groupSites = $this->getAll('group_sites', array('groupId' => $group['groupId']));
			$groupFound = false;
			foreach($groupSites as $site){
				if($site['siteId'] == $appData['site']['siteId']){
					$groupFound = true;
					break;
				}
			}
			if(!$groupFound){
				continue;
			}
			$groupUsers = $this->getAll('group_users', array('groupId' => $group['groupId']));
			foreach($groupUsers as $user){
				if(!in_array($user['userId'], $editors)){
					$editors[] = $user['userId'];
				}
			}
		}
		
		if(count($editors) == 0){
			return false;
		}
		
		$postSite = $this->get('sites', $getPost['siteId']);
		$postUser = $this->get('users', $getPost['userId'], array('userId','username', 'email','slug'));
		
		foreach($editors as $editor){
			if($editor == $getPost['userId']){
				continue;
			}

			$notifyData = array();
			$notifyData['site'] = $postSite;
			$notifyData['user'] = $postUser;
			$notifyData['post'] = $getPost;
			$notifyData['editorId'] = $editor;
			$notify = Slick_App_Meta_Model::notifyUser($editor, 'emails.readyPublishNotice', $postId, 'blog-post-ready', true, $notifyData);
		}
		return true;
	}
	
		
	public function editPost($id, $data, $appData)
	{
		//get previous copy of post
		$getPost = $this->get('blog_posts', $id);
		
		//check required fields
		$req = array('title' => true, 'url' => false, 'siteId' => true, 
					'content' => false, 'publishDate' => true, 'excerpt' => false, 'featured' => false, 'userId' => false, 'status' => true, 'formatType' => false,
					'notes' => false);
		$useData = array();
		foreach($req as $key => $required){
			if(!isset($data[$key]) OR (isset($data[$key]) AND trim($data[$key]) == '')){
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
		
		//generate URL
		if(trim($useData['url']) == ''){
			$useData['url'] = $useData['title'];
		}
		$useData['url'] = genURL($useData['url']);
		$useData['url'] = $this->checkURLExists($useData['url'], $id);
		

		if(!$useData['userId']){
			unset($useData['userId']);
		}
		
		//check if formating is switching from markdown to HTML view
		if($getPost['formatType'] == 'markdown' AND $useData['formatType'] != 'markdown'){
			//convert from markdown to html editor
			$useData['content'] = markdown($useData['content']);
			$useData['excerpt'] = markdown($useData['excerpt']);
		}

		//legacy status stuff, get rid of this later
		$useData['published'] = 0;
		$useData['ready'] = 0;
		switch($useData['status']){
			case 'draft':
				//keep both at 0 ^
				break;
			case 'ready':
				$useData['ready'] = 1;
				break;
			case 'published':
				$useData['published'] = 1;
				break;
		}
		//unset($useData['status']);
		
		$useData['editTime'] = timestamp();
		
		if($appData['perms']['canChangeEditor'] AND isset($data['editedBy'])){
			$useData['editedBy'] = intval($data['editedBy']);
		}
		if($getPost['editedBy'] == 0
			AND ($appData['perms']['canSetEditStatus'] OR $appData['perms']['canChangeEditor'])
			AND (($getPost['status'] != 'published' OR $getPost['published'] == 0) AND $getPost['status'] != 'editing')
			AND ($useData['status'] == 'published' OR $useData['status'] == 'editing')){
			$useData['editedBy'] = $appData['user']['userId'];
		}

		//check for new version
		if($getPost['content'] != $useData['content'] OR $getPost['excerpt'] != $useData['excerpt']){
			$versionContent = array('content' => $useData['content'], 'excerpt' => $useData['excerpt']);
			$versionNum = $this->getNextVersionNum($id);
			$changeNum = 0;
			$getPrevVersion = $this->get('content_versions', $getPost['version']);
			if($getPrevVersion){
				$prevContent = json_decode($getPrevVersion['content'], true);
				$compare = $this->comparePostChanges($versionContent, $prevContent);
				if($compare){
					$changeNum = $compare['num'];
				}
			}
			$newVersion = $this->insert('content_versions', array('type' => 'blog-post', 'itemId' => $id, 'userId' => $appData['user']['userId'],
															'content' => json_encode($versionContent), 'formatType' => $useData['formatType'],
															'versionDate' => $useData['editTime'], 'num' => $versionNum, 'changes' => $changeNum));
			if($newVersion){
				$useData['version'] = $newVersion;
			}
		}

		//apply main edit
		$edit = $this->edit('blog_posts', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing post');
		}
		
		//update categories
		if(isset($data['categories'])){
			$this->updatePostCategories($id, $data['categories']);
		}
		
		//update images
		$this->updatePostImage($id);
		$this->updatePostImage($id, 'coverImage');
		
		
		//check if published. if so, run some extra tasks
		if($useData['published'] == 1){
			$blogApp = $this->get('apps', 'blog', array(), 'slug');
			$postApp = $this->get('modules', 'blog-post', array(), 'slug');
			mention($useData['content'], '%username% has mentioned you in a 
					<a href="'.$appData['site']['url'].'/'.$blogApp['url'].'/'.$postApp['url'].'/'.$useData['url'].'">blog post.</a>',
					$appData['post']['userId'], $id, 'blog-post-mention');
		}
		elseif($getPost['ready'] == 0 AND $useData['ready'] == 1){
			$this->notifyEditorsOnReady($id, $appData);
		}
		
		//update any custom meta fields that might be present
		foreach($data as $key => $val){
			$fieldId = intval(str_replace('meta_', '', $key));
			$getField = $this->get('blog_postMetaTypes', $fieldId);
			if(!$getField){
				continue;
			}
			$update = $this->updatePostMeta($id, $getField['slug'], $val);
		}

		return true;
	}
	
	public function updatePostMeta($postId, $key, $value = '')
	{
		$getField = $this->get('blog_postMetaTypes', $key, array(), 'slug');
		if(!$getField){
			return false;
		}
		$fieldId = $getField['metaTypeId'];
		$getVal = $this->fetchSingle('SELECT * FROM blog_postMeta WHERE postId = :postId AND metaTypeId = :typeId',
									array(':postId' => $postId, ':typeId' => $fieldId));
		
		$insertData = array('value' => strip_tags(trim($value)));
		if($getVal){
			$update = $this->edit('blog_postMeta', $getVal['metaId'], $insertData);
		}
		else{
			//insert new one
			$insertData['postId'] = $postId;
			$insertData['metaTypeId'] = $fieldId;
			$update = $this->insert('blog_postMeta', $insertData);
		}
		
		return $update;
	}
	
	public function getPostMetaVal($postId, $key)
	{
		$model = new Slick_App_Blog_Post_Model;
		$getMeta = $model->getPostMeta($postId, false, true);
		foreach($getMeta as $mKey => $mVal){
			if($mKey == $key AND trim($mVal) != ''){
				return $mVal;
			}
		}
		return false;
	}
	
	public function getPostFormCategories($postId)
	{
		$get = $this->getAll('blog_postCategories', array('postId' => $postId));
		$output = array();
		foreach($get as $row){
			$output[] = $row['categoryId'];
		}
		
		return $output;
	}


	
	public function checkURLExists($url, $ignore = 0, $count = 0)
	{
		$useurl = $url;
		if($count > 0){
			$useurl = $url.'-'.$count;
		}
		$get = $this->get('blog_posts', $useurl, array('postId', 'url'), 'url');
		if($get AND $get['postId'] != $ignore){
			//url exists already, search for next level of url
			$count++;
			return $this->checkURLExists($url, $ignore, $count);
		}
		
		if($count > 0){
			$url = $url.'-'.$count;
		}

		return $url;
	}
	
	public function getTrashItems($userId = 0)
	{
		$site = currentSite();
		$where = array('trash' => 1, 'siteId' => $site['siteId']);
		if($userId != 0){
			$where['userId'] = $userId;
		}
		$get = $this->getAll('blog_posts', $where, array(), 'postId');	
		return $get;
	}
	
	public function countTrashItems($userId = 0)
	{
		$items = $this->getTrashItems($userId);
		if(!is_array($items)){
			return false;
		}
		return count($items);
	}
	
	public function getVersionNum($postId)
	{
		$get = $this->fetchSingle('SELECT num
								   FROM content_versions
								   WHERE type = "blog-post" AND itemId = :id
								   ORDER BY num DESC
								   LIMIT 1', array(':id' => $postId));
		if(!$get){
			return 1;
		}
		return $get['num'];
	}
	
	public function getNextVersionNum($postId)
	{

		return $this->getVersionNum($postId) + 1;
	}
	
	public function getPostVersion($postId, $version = 0)
	{
		if($version == 0){
			$version = $this->getVersionNum($postId);
		}
		$get = $this->fetchSingle('SELECT * FROM content_versions
								   WHERE type = "blog-post" AND itemId = :id
								   AND num = :num',
								   array(':id' => $postId, ':num' => $version));
		if($get){
			$get['content'] = json_decode($get['content'], true);
		}
		return $get;
	}
	
	public function getVersions($postId)
	{
		$profModel = new Slick_App_Profile_User_Model;
		$get = $this->getAll('content_versions', array('type' => 'blog-post', 'itemId' => $postId), array(), 'num', 'asc');
		foreach($get as &$row){
			$row['content'] = json_decode($row['content'], true);
			$row['user'] = $profModel->getUserProfile($row['userId']);
		}
		return $get;
	}
	
	public function comparePostChanges($new, $old)
	{
		$fields = array('content', 'excerpt');
		$lines = array();
		$oldLines = array();
		
		$numChanges = 0;
		foreach($fields as $field){
			if(isset($new[$field])){
				$newLines[$field] = explode("\n", $new[$field]);
			}
			if(isset($old[$field])){
				$oldLines[$field] = explode("\n", $old[$field]);
			}
			
			$oldCount = count($oldLines[$field]);
			$newCount = count($newLines[$field]);
			$maxLines = $oldCount;
			if($newCount > $oldCount){
				$maxLines = $newCount;
			}
			
			$lines[$field] = array();
			for($i = 0; $i < $maxLines; $i++){
				$getOld = '';
				$getNew = '';
				if(isset($oldLines[$field][$i])){
					$getOld = $oldLines[$field][$i];
				}
				if(isset($newLines[$field][$i])){
					$getNew = $newLines[$field][$i];
				}
				if(trim($getOld) != trim($getNew)){
					$lines[$field][$i] = array('old' => $getOld, 'new' => $getNew);
					$numChanges++;
				}
			}			
		}
		
		return array('lines' => $lines, 'num' => $numChanges);
	}
	
	public function comparePostVersions($postId, $v1, $v2)
	{
		$getVersions = $this->getVersions($postId);
		$getV1 = false;
		$getV2 = false;
		foreach($getVersions as $version){
			if($version['num'] == $v1){
				$getV1 = $version;
			}
			if($version['num'] == $v2){
				$getV2 = $version;
			}
		}
		
		if(!$getV1 OR !$getV2){
			return false;
		}
		
		$compare = $this->comparePostChanges($getV1['content'], $getV2['content']);
		$compare['v1_user'] = $getV1['user'];
		$compare['v2_user'] = $getV2['user'];
		
		return $compare;
		
	}
}

?>
