<?php
namespace App\Blog;
use Core, UI, Util, API, App\Tokenly, App\Profile;
class Submissions_Model extends Core\Model
{
	public static $approvedCategories = false;
	public static $editorComments = false;
	
	function __construct()
	{
		parent::__construct();
		if(!self::$approvedCategories){
			self::$approvedCategories = $this->fetchAll('SELECT pc.categoryId, pc.postId
									 FROM blog_postCategories pc
									 LEFT JOIN blog_categories c ON c.categoryId = pc.categoryId
									 LEFT JOIN blogs b ON b.blogId = c.blogId
									 LEFT JOIN blog_posts p ON p.postId = pc.postId
									 WHERE pc.approved = 1 AND b.active = 1 AND p.status = "published"
									 GROUP BY pc.categoryId, pc.postId');
		}
		if(!self::$editorComments){
			self::$editorComments = $this->fetchAll('SELECT commentId, postId, userId FROM blog_comments WHERE editorial  = 1 ORDER BY commentId DESC');
		}
	}

	protected function getPostForm($postId = 0, $siteId, $andUseMeta = true, $user = array())
	{
		$getPost = false;
		if($postId != 0){
			$getPost = $this->get('blog_posts', $postId);
		}
		
		$form = new UI\Form;
		$form->setFileEnc();
		
		if(!$getPost OR $getPost['formatType'] == 'markdown'){
			$excerpt = new UI\Markdown('excerpt', 'markdown');
			$excerpt->setLabel('Excerpt');
			
			$content = new UI\Markdown('content', 'markdown');
			$content->setLabel('Content');
		}
		else{
			$excerpt = new UI\Textarea('excerpt', 'mini-editor');
			$excerpt->setLabel('Excerpt');

			$content = new UI\Textarea('content', 'html-editor');
			$content->setLabel('Content');
		}		
		
		$title = new UI\Textbox('title');
		$title->addAttribute('required');
		$title->setLabel('Post Title');
		$form->add($title);
		
		$form->add($content);
		
		$autoGen = new UI\Checkbox('autogen-excerpt');
		$autoGen->setBool(1);
		$autoGen->setValue(1);
		$autoGen->setLabel('Create custom post excerpt');
		$form->add($autoGen);
		
		$form->add($excerpt);
		
		$url = new UI\Textbox('url');
		$url->setLabel('URL');
		$form->add($url);	
		
		$author = new UI\Select('userId');
		$getUsers = $this->getAll('users', array(), array('userId', 'username'));
		foreach($getUsers as $writer){
			$author->addOption($writer['userId'], $writer['username']);
		}
		$author->setLabel('Author');
		$form->add($author);	
		
		$status = new UI\Select('status');
		$status->addOption('draft', 'Draft');
		$status->addOption('ready', 'Ready for Review');
		$status->addOption('published', 'Finished');
		$status->setLabel('Post Status');
		$form->add($status);
		
		$formatType = new UI\Select('formatType');
		$formatType->addOption('markdown', 'Markdown');
		$formatType->addOption('wysiwyg', 'WYSIWYG');
		$formatType->setLabel('Formatting Type (Save/Submit to change)');
		$form->add($formatType);

		$featured = new UI\Checkbox('featured');
		$featured->setLabel('Featured');
		$featured->setBool(1);
		$featured->setValue(1);
		$form->add($featured);

		$pubTime = new UI\Textbox('publishDate', 'datetimepicker');
		$pubTime->setLabel('Publish Date/Time');
		$form->add($pubTime);
		/*$pubTime = new DateTime('publishDate');
		$pubTime->setLabel('Publish Date/Time');
		$pubTime->setMinYear(2013);
		$pubTime->setMaxYear(date('Y') + 5);
		$form->add($pubTime);*/
		
		$app = $this->get('apps', 'blog', array(), 'slug');
		$metaModel = new \App\Meta_Model;
		$app['meta'] = $metaModel->appMeta($app['appId']);
		
		/*$image = new UI\File('image');
		$image->setLabel('Featured Image ('.$app['meta']['featuredWidth'].'x'.$app['meta']['featuredHeight'].')');
		$form->add($image);*/
        
		$coverImage = new UI\File('coverImage');
		$coverImage->setLabel('Cover Image ('.$app['meta']['coverWidth'].'x'.$app['meta']['coverHeight'].')');
		$form->add($coverImage);

		$categories = new UI\CascadingCheckboxList('categories');
		$categories->setLabel('Requested Blog Categories *');
		$catModel = new Categories_Model;
		$multiblog = new Multiblog_Model;
		$accessRoles = array('independent-writer', 'writer', 'editor', 'admin');
		$getCats = $catModel->getCategories($siteId, 0, true);
		$blogCatList = array();
		$getCats = $this->container->checkCategoryListAccess($getCats, $user);
		foreach($getCats as $cat){
			$getBlog = $this->get('blogs', $cat['blogId']);
			if(!isset($blogCatList[$cat['blogId']])){
				$blogCatList[$cat['blogId']] = array('value' => 0, 'label' => $getBlog['name'], 'children' => array());
			}
			$blogCatList[$cat['blogId']]['children'][] = $cat;
		}

		$categories->setOptions($blogCatList);
		$form->add($categories);
		
		if($andUseMeta){
			$getMetaTypes = $this->getAll('blog_postMetaTypes', array('siteId' => $siteId, 'hidden' => 0, 'active' => 1), array(), 'rank', 'asc');
			foreach($getMetaTypes as $field){
				$slug = 'meta_'.$field['metaTypeId'];
				switch($field['type']){
					case 'textbox':
						$elem = new UI\Textbox($slug);
						break;
					case 'textarea':
						$elem = new UI\Textarea($slug);
						break;
					case 'select':
						$elem = new UI\Select($slug);
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
		
	
		$notes = new UI\Textarea('notes');
		$notes->setLabel('Notes');
		$form->add($notes);
		
		$form->setSubmitText('Save');
		
		return $form;
	}
	
	protected function checkCategoryListAccess($cats, $user)
	{
		$catModel = new Categories_Model;
		$multiblog = new Multiblog_Model;		
		$accessRoles = array('independent-writer', 'writer', 'editor', 'admin');
		foreach($cats as $k => &$cat){
			$getBlog = $this->get('blogs', $cat['blogId']);
			if($getBlog['active'] == 0){
				unset($cats[$k]);
				continue;
			}
			$getRoles = $multiblog->getBlogUserRoles($cat['blogId']);
			if($cat['public'] == 0){
				$has_access = false;
				foreach($getRoles as $role){
					if($role['userId'] == $user['userId'] AND in_array($role['type'], $accessRoles)){
						$has_access = true;
					}
				}
				if($user['perms']['canManageAllBlogs']){
					$has_access = true;
				}
				if(!$has_access){
					unset($cats[$k]);
					continue;
				}
			}
			if(isset($cat['children'])){
				$cat['children'] = $this->container->checkCategoryListAccess($cat['children'], $user);
			}
		}
		return $cats;
	}
	
	protected function checkCategoryAccess($categoryId, $userId)
	{
		$cat = $this->get('blog_categories', $categoryId);
		if(!$cat){
			return false;
		}
		$getBlog = $this->get('blogs', $cat['blogId']);
		if($getBlog['active'] == 0){
			return false;
		}
		$multiblog = new Multiblog_Model;
		$getRoles = $multiblog->getBlogUserRoles($getBlog['blogId'], true);
		if($cat['public'] == 0){
			$found = false;
			foreach($getRoles as $role){
				if($role['userId'] == $userId){
					$found = true;
				}
			}
			if(!$found){
				return false;
			}
		}
		$cat['blog'] = $getBlog;
		$cat['roles'] = $getRoles;
		return $cat;
	}
	
	protected function updatePostCategories($postId, $cats, $user)
	{
		\Core\Model::$cacheMode = false;
		if(!is_array($cats)){
			$cats = array($cats);
		}
		
		$multiblog = new Multiblog_Model;
		$accessRoles = array('independent-writer', 'writer', 'editor', 'admin');
		$postCats = $this->fetchAll('SELECT c.*, pc.approved, pc.postCatId
									   FROM blog_postCategories pc
									   LEFT JOIN blog_categories c ON c.categoryId = pc.categoryId
									   WHERE pc.postId = :postId',
									  array(':postId' => $postId));
		
		foreach($postCats as $k => $cat){
			if(!in_array($cat['categoryId'], $cats)){
				$catAccess = $this->container->checkCategoryAccess($cat['categoryId'], $user['userId']);
				if($catAccess OR $user['perms']['canManageAllBlogs']){ //only remove if they also have access to the category (to prevent accidental removal)
					$this->delete('blog_postCategories', $cat['postCatId']);
					unset($postCats[$k]);
				}
			}
		}
	
		
		//clear out any duplicates
		$used = array();
		foreach($postCats as $k => $cat){
			if(!in_array($cat['categoryId'], $used)){
				$used[] = $cat['categoryId'];
			}
			else{
				$this->delete('blog_postCategories', $cat['postCatId']);
				unset($postCats[$k]);
			}
		}
		
		
		foreach($cats as $cat){
			if(in_array($cat, $used)){
				continue;
			}
			$catAccess = $this->container->checkCategoryAccess($cat, $user['userId']);
			if(!$catAccess AND !$user['perms']['canManageAllBlogs']){
				continue;
			}
					
			$existing = false;
			foreach($postCats as $postCat){
				if($catAccess AND $postCat['categoryId'] == $catAccess['categoryId']){
					$existing = true;
					break;
				}
			}
			

			if(!$existing){
				$approved = 0;
				if($user['perms']['canManageAllBlogs'] OR $user['userId'] == $catAccess['blog']['userId']){
					$approved = 1;
				}
				else{
					foreach($catAccess['roles'] as $role){
						if($role['userId'] == $user['userId']){
							switch($role['type']){
								case 'admin':
								case 'editor';
								case 'independent-writer':
									$approved = 1;
									break;
								default:
									break;
							}
						}
					}
				}
				$insert_vals = array('postId' => $postId, 'categoryId' => $cat, 'approved' => $approved);
				$update = $this->insert('blog_postCategories', $insert_vals);		
			}
		}
		\Core\Model::$cacheMode = true;		
		return true;
	}
	

	protected function updatePostImage($id, $type = 'image')
	{
		if(isset($_FILES[$type]['tmp_name']) AND trim($_FILES[$type]['tmp_name']) != false){
			$app = $this->get('apps', 'blog', array(), 'slug');
			$metaModel = new \App\Meta_Model;
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
			$resize = Util\Image::resizeImage($_FILES[$type]['tmp_name'], $path, $width, $height);
			if($resize){
				$update = $this->edit('blog_posts', $id, array($type => $name));
				if($update){
					return true;
				}
			}
			
		}
		return false;
		
	}

	protected function addPost($data, $appData)
	{
		//check required fields
		$req = array('title' => true, 'url' => false, 'siteId' => true, 'status' => true,
					 'content' => false, 'userId' => true, 'publishDate' => true, 'excerpt' => false, 'featured' => false, 'formatType' => false,
					 'notes' => false);
		$useData = array();
		foreach($req as $key => $required){
			if(!isset($data[$key]) OR (isset($data[$key]) AND trim($data[$key]) == '')){
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
		
		//setup URL and prep sql data
		if(trim($useData['url']) == ''){
			$useData['url'] = $useData['title'];
		}
		$useData['url'] = genURL($useData['url']);
		$useData['url'] = $this->container->checkURLExists($useData['url']);
		$useData['postDate'] = timestamp();
		$useData['editTime'] = $useData['postDate'];
		$useData['editedBy'] = $appData['user']['userId'];
			
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

		//perform insertion
		$add = $this->insert('blog_posts', $useData);
		if(!$add){
			throw new \Exception('Error adding post');
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
			$appData['user']['perms'] = $appData['perms'];
			$this->container->updatePostCategories($add, $data['categories'], $appData['user']);
		}
		
		//setup images
		$this->container->updatePostImage($add);
		$this->container->updatePostImage($add, 'coverImage');
		
		
		//check if publishing right away
		if($useData['published'] == 1){
			$blogApp = $this->get('apps', 'blog', array(), 'slug');
			$postApp = $this->get('modules', 'blog-post', array(), 'slug');
			mention($useData['content'], '%username% has mentioned you in a 
					<a href="'.$appData['site']['url'].'/'.$blogApp['url'].'/'.$postApp['url'].'/'.$useData['url'].'">blog post.</a>',
					$useData['userId'], $add, 'blog-post-mention');
		}
		elseif($useData['status'] == 'ready'){
			$useData['postId'] = $add;
			$this->container->notifyEditorsOnReady($useData, $appData);
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

	
	protected function notifyEditorsOnReady($post, $appData)
	{
		$multiblog = new Multiblog_Model;
		$getBlogs = $this->fetchAll('SELECT b.*
									 FROM blog_postCategories pc
									 LEFT JOIN blog_categories c ON c.categoryId = pc.categoryId
									 LEFT JOIN blogs b ON b.blogId = c.blogId
									 WHERE pc.postId = :postId AND b.active = 1
									 GROUP BY b.blogId', array(':postId' => $post['postId']));
									 
		$notifyData = array();
		$notifyData['post'] = $post;
		$notifyData['user'] = $appData['user'];

		foreach($getBlogs as $blog){
			$getRoles = $multiblog->getBlogUserRoles($blog['blogId'], true);
			foreach($getRoles as $role){
				switch($role['type']){
					case 'admin':
					case 'owner':
					case 'editor':
						\App\Meta_Model::notifyUser($role['userId'], 'emails.blog.ready_review', $post['postId'], 'blog-post-ready', true, $notifyData);
						break;
				}
			}
		}
		
		//get platform chief editors
		$meta = new \App\Meta_Model;
		$global_editors = $meta->getUsersWithPermission('blog', 'receiveAllReview');
		if($global_editors){
			foreach($global_editors as $ge){
				if($ge != $post['userId']){
					\App\Meta_Model::notifyUser($ge, 'emails.blog.ready_review', $post['postId'], 'blog-post-ready', true, $notifyData);
				}
			}
		}
		
		return true; 
	}
	
		
	protected function editPost($id, $data, $appData)
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
		
		//generate URL
		if(trim($useData['url']) == ''){
			$useData['url'] = $useData['title'];
		}
		$useData['url'] = genURL($useData['url']);
		$useData['url'] = $this->container->checkURLExists($useData['url'], $id);
		

		if(!$useData['userId']){
			unset($useData['userId']);
		}
		
		//check if formating is switching from markdown to HTML view
		if($getPost['formatType'] == 'markdown' AND $useData['formatType'] != 'markdown'){
			//convert from markdown to html editor
			$useData['content'] = markdown($useData['content']);
			$useData['excerpt'] = markdown($useData['excerpt']);
		}
		if($getPost['formatType'] == 'wysiwyg' AND $useData['formatType'] == 'markdown'){
			$useData['content'] = strip_tags($useData['content']);
			$useData['excerpt'] = strip_tags($useData['excerpt']);
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
		
		if($useData['status'] != $getPost['status']){
			$notifyData = array();
			$notifyData['culprit'] = $appData['user'];
			$notifyData['post'] = $getPost;
			$notifyData['new_status'] = $useData['status'];
			$this->container->notifyContributors($getPost['postId'], 'status_change', $notifyData, $appData['user']['userId']);
		}
		
		if($useData['publishDate'] != $getPost['publishDate']){
			$notifyData = array();
			$notifyData['culprit'] = $appData['user'];
			$notifyData['post'] = $getPost;
			$notifyData['new_date'] = $useData['publishDate'];
			$this->container->notifyContributors($getPost['postId'], 'publish_date_change', $notifyData, $appData['user']['userId']);
		}
		
		
		$useData['editTime'] = timestamp();
		$useData['editedBy'] = $appData['user']['userId'];
		
		//check for new version
		if($getPost['content'] != $useData['content'] OR $getPost['excerpt'] != $useData['excerpt']){
			$versionContent = array('content' => $useData['content'], 'excerpt' => $useData['excerpt']);
			$versionNum = $this->container->getNextVersionNum($id);
			$changeNum = 0;
			$getPrevVersion = $this->get('content_versions', $getPost['version']);
			if($getPrevVersion){
				$prevContent = json_decode($getPrevVersion['content'], true);
				$compare = $this->container->comparePostChanges($versionContent, $prevContent);
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
			throw new \Exception('Error editing post');
		}
		
		//update categories
		if(isset($data['categories'])){
			$appData['user']['perms'] = $appData['perms'];
			$this->container->updatePostCategories($id, $data['categories'], $appData['user']);
		}
		
		//update images
		//$this->container->updatePostImage($id); //disabled normal image
		$this->container->updatePostImage($id, 'coverImage');
		
		if($getPost['status'] != 'ready' AND $useData['status'] == 'ready'){
			$this->container->notifyEditorsOnReady($getPost, $appData);
		}
			
		foreach($data as $key => $val){
			$fieldId = intval(str_replace('meta_', '', $key));
			$getField = $this->get('blog_postMetaTypes', $fieldId);
			if(!$getField){
				continue;
			}
			$update = $this->container->updatePostMeta($id, $getField['slug'], $val);
		}
		return true;
	}
	
	protected function updatePostMeta($postId, $key, $value = '')
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
	
	protected function getPostMetaVal($postId, $key)
	{
		$model = new Post_Model;
		$getMeta = $model->getPostMeta($postId, false, true);
		foreach($getMeta as $mKey => $mVal){
			if($mKey == $key AND trim($mVal) != ''){
				return $mVal;
			}
		}
		return false;
	}
	
	protected function getPostFormCategories($postId, $returnFull = false)
	{
		$get = $this->getAll('blog_postCategories', array('postId' => $postId));
		if($returnFull){
			return $get;
		}
		$output = array();
		foreach($get as $row){
			$output[] = $row['categoryId'];
		}
		
		return $output;
	}


	
	protected function checkURLExists($url, $ignore = 0, $count = 0)
	{
		$useurl = $url;
		if($count > 0){
			$useurl = $url.'-'.$count;
		}
		$get = $this->get('blog_posts', $useurl, array('postId', 'url'), 'url');
		if($get AND $get['postId'] != $ignore){
			//url exists already, search for next level of url
			$count++;
			return $this->container->checkURLExists($url, $ignore, $count);
		}
		
		if($count > 0){
			$url = $url.'-'.$count;
		}

		return $url;
	}
	
	protected function getTrashItems($userId = 0)
	{
		$site = currentSite();
		$where = array('trash' => 1, 'siteId' => $site['siteId']);
		if($userId != 0){
			$where['userId'] = $userId;
		}
		$get = $this->getAll('blog_posts', $where, array(), 'postId');	
		return $get;
	}
	
	protected function countTrashItems($userId = 0)
	{
		$items = $this->container->getTrashItems($userId);
		if(!is_array($items)){
			return false;
		}
		return count($items);
	}
	
	protected function getVersionNum($postId)
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
	
	protected function getNextVersionNum($postId)
	{

		return $this->container->getVersionNum($postId) + 1;
	}
	
	protected function getPostVersion($postId, $version = 0)
	{
		if($version == 0){
			$version = $this->container->getVersionNum($postId);
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
	
	protected function getVersions($postId)
	{
		$profModel = new Profile\User_Model;
		$get = $this->getAll('content_versions', array('type' => 'blog-post', 'itemId' => $postId), array(), 'num', 'asc');
		foreach($get as &$row){
			$row['content'] = json_decode($row['content'], true);
			$row['user'] = $profModel->getUserProfile($row['userId']);
		}
		return $get;
	}
	
	protected function comparePostChanges($new, $old)
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
	
	protected function comparePostVersions($postId, $v1, $v2)
	{
		$getVersions = $this->container->getVersions($postId);
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
		
		$compare = $this->container->comparePostChanges($getV1['content'], $getV2['content']);
		$compare['v1_user'] = $getV1['user'];
		$compare['v2_user'] = $getV2['user'];
		
		return $compare;
		
	}
	
	protected function getCommentListHash($postId)
	{
		$get = $this->fetchAll('SELECT commentId as id, editTime as edit FROM blog_comments WHERE postId = :id AND buried = 0 AND editorial = 1',
								array(':id' => $postId), 0, true);
		$encode = json_encode($get);
		return hash('sha256', $encode);
	}
	
	protected function complete_blog_contributor_request($invite)
	{
		$getPost = $this->get('blog_posts', $invite['itemId']);
		if(!$getPost){
			throw new \Exception('Invalid blog post');
		}
		
		$getRow = $this->get('blog_contributors', $invite['inviteId'], array(), 'inviteId');
		if(!$getRow){
			throw new \Exception('Invalid blog contributor request');
		}
		
		$contribs = $this->container->getPostContributors($getPost['postId']);
		$contribs[] = array('userId' => $getPost['userId']); //add author to contrib list
		foreach($contribs as $contrib){
			\App\Meta_Model::notifyUser($contrib['userId'], 'emails.invites.'.$invite['type'].'_complete', $invite['inviteId'], 'user-invite-complete', false, $invite);
		}
		
		//send acceptance notification to user
		\App\Meta_Model::notifyUser($invite['sendUser'], 'emails.invites.'.$invite['type'].'_accept', $invite['inviteId'], 'user-invite-accept', false, $invite);
		
		$site = currentSite();
		$dashApp = get_app('dashboard');
		$blogApp = get_app('blog');
		$submitModule = $this->get('modules', 'blog-submissions', array(), 'slug');
		$redirect = $site['url'].'/'.$dashApp['url'].'/'.$blogApp['url'].'/'.$submitModule['url'].'/edit/'.$getPost['postId'];
		return $redirect;
	}
	
	protected function getPostContributors($postId, $andAccepted = true)
	{
		$accept = '';
		if($andAccepted){
			$accept = ' AND i.accepted = 1';
		}
		$get = $this->fetchAll('SELECT u.userId, u.username, u.slug, c.role, c.share, c.inviteId, c.contributorId, i.accepted
								FROM blog_contributors c
								LEFT JOIN user_invites i ON c.inviteId = i.inviteId
								LEFT JOIN users u ON i.userId = u.userId
								WHERE c.postId = :postId '.$accept.'
								ORDER BY c.share DESC', array(':postId' => $postId));
								
		
		return $get;
		
	}
	
	protected function checkUserContributor($postId, $userId)
	{
		$contribs = $this->container->getPostContributors($postId);
		foreach($contribs as $contrib){
			if($contrib['userId'] == $userId){
				return true;
			}
		}
		return false;
	}
	
	protected function getUserContributedPosts($data)
	{
		$get = $this->fetchAll('SELECT p.postId, p.userId, p.url, p.title, p.status, p.views, p.commentCount, p.commentCheck,
									   p.postDate, p.publishDate, p.excerpt, p.published, p.status, p.ready,
									   p.siteId, p.version, p.editTime, p.editedBy, p.coverImage, p.formatType, c.role, p.content
								FROM blog_contributors c
								LEFT JOIN user_invites i ON i.inviteId = c.inviteId
								LEFT JOIN blog_posts p ON p.postId = c.postId
								WHERE i.userId = :userId AND p.trash = 0 AND p.siteId = :siteId AND i.accepted = 1
								GROUP BY c.postId', array(':siteId' => $data['site']['siteId'], ':userId' => $data['user']['userId']));
		
		return $get;
	}
	
	protected function getUserPostsWithContributed($data)
	{
		$contribs = $this->container->getUserContributedPosts($data);
		$posts = $this->getAll('blog_posts', array('userId' => $data['user']['userId'], 'trash' => 0));
		if(!$posts){
			$posts = array();
		}
		foreach($posts as $k => $post){
			$posts[$k]['time_num'] = strtotime($post['postDate']);
		}
		if(!$contribs){
			$contribs = array();
		}
		foreach($contribs as $k => $post){
			$contribs[$k]['time_num'] = strtotime($post['postDate']);
		}
		$output = array_merge($contribs, $posts);
		aasort($output, 'time_num');
		$output = array_reverse($output);
		return $output;
	}
	
	protected function checkPostCategoryApproved($postId, $categoryId)
	{
		$getPostCat = $this->fetchSingle('SELECT approved from blog_postCategories
												 WHERE categoryId = :categoryId AND postId = :postId',
												 array(':categoryId' => $categoryId, ':postId' => $postId));
												 
		if($getPostCat AND $getPostCat['approved'] == 1){
			return true;
		}
		return false;
	}
	
	protected function parseApprovedCategoryOptions($catOpts, $postId, $categoryId)
	{
		$postApproved = $this->container->checkPostCategoryApproved($postId, $categoryId);	
		foreach($catOpts as $ck => $cv){
			if($categoryId == $cv['value']){
				if($postApproved){
					$catOpts[$ck]['label'] = $cv['label'].' <i class="fa fa-thumbs-o-up text-success" title="Approved"></i>';
				}
				else{
					$catOpts[$ck]['label'] = $cv['label'].' <span class="text-default">[pending]</span>';
				}
			}
			if(isset($cv['children'])){
				$catOpts[$ck]['children'] = $this->container->parseApprovedCategoryOptions($cv['children'], $postId, $categoryId);
			}			
		}
		return $catOpts;
	}
	protected static function checkPostApproved($postId)
	{
		$check = extract_row(self::$approvedCategories, array('postId' => $postId));
		if($check AND count($check) > 0){
			return 1;
		}
		return 0;
	}
	
	protected function notifyContributors($postId, $notification, $data, $skipUser = 0)
	{
		$getAuthor = $this->get('blog_posts', $postId, array('userId'));
		\Core\Model::$cacheMode = false;
		$getContribs = $this->container->getPostContributors($postId);
		\Core\Model::$cacheMode = true;
		$getContribs[] = $getAuthor;
		foreach($getContribs as $contrib){
			if($contrib['userId'] == $skipUser){
				continue;
			}
			\App\Meta_Model::notifyUser($contrib['userId'], 'emails.blog.'.$notification, $postId, 'blog-'.$notification, true, $data);
		}
		
	}
	
	protected function checkPostBlogRole($postId, $userId)
	{
		$multiblogs = new Multiblog_Model;
		$getCatBlogs = $this->fetchAll('SELECT c.blogId
									FROM blog_postCategories pc 
									LEFT JOIN blog_categories c ON c.categoryId = pc.categoryId
									WHERE pc.postId = :postId
									GROUP BY c.blogId', array(':postId' => $postId));
		$allowed_roles = array('admin', 'editor', 'owner');
		foreach($getCatBlogs as $blog){
			$getRoles = $multiblogs->getBlogUserRoles($blog['blogId'], true);
			foreach($getRoles as $role){
				if($role['userId'] == $userId AND in_array($role['type'], $allowed_roles)){
					return true;
				}
			}
		}
		return false;
	}
	
	protected function getContentWordCount($content, $type = 'markdown')
	{
		switch($type){
			case 'markdown':
				$content = markdown($content);
				break;
		}
		$content = strip_tags($content);
		return str_word_count($content);
	}	
}
