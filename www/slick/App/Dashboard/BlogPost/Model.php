<?php
class Slick_App_Dashboard_BlogPost_Model extends Slick_Core_Model
{

	public function getPostForm($postId = 0, $theme, $siteId)
	{
		$getPost = false;
		if($postId != 0){
			$getPost = $this->get('blog_posts', $postId);
		}
		
		$form = new Slick_UI_Form;
		$form->setFileEnc();
		
		if(!$getPost OR $getPost['formatType'] == 'markdown'){
			$inkUrlExcerpt = $this->getInkpadUrl($postId, 'inkpad-excerpt-url');
			$excerpt = new Slick_UI_Inkpad('excerpt');
			$excerpt->setInkpad($inkUrlExcerpt);
			$excerpt->setLabel('Excerpt');
			
			$inkUrl = $this->getInkpadUrl($postId);
			$content = new Slick_UI_Inkpad('content');
			$content->setInkpad($inkUrl);
			$content->setLabel('Content');
			
			if($getPost){
				$getExcerptPad = $excerpt->getValue();
				$getContentPad = $content->getValue();
				
				if(md5($getExcerptPad) != md5($getPost['excerpt'])){
					$excerpt->setLabel('Excerpt <span class="unsaved">[unsaved]</span>');
				}
				else{
					$excerpt->setLabel('Excerpt <span class="saved">[saved]</span>');
				}
				
				if(md5($getContentPad) != md5($getPost['content'])){
					$content->setLabel('Content <span class="unsaved">[unsaved]</span>');
				}
				else{
					$content->setLabel('Content <span class="saved">[saved]</span>');
				}				
			}
					
			//$form->add($excerpt);
			//$form->add($content);	
		}
		else{
			$excerpt = new Slick_UI_Textarea('excerpt', 'mini-editor');
			$excerpt->setLabel('Excerpt');
			//$form->add($excerpt);
			

			$content = new Slick_UI_Textarea('content', 'html-editor');
			$content->setLabel('Content');
			//$form->add($content);	
		}		
		
		$title = new Slick_UI_Textbox('title');
		$title->addAttribute('required');
		$title->setLabel('Post Title');
		$form->add($title);
		
		$form->add($content);
		
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

		$featured = new Slick_UI_Checkbox('featured');
		$featured->setLabel('Featured');
		$featured->setBool(1);
		$featured->setValue(1);
		$form->add($featured);

		$pubTime = new Slick_UI_DateTime('publishDate');
		$pubTime->setLabel('Publish Date/Time');
		$pubTime->setMinYear(2013);
		$pubTime->setMaxYear(date('Y') + 5);
		$form->add($pubTime);
		
		$app = $this->get('apps', 'blog', array(), 'slug');
		$metaModel = new Slick_App_Meta_Model;
		$app['meta'] = $metaModel->appMeta($app['appId']);
		
		$image = new Slick_UI_File('image');
		$image->setLabel('Featured Image ('.$app['meta']['featuredWidth'].'x'.$app['meta']['featuredHeight'].')');
		$form->add($image);
        
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
		
		$form->add($excerpt);
		
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
		$req = array('title' => true, 'url' => false, 'siteId' => true, 'status' => true,
					 'content' => false, 'userId' => true, 'publishDate' => true, 'excerpt' => false, 'featured' => false, 'formatType' => false,
					 'notes' => false);
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
		
		if(trim($useData['url']) == ''){
			$useData['url'] = $useData['title'];
		}
		$useData['url'] = genURL($useData['url']);
		$useData['url'] = $this->checkURLExists($useData['url']);
		$useData['postDate'] = timestamp();
		
		$getExcerpt = false;
		if(isset($_POST['excerpt_inkpad'])){
			$excerptInkpad = new Slick_UI_Inkpad('excerpt');
			$excerptInkpad->setInkpad($_POST['excerpt_inkpad']);
			$getExcerpt = $excerptInkpad->getValue();
			if($getExcerpt){
				$useData['excerpt'] = $getExcerpt;
			}
		}
		
		$getContent =false;
		if(isset($_POST['content_inkpad'])){
			$contentInkpad = new Slick_UI_Inkpad('content');
			$contentInkpad->setInkpad($_POST['content_inkpad']);
			$getContent = $contentInkpad->getValue();
			if($getContent){
				$useData['content'] = $getContent;
			}
		}		
		
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
		
		if($appData['perms']['canChangeEditor'] AND isset($data['editedBy'])){
			$useData['editedBy'] = intval($data['editedBy']);
		}
		elseif(($appData['perms']['canSetEditStatus'] OR $appData['perms']['canChangeEditor'])
			AND ($useData['status'] == 'published' OR $useData['status'] == 'editing')){
			$useData['editedBy'] = $appData['user']['userId'];
		}		
		
		$add = $this->insert('blog_posts', $useData);
		if(!$add){
			throw new Exception('Error adding post');
		}
		
		if(isset($_POST['excerpt_inkpad']) AND $getExcerpt){
			$this->updatePostMeta($add, 'inkpad-excerpt-url', $_POST['excerpt_inkpad']);
		}		
		if(isset($_POST['content_inkpad']) AND $getContent){
			$this->updatePostMeta($add, 'inkpad-url', $_POST['content_inkpad']);
		}

		if(isset($data['categories'])){
			$this->updatePostCategories($add, $data['categories']);
		}
		
		$this->updatePostImage($add);
		$this->updatePostImage($add, 'coverImage');
		
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
		$getPost = $this->get('blog_posts', $id);
		$req = array('title' => true, 'url' => false, 'siteId' => true, 
					'content' => false, 'publishDate' => true, 'excerpt' => false, 'featured' => false, 'userId' => true, 'status' => true, 'formatType' => false,
					'notes' => false);
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
		
		//generate URL
		if(trim($useData['url']) == ''){
			$useData['url'] = $useData['title'];
		}
		$useData['url'] = genURL($useData['url']);
		$useData['url'] = $this->checkURLExists($useData['url'], $id);
		
		if(!$useData['userId']){
			unset($useData['userId']);
		}
		
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

		
		$edit = $this->edit('blog_posts', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing post');
		}

		if(isset($data['categories'])){
			$this->updatePostCategories($id, $data['categories']);
		}
		
		$this->updatePostImage($id);
		$this->updatePostImage($id, 'coverImage');
		
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


	public function getInkpadUrl($postId, $urlKey = 'inkpad-url')
	{
		if($postId != 0){
			$getUrl = $this->getPostMetaVal($postId, $urlKey);
			if($getUrl){
				return $getUrl;
			}
		}

		//generate new inkpad
		$url = Slick_UI_Inkpad::getNewPad();

		if($postId != 0){
			$this->updatePostMeta($postId, $urlKey, $url);
		}
		
		return $url;
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
	
}

?>
