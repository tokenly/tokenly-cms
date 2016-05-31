<?php
namespace App\Blog;
/*
 * @module-type = dashboard
 * @menu-label = Submissions
 * 
 * */
use App\Tokenly, App\Account, API, Util;
class Submissions_Controller extends \App\ModControl
{
    
    function __construct()
    {
        parent::__construct();
        $this->model = new Submissions_Model;
        $this->user = Account\Auth_Model::userInfo();
		$this->tca = new Tokenly\TCA_Model;
		$this->inventory = new Tokenly\Inventory_Model;
		$this->meta = new \App\Meta_Model;
		$this->blogModel = new Multiblog_Model;
		$this->postModule = $this->model->get('modules', 'blog-post', array(), 'slug');
		$this->catModule = $this->model->get('modules', 'blog-category', array(), 'slug');        
		$this->blogApp = $this->model->get('apps', 'blog', array(), 'slug');
		$this->blogSettings = $this->meta->appMeta($this->blogApp['appId']);
        $this->postModel = new Post_Model;
        $this->invite = new Account\Invite_Model;
        $this->credits = new Account\Credits_Model;
    }
    
    function __install($moduleId)
    {
		$install = parent::__install($moduleId);
		if(!$install){
			return false;
		}
		
		$meta = $this->meta;
		$blogApp = $meta->get('apps', 'blog', array(), 'slug');
		$meta->updateAppMeta($blogApp['appId'], 'submission-fee', 1000, 'Article Submission Fee', 1);
		$meta->updateAppMeta($blogApp['appId'], 'submission-fee-token', 'LTBCOIN', 'Submission Fee Token', 1);
		
		$meta->addAppPerm($blogApp['appId'], 'canBypassSubmitFee');
		
		return $install;
	}
    
    protected function init()
    {
		$output = parent::init();
		$tca = $this->tca;
		$postModule = $tca->get('modules', 'blog-post', array(), 'slug');
		$this->data['perms'] = \App\Meta_Model::getUserAppPerms($this->data['user']['userId'], 'blog');
		$this->data['perms'] = $tca->checkPerms($this->data['user'], $this->data['perms'], $postModule['moduleId'], 0, '');

        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->container->showPosts();
					break;
				case 'add':
					$output = $this->container->addPost();
					break;
				case 'edit':
					$output = $this->container->editPost();
					break;
				case 'delete':
					$output = $this->container->deletePost();
					break;
				case 'preview':
					$output = $this->container->previewPost($output);
					break;
				case 'trash':
					if(isset($this->args[3])){
						$output = $this->container->trashPost();
					}
					else{
						$output = $this->container->showPosts(1);
					}
					break;
				case 'restore':
					$output = $this->container->trashPost(true);
					break;
				case 'clear-trash':
					$output = $this->container->clearTrash();
					break;
				case 'compare':
					$output = $this->container->comparePostVersions();
					break;
				default:
					$output = $this->container->showPosts();
					break;
			}
		}
		else{
			$output = $this->container->showPosts();
		}
		$output['postModule'] = $this->postModule;
		$output['blogApp'] = $this->blogApp;
		if(!isset($output['template'])){
			$output['template'] = 'admin';
		}
        $output['perms'] = $this->data['perms'];
        		
        return $output;
    }
    
    /**
    * Shows a list of posts that the current user has submitted
    *
    * @return Array
    */
    protected function showPosts($trash = 0)
    {
		$output = array('view' => 'list');
		$getPosts = $this->model->getAll('blog_posts', array('siteId' => $this->data['site']['siteId'],
															 'userId' => $this->data['user']['userId'],
															 'trash' => $trash), array(), 'postId');
		if($trash == 0){													 								 
			$getContribPosts = $this->model->getUserContributedPosts($this->data);
			$getPosts = array_merge($getPosts, $getContribPosts);
		}
															 
		$viewedComments = $this->meta->getUserMeta($this->data['user']['userId'], 'viewed-editorial-comments');
		if($viewedComments){
			$viewedComments = explode(',', $viewedComments);
		}															 
															
		$output['totalPosts'] = 0;
		$output['totalPublished'] = 0;
		$output['totalViews'] = 0;
		$output['totalComments'] = 0;
		$output['totalContributed'] = 0;
		$time = time();
		$comment_updates = array();
		$disqus = new API\Disqus;
		foreach($getPosts as $key => $row){
			$row['published'] = $this->model->checkPostApproved($row['postId']);
			$getPosts[$key]['published'] = $row['published'];
			$getPosts[$key]['author'] = $this->model->get('users', $row['userId'], array('userId', 'username', 'slug'));
			$postPerms = $this->tca->checkPerms($this->data['user'], $this->data['perms'], $this->postModule['moduleId'], $row['postId'], 'blog-post');
			$getPosts[$key]['perms'] = $postPerms;
			if($row['userId'] == $this->data['user']['userId']){
				$output['totalPosts']++;
				if($row['published'] == 1){
					$output['totalPublished']++;
				}
			}
			$output['totalViews']+=$row['views'];	

			$comDiff = $time - strtotime($row['commentCheck']);
			$commentThread = false;
			if($comDiff > 600){
				$comment_updates[] = $row['postId'];
			}
			$output['totalComments'] += $row['commentCount'];
			
			//editorial comments
			$post['new_comments'] = false;
			$getLastComment = extract_row(Submissions_Model::$editorComments, array('postId' => $row['postId']));					
			if($getLastComment){
				$getLastComment = $getLastComment[0];
				if($getLastComment['userId'] != $this->data['user']['userId']){
					$post['new_comments'] = true;
					if($viewedComments){
						foreach($viewedComments as $viewed){
							$expViewed = explode(':', $viewed);
							if($expViewed[0] == $row['postId']){
								if($expViewed[1] == $getLastComment['commentId']){
									$post['new_comments'] = false;
								}
							}
						}
					}
				}
			}
			$getPosts[$key]['new_comments'] = $post['new_comments'];		
			if($row['userId'] != $this->data['user']['userId']){
				$output['totalContributed']++;
			}
		}
		
		$output['postList'] = $getPosts;
		
		//trigger comment count updating
		if(count($comment_updates) > 0){
			exec('nohup php '.SITE_BASE.'/scripts/updateBlogPostCommentCounts.php \''.join(',',$comment_updates).'\' > /dev/null &');
		}
		
		$output['submission_fee'] = $this->blogSettings['submission-fee'];
		$output['num_credits'] = floor($this->credits->getCreditBalance() / $output['submission_fee']);
		
		$output['trashCount'] = $this->model->countTrashItems($this->user['userId']);
		$output['trashMode'] = $trash;
		
		
		return $output;
	}
	
	
	protected function addPost()
	{
		$output = array('view' => 'form');
		if(!$this->data['perms']['canWritePost']){
			$output['view'] = '403';
			return $output;
		}
		
		$output['num_credits'] = $this->credits->getCreditBalance();
		if(!$this->data['perms']['canBypassSubmitFee'] AND $output['num_credits'] < floatval($this->blogSettings['submission-fee'])){
			Util\Session::flash('blog-message', 'You do not have enough system credits to create a new post', 'error');
			redirect($this->site.$this->moduleUrl);
		}
		$this->data['user']['perms'] = $this->data['perms'];
		$output['form'] = $this->model->getPostForm(0, $this->data['site']['siteId'], true, $this->data['user']);
		$output['formType'] = 'Submit';
		$output['form']->remove('featured');
		if(!$this->data['perms']['canPublishPost']){
			$output['form']->field('status')->removeOption('published');
			//$output['form']->remove('featured');
		}

		if(isset($this->data['perms']['canUseMagicWords']) AND !$this->data['perms']['canUseMagicWords']){
			$getField = $this->model->get('blog_postMetaTypes', 'magic-word', array(), 'slug');
			if($getField){
				$output['form']->remove('meta_'.$getField['metaTypeId']);
			}
		}
	
		if(!$this->data['perms']['canChangeAuthor']){
			$output['form']->remove('userId');
		}
		else{
			$output['form']->setValues(array('userId' => $this->data['user']['userId']));
		}

		if(posted()){
			$data = $output['form']->grabData();
			if(isset($data['publishDate'])){
				$data['publishDate'] = date('Y-m-d H:i:s', strtotime($data['publishDate']));
			}			
			$data['siteId'] = $this->data['site']['siteId'];
			if(!$this->data['perms']['canChangeAuthor']){
				$data['userId'] = $this->user['userId'];
			}
			if(!$this->data['perms']['canPublishPost']){
				if(isset($data['published'])){
					unset($data['published']);
				}
				if(isset($data['featured'])){
					unset($data['featured']);
				}
				if(isset($data['status']) AND $data['status'] == 'published'){
					$data['status'] = 'draft';
				}
			}
		
			if($data['autogen-excerpt'] == 0){
				$data['excerpt'] = shortenMsg(strip_tags($data['content']), 500);
			}			
			try{
				$add = $this->model->addPost($data, $this->data);
			}
			catch(\Exception $e){
				Util\Session::flash('blog-message', $e->getMessage(), 'error');
				$add = false;
			}
			
			if($add){
				if(!$this->data['perms']['canBypassSubmitFee']){
					//deduct from their current credits
                    $this->credits->debit($this->blogSettings['submission-fee'], 'blog-post:'.$add, 'Submitted blog article');
				}
				redirect($this->site.$this->moduleUrl);
			}
			else{
				redirect($this->site.$this->moduleUrl.'/add');
			}
			return;
		}
		
		$output['form']->field('publishDate')->setValue(date('Y/m/d H:i'));
		
		return $output;
		
	}
	
	protected function accessPost()
	{
		if(!isset($this->args[3])){
			throw new \Exception('404');
		}		
		
		$getPost = $this->model->get('blog_posts', $this->args[3]);
		if(!$getPost OR $getPost['trash'] == 1){
			throw new \Exception('404');
		}
		$getPost['published'] = $this->model->checkPostApproved($getPost['postId']);


		$tca = $this->tca;
		$postModule = $tca->get('modules', 'blog-post', array(), 'slug');
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');	
		$this->data['perms'] = $tca->checkPerms($this->data['user'], $this->data['perms'], $postModule['moduleId'], $getPost['postId'], 'blog-post');
		$foundRole = true;
		$getPost['user_blog_role'] = false;
		$getPost['pending_contrib'] = false;
		$getPost['active_contrib'] = false;
		
		if(!$this->data['perms']['canManageAllBlogs']){
			$postTCA = $tca->checkItemAccess($this->data['user'], $postModule['moduleId'], $getPost['postId'], 'blog-post');
			if(!$postTCA){
				throw new \Exception('403');
			}
			
			if($getPost['userId'] != $this->data['user']['userId']){
				$foundBlogRole = $this->model->checkPostBlogRole($getPost['postId'], $this->data['user']['userId']);
				$foundRole = $foundBlogRole;

				$getContribs = $this->model->getPostContributors($getPost['postId'], false);
				foreach($getContribs as $contrib){
					if($contrib['userId'] == $this->data['user']['userId']){
						$foundRole = true;
						if($contrib['accepted'] == 0){
							$getPost['pending_contrib'] = true;
						}
						else{
							$getPost['active_contrib'] = true;
						}
					}
				}
				$getPost['contributors'] = $getContribs;
				
				if($foundBlogRole){
					$getPost['user_blog_role'] = true;
				}
			}
			
			if(($getPost['userId'] != $this->data['user']['userId'] AND (!$foundRole OR !$this->data['perms']['canEditOtherPost']))
				OR
			   ($getPost['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canEditSelfPost'])
			   ){
				throw new \Exception('403');
			}
			
			if($getPost['status'] == 'published' AND !$this->data['perms']['canEditAfterPublished']){
				throw new \Exception('403');
			}
		}
		
		$getPost['categories'] = $this->model->getPostFormCategories($getPost['postId']);
		$getPost['author'] = $this->model->get('users', $getPost['userId']);
		$getPost['word_count'] = $this->model->getContentWordCount($getPost['content'], $getPost['formatType']);
		
		return $getPost;
	}
	
	protected function editPost()
	{
		try{
			$getPost = $this->container->accessPost();
		}
		catch(\Exception $e){
			return array('view' => $e->getMessage());
		}
		
		$output = array('view' => 'form');
		$this->data['user']['perms'] = $this->data['perms'];
		$output['form'] = $this->model->getPostForm($getPost['postId'], $this->data['site']['siteId'], true, $this->data['user']);
		$output['formType'] = 'Edit';
		$output['post'] = $getPost;
		$this->data['post'] = $getPost;
		$output['unlock_post'] = true;
		$contributor = $this->model->checkUserContributor($getPost['postId'], $this->data['user']['userId']);
		$output['contributor'] = $contributor;
		$output['contributor_list'] = $this->model->getPostContributors($getPost['postId'], false);

		if($getPost['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canManageAllBlogs']){
			if((!$contributor OR $getPost['status'] == 'published')){
				if(!$contributor OR !$getPost['user_blog_role']){
					$output['form']->field('title')->addAttribute('disabled');
					$output['form']->field('url')->addAttribute('disabled');
					$output['form']->field('formatType')->addAttribute('disabled');
					$output['form']->field('content')->addAttribute('disabled');
					$output['form']->field('excerpt')->addAttribute('disabled');
					$output['form']->field('autogen-excerpt')->addAttribute('disabled');
					$output['form']->field('notes')->addAttribute('disabled');
					$output['form']->field('coverImage')->addAttribute('disabled');
					$output['form']->field('status')->addAttribute('disabled');
					$output['form']->field('publishDate')->addAttribute('disabled');	
					$output['form']->field('categories')->addAttribute('disabled');		
					$output['form']->remove('featured');
					foreach($output['form']->fields as $fkey => $field){
						if(strpos($fkey, 'meta_') === 0){
							$output['form']->field($fkey)->addAttribute('disabled');
						}
					}							
					$output['unlock_post'] = false;
				}
			}
			//still disable some stuff for them
			if(!$getPost['user_blog_role']){
				$output['form']->field('status')->addAttribute('disabled');
				$output['form']->field('publishDate')->addAttribute('disabled');	
				$output['form']->field('coverImage')->addAttribute('disabled');
				$output['form']->field('categories')->addAttribute('disabled');
				$output['form']->remove('featured');
			}		
		}
		
		if(isset($this->data['perms']['canUseMagicWords'])){
			if(!$this->data['perms']['canUseMagicWords']){
				$getField = $this->model->get('blog_postMetaTypes', 'magic-word', array(), 'slug');
				if($getField){
					$output['form']->remove('meta_'.$getField['metaTypeId']);
				}
			}
			else{
				$getWords = $this->model->getAll('pop_words', array('itemId' => $getPost['postId'],
																	'moduleId' => $this->postModule['moduleId']),
																array('submitId'));
				$output['magic_word_count'] = count($getWords);
			}
		}
		
		if(!$this->data['perms']['canPublishPost']){
			if($getPost['status'] == 'published'){
				$output['form']->field('status')->addAttribute('disabled');
			}
			else{
				$output['form']->field('status')->removeOption('published');
			}
			//$output['form']->remove('featured');
		}

		if(!$this->data['perms']['canChangeAuthor']){
			$output['form']->remove('userId');
		}
			
		//request/invite a contributor
		if(posted()){
			if(isset($_POST['request-contrib']) AND !$contributor){
				return $this->container->requestContributor($output);
			}
			elseif(isset($_POST['invite-contrib']) AND ($this->data['user']['userId'] == $getPost['userId'] OR $this->data['perms']['canManageAllBlogs'])){
				return $this->container->requestContributor($output, true);
			}
			elseif(isset($_POST['update-contribs']) AND ($this->data['user']['userId'] == $getPost['userId'] OR $this->data['perms']['canManageAllBlogs'])){
				return $this->container->updateContributors($output);
			}
		}
		//contributor controls
		if(isset($this->args[4]) AND $this->args[4] == 'contributors'){
			if(isset($this->args[5])){
				switch($this->args[5]){
					case 'delete':
						return $this->container->deleteContributor($output);
				}
			}	
		}	
		
		
		if(posted() AND !isset($_POST['no_edit']) AND $output['unlock_post']){
			$data = $output['form']->grabData();
			if(isset($data['publishDate'])){
				$data['publishDate'] = date('Y-m-d H:i:s', strtotime($data['publishDate']));
			}
			if($getPost['userId'] != $this->data['user']['userId'] AND !$getPost['user_blog_role'] AND !$this->data['perms']['canManageAllBlogs']){
				$data['publishDate'] = $getPost['publishDate'];
				$data['status'] = $getPost['status'];
			}

			$data['siteId'] = $this->data['site']['siteId'];
			if(!$this->data['perms']['canChangeAuthor']){
				$data['userId'] = false;
			}

			if(!$this->data['perms']['canPublishPost']){
				if($getPost['published'] == 0){
					if(isset($data['status']) AND $data['status'] == 'published'){
						$data['status'] = 'draft';
					}
				}
				else{
					$data['status'] = 'published';
				}
			}

			if(!isset($data['status'])){ 
				$data['status'] = $getPost['status'];
			}	

			if($data['autogen-excerpt'] == 0){
				$data['excerpt'] = shortenMsg(strip_tags($data['content']), 500);
			}
			$data['contributor'] = $contributor;
			
			if($getPost['userId'] != $this->data['user']['userId'] AND !$getPost['user_blog_role'] AND !$this->data['perms']['canManageAllBlogs']){
				unset($data['categories']);
			}
			
			try{
				$edit = $this->model->editPost($this->args[3], $data, $this->data);
			}
			catch(\Exception $e){
				Util\Session::flash('blog-message', $e->getMessage(), 'error');			
				$edit = false;
			}
			
			if($edit){
				Util\Session::flash('blog-message', 'Post edited successfully!', 'success');
			}
			redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url'].'/edit/'.$getPost['postId']);
		}	
		
		//get version list and #
		$output['versions'] = $this->model->getVersions($getPost['postId']);
		$output['current_version'] = $this->model->getVersionNum($getPost['postId']);
		$output['old_version'] = false;
		
		if(isset($this->args[4])){
			foreach($output['versions'] as $version){
				if($version['num'] == $this->args[4]){
					$oldVersion = $this->model->getPostVersion($getPost['postId'], $version['num']);
					if($oldVersion AND $oldVersion['versionId'] != $getPost['version']){
						if(isset($this->args[5]) AND $this->args[5] == 'delete'){
							if(($getPost['userId'] == $this->data['user']['userId'] AND $this->data['perms']['canDeleteSelfPostVersion'])
								OR
							 ($getPost['userId'] != $this->data['user']['userId'] AND $this->data['perms']['canDeleteOtherPostVersion'])){
								$killVersion = $this->model->delete('content_versions', $oldVersion['versionId']);
								Util\Session::flash('blog-message', 'Version #'.$oldVersion['num'].' removed', 'success');
								redirect($this->site.'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/edit/'.$getPost['postId']);
							}
						}
						$output['post']['content'] = $oldVersion['content']['content'];
						$output['post']['excerpt'] = $oldVersion['content']['excerpt'];
						$output['post']['word_count'] = $this->model->getContentWordCount($oldVersion['content']['content'], $oldVersion['formatType']);
						$output['old_version'] = $oldVersion;
						$getPost['content'] = $output['post']['content'];
						$getPost['excerpt'] = $output['post']['excerpt'];
						$output['post']['formatType'] = $oldVersion['formatType'];
						$getPost['formatType'] = $oldVersion['formatType'];
						if($oldVersion['formatType'] == 'wysiwyg'){
							$output['form']->field('content')->setID('html-editor');
							$output['form']->field('excerpt')->setID('mini-editor');							
						}
					}
					break;
				}
			}
		}		
		
		//private editorial discussion
		$output['comment_form'] = $this->postModel->getCommentForm();
		$output['private_comments'] = $this->postModel->getPostComments($getPost['postId'], 1);
		if(count($output['private_comments']) > 0){
			$meta = $this->meta;
			$viewedComments = $meta->getUserMeta($this->data['user']['userId'], 'viewed-editorial-comments');
			$getLastComment = $meta->fetchSingle('SELECT commentId FROM blog_comments
												  WHERE postId = :postId AND userId != :userId AND editorial = 1
												  ORDER BY commentId DESC',
												array(':postId' => $getPost['postId'], ':userId' => $this->data['user']['userId']));			
			if($viewedComments){
				$viewedComments = explode(',', $viewedComments);
			}
			else{
				$viewedComments = array();
			}
		
			$updateViewed = true;
			foreach($viewedComments as $k => $viewed){
				$expViewed = explode(':', $viewed);
				if($expViewed[0] == $getPost['postId']){
					if($expViewed[1] == $getLastComment['commentId']){
						$updateViewed = false;
					}
					else{
						unset($viewedComments[$k]);
						$updateViewed = true;
						break;
					}
				}
			}
			if($updateViewed){
				$viewedComments[] = $getPost['postId'].':'.$getLastComment['commentId'];
				$meta->updateUserMeta($this->data['user']['userId'], 'viewed-editorial-comments', join(',',$viewedComments));
			}
		}

		$output['comment_list_hash'] = $this->model->getCommentListHash($getPost['postId']);
		if(isset($this->args[4]) AND $this->args[4] == 'comments'){
			if(isset($this->args[5])){
				switch($this->args[5]){
					case 'post':
						$json = $this->container->postPrivateComment();
						break;
					case 'edit':
						$json = $this->container->editPrivateComment();
						break;
					case 'delete':
						$json = $this->container->deletePrivateComment();
						break;
					case 'check':
						$json = $this->container->checkCommentList();
						break;
					case 'get':
					default:
						$json = $this->container->getPrivateComments();
						break;
				}
				
				ob_end_clean();
				header('Content-Type: application/json');
				echo json_encode($json);
				die();
			}
		}
		
		
		//setup form values
		$catOpts = $output['form']->field('categories')->getOptions();
		foreach($getPost['categories'] as $catId){
			$catOpts = $this->model->parseApprovedCategoryOptions($catOpts, $getPost['postId'], $catId);
		}
		$output['form']->field('categories')->setOptions($catOpts);
		$output['form']->setValues($getPost);
		$output['form']->field('publishDate')->setValue(date('Y/m/d H:i', strtotime($getPost['publishDate'])));
		
		return $output;
	}
	
	
	protected function postPrivateComment()
	{
		$output = array('error' => null);
		
		if(!posted()){
			http_response_code(400);
			$output['error'] = 'Invalid request method';
			return $output;
		}
		
		if(!$this->data['perms']['canPostComment']){
			http_response_code(403);
			$output['error'] = 'You do not have permission for this';
			return $output;
		}
		
		if(!isset($_POST['message'])){
			http_response_code(400);
			$output['error'] = 'Message required';
			return $output;
		}
		
		$data = array();
		$data['postId'] = $this->data['post']['postId'];
		$data['userId'] = $this->data['user']['userId'];
		$data['message'] = strip_tags($_POST['message']);
		
		try{
			$postComment = $this->postModel->postComment($data, $this->data, 1);
		}
		catch(\Exception $e){
			http_response_code(400);
			$output['error'] = $e->getMessage();
			return $output;
		}
		
		$output['result'] = 'success';
		$postComment['formatDate'] = formatDate($postComment['commentDate']);
		$postComment['html_content'] = markdown($postComment['message']);
		$postComment['encoded'] = base64_encode($postComment['message']);
		$profModel = new \App\Profile\User_Model;
		$authProf = $profModel->getUserProfile($postComment['userId']);
		$postComment['author'] = array('username' => $authProf['username'], 'slug' => $authProf['slug'], 'avatar' => $authProf['avatar']);
		$output['comment'] = $postComment;
		$output['new_hash'] = $this->model->getCommentListHash($this->data['post']['postId']);
		
		return $output;
	}
	
	protected function deletePrivateComment()
	{
		$output = array('error' => null);
		
		if(!posted()){
			http_response_code(400);
			$output['error'] = 'Invalid request method';
			return $output;
		}	
		
		if(!isset($_POST['commentId'])){
			http_response_code(400);
			$output['error'] = 'Comment ID required';
			return $output;
		}
		
		$comment = $this->model->get('blog_comments', $_POST['commentId']);
		if(!$comment){
			http_response_code(400);
			$output['error'] = 'Invalid comment ID';
			return $output;
		}
		
		if(($comment['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canDeleteSelfComment'])
			OR ($comment['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canDeleteOtherComment'])){
			http_response_code(403);
			$output['error'] = 'You do not have permission for this';
			return $output;
		}			
		
		$delete = $this->model->delete('blog_comments', $comment['commentId']);
		$output['result'] = 'success';
	
		return $output;
	}
	
	protected function editPrivateComment()
	{
		$output = array('error' => null);
		
		if(!posted()){
			http_response_code(400);
			$output['error'] = 'Invalid request method';
			return $output;
		}	
		
		if(!isset($_POST['commentId'])){
			http_response_code(400);
			$output['error'] = 'Comment ID required';
			return $output;
		}
		
		if(!isset($_POST['message'])){
			http_response_code(400);
			$output['error'] = 'Message';
			return $output;
		}
		
		$comment = $this->model->get('blog_comments', $_POST['commentId']);
		if(!$comment){
			http_response_code(400);
			$output['error'] = 'Invalid comment ID';
			return $output;
		}
		
		if(($comment['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canEditSelfComment'])
			OR ($comment['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canEditOtherComment'])){
			http_response_code(403);
			$output['error'] = 'You do not have permission for this';
			return $output;
		}
		
		$data = array();
		$data['message'] = strip_tags($_POST['message']);
		$data['editTime'] = timestamp();
		
		$edit = $this->model->edit('blog_comments', $comment['commentId'], $data);

		$output['result'] = 'success';
		$comment['formatDate'] = formatDate($comment['commentDate']);
		$comment['formatEditDate'] = formatDate($data['editTime']);
		$comment['html_content'] = markdown($data['message']);
		$comment['encoded'] = base64_encode($data['message']);
		$profModel = new \App\Profile\User_Model;
		$authProf = $profModel->getUserProfile($comment['userId']);
		$comment['author'] = array('username' => $authProf['username'], 'slug' => $authProf['slug'], 'avatar' => $authProf['avatar']);
		$output['comment'] = $comment;
		$output['new_hash'] = $this->model->getCommentListHash($this->data['post']['postId']);
		
		return $output;
	}
	
	protected function checkCommentList()
	{
		$hash = $this->model->getCommentListHash($this->data['post']['postId']);
		return array('hash' => $hash);
	}
	
	protected function getPrivateComments()
	{
		$comments = $this->postModel->getPostComments($this->data['post']['postId'], 1);
		foreach($comments as &$comment){
			$comment['author'] = array('username' => $comment['author']['username'],
									   'slug' => $comment['author']['slug'],
									   'avatar' => $comment['author']['avatar']);
			$comment['html_content'] = markdown($comment['message']);
			$comment['encoded'] = base64_encode($comment['message']);
			$comment['formatDate'] = formatDate($comment['commentDate']);
			$comment['formatEditDate'] = formatDate($comment['editTime']);
			unset($comment['buried']);
			unset($comment['editorial']);
			unset($comment['postId']);
			
		}
		$output['comments'] = $comments;
		$output['new_hash'] = $this->model->getCommentListHash($this->data['post']['postId']);
		
		return $output;
	}

	
	protected function deletePost()
	{
		if(!isset($this->args[3])){
			redirect($this->site.$this->moduleUrl);
		}
		
		$getPost = $this->model->get('blog_posts', $this->args[3]);
		if(!$getPost){
			redirect($this->site.$this->moduleUrl);
		}
		
		if(($getPost['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canDeleteSelfPost'])
		OR ($getPost['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canDeleteOtherPost'])){
			return array('view' => '403');
		}

		if($getPost['published'] == 1 AND !$this->data['perms']['canPublishPost']){
			return array('view' => '403');
		}
			
		$delete = $this->model->delete('blog_posts', $this->args[3]);
		Util\Session::flash('blog-message', $getPost['title'].' deleted successfully', 'success');
		
		redirect($this->site.$this->moduleUrl.'/trash');
	}
	
	protected function previewPost($output)
	{
		if(!isset($this->args[3])){
			redirect($this->site.$this->moduleUrl);
		}
		
		$model = new Post_Model;
		$getPost = $model->getPost($this->args[3], $this->data['site']['siteId']);
		if(!$getPost){
			redirect($this->site.$this->moduleUrl);
		}
		
		if(isset($this->args[4])){
			$oldVersion = $this->model->getPostVersion($getPost['postId'], $this->args[4]);
			if($oldVersion){
				$getPost['content'] = $oldVersion['content']['content'];
				$getPost['excerpt'] = $oldVersion['content']['excerpt'];
			}
		}	
		
		$getCategories = $this->model->getAll('blog_postCategories', array('postId' => $getPost['postId']));
		
		
		$cats = array();
		foreach($getCategories as $cat){
			$getCat = $this->model->get('blog_categories', $cat['categoryId']);
			$cats[] = $getCat;
		}
		$getPost['categories'] = $cats;
		
		$output['template'] = 'blog';
		$output['view'] = '';
		$output['force-view'] = 'Blog/Post/post';
		$output['post'] = $getPost;
		$output['disableComments'] = true;
		$output['user'] = Account\Auth_Model::userInfo();
		$output['title'] = $getPost['title'];
		$output['commentError'] = '';
		$output['comments'] = array();
		
		$getBlog = $this->postModel->getPostFirstBlog($getPost['postId']);
		if($getBlog){
			if($getBlog['themeId'] != 0){
				$getTheme = $this->model->get('themes', $getBlog['themeId']);
				if($getTheme){
					$output['theme'] = $getTheme['location'];
				}
			}
		}
		$getBlog['settings'] = $this->blogModel->getSingleBlogSettings($getBlog);
		$output['blog'] = $getBlog;
		
		
		return $output;
	}
	
	
	protected function trashPost($restore = false)
	{
		if(!isset($this->args[3])){
			redirect($this->site.$this->moduleUrl);
		}
		
		$getPost = $this->model->get('blog_posts', $this->args[3]);
		if(!$getPost){
			redirect($this->site.$this->moduleUrl);
		}
		
		if($getPost['userId'] != $this->data['user']['userId']){
			return array('view' => '403');
		}

		if($getPost['published'] == 1 AND !$this->data['perms']['canPublishPost']){
			return array('view' => '403');
		}
				
		
		if($restore){
			$restorePost = $this->model->edit('blog_posts', $this->args[3], array('trash' => 0));
			Util\Session::flash('blog-message', $getPost['title'].' restored from trash', 'success');
			redirect($this->site.$this->moduleUrl.'/trash');
		}
		else{
			$delete = $this->model->edit('blog_posts', $this->args[3], array('trash' => 1));
			Util\Session::flash('blog-message', $getPost['title'].' moved to trash', 'success');
			redirect($this->site.$this->moduleUrl);
		}
	}		
		
	protected function clearTrash()
	{

		$trashPosts = $this->model->getAll('blog_posts', array('siteId' => $this->data['site']['siteId'],
															 'userId' => $this->user['userId'], 
															 'trash' => 1));
															 
		$tca = $this->tca;
		$postModule = $tca->get('modules', 'blog-post', array(), 'slug');
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');															 
		
		foreach($trashPosts as $getPost){
			if(($getPost['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canDeleteSelfPost'])
			OR ($getPost['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canDeleteOtherPost'])){
				return array('view' => '403');
			}

			if($getPost['published'] == 1 AND !$this->data['perms']['canPublishPost']){
				return array('view' => '403');
			}
			
			$postTCA = $tca->checkItemAccess($this->data['user'], $postModule['moduleId'], $getPost['postId'], 'blog-post');
			if(!$postTCA){
				return array('view' => '403');
			}
			$getCategories = $this->model->getAll('blog_postCategories', array('postId' => $getPost['postId']));
			foreach($getCategories as $cat){
				$catTCA = $tca->checkItemAccess($this->data['user'], $catModule['moduleId'], $cat['categoryId'], 'blog-category');
				if(!$catTCA){
					return array('view' => '403');
				}
			}			
			
			$delete = $this->model->delete('blog_posts', $getPost['postId']);
		}
		
		Util\Session::flash('blog-message', 'Trash bin emptied!', 'success');
		redirect($this->site.$this->moduleUrl.'/trash');
	
		return true;
	}		
	
	protected function comparePostVersions()
	{
		try{
			$getPost = $this->container->accessPost();
		}
		catch(\Exception $e){
			return array('view' => $e->getMessage());
		}
		
		$v1 = false;
		$v2 = false;
		if(isset($this->args[4])){
			$v1 = intval($this->args[4]);
		}
		if(isset($this->args[4])){
			$v2 = intval($this->args[5]);
		}
		
		$compare = $this->model->comparePostVersions($getPost['postId'], $v1, $v2);
		$compare['v1_user'] = array('userId' => $compare['v1_user']['userId'], 'username' => $compare['v1_user']['username'], 'slug' => $compare['v1_user']['slug']);
		$compare['v2_user'] = array('userId' => $compare['v2_user']['userId'], 'username' => $compare['v2_user']['username'], 'slug' => $compare['v2_user']['slug']);
		
		ob_end_clean();
		header('Content-Type: application/json');
		$output = $compare;
		
		echo json_encode($output);
		die();
	}	
	
	protected function requestContributor($output, $author_invite = false)
	{
		$redirect_link = $this->site.$this->data['app']['url'].'/'.$this->data['module']['url'].'/edit/'.$output['post']['postId'];

		if($author_invite){
			$getUser = $this->model->get('users', trim($_POST['username']), array('userId', 'username', 'slug', 'email'), 'username');
			if(!$getUser OR $getUser['userId'] == $output['post']['userId']){
				Util\Session::flash('blog-message', 'User '.$_POST['username'].' not found (contributor request)', 'error');
				redirect($redirect_link);
			}
		}
			
		$role = strip_tags($_POST['role']);
		if(trim($role) == ''){
			Util\Session::flash('blog-message', 'Must enter a contributor role', 'error');
			redirect($redirect_link);
		}
		$share = round(floatval($_POST['share']), 2);
		$contribs = $this->model->getPostContributors($output['post']['postId'], false);
		$totalShare = $share;
		foreach($contribs as $contrib){
			if(!$author_invite){
				if($this->data['user']['userId'] == $contrib['userId']){
					Util\Session::flash('blog-message', 'You already have a pending contribution request', 'error');				
					redirect($redirect_link);
				}
			}
			else{
				if($getUser['userId'] == $contrib['userId']){
					Util\Session::flash('blog-message', 'User already pending contribution request', 'error');
					redirect($redirect_link);
				}
			}
			$totalShare += $contrib['share'];
		}
		
		if($share < 0){
			Util\Session::flash('blog-message', 'Reward share cannot be less than 0', 'error');
			redirect($redirect_link);
		}
		
		if($totalShare > 100){
			Util\Session::flash('blog-message', 'Total reward share percentage cannot go over 100%', 'error');
			redirect($redirect_link);
		}
		
		if(!$author_invite){
			$inviteData = array('userId' => $this->data['user']['userId'], 'acceptUser' => $output['post']['userId'], 'sendUser' => $this->data['user']['userId'],
						  'type' => 'blog_contributor', 'itemId' => $output['post']['postId'], 'info' => array('request_type' => 'request',
						  'post_title' => $output['post']['title'], 'request_role' => $role, 'request_share' => $share),
						  'class' => '\\App\\Blog\\Submissions_Model');	
		}
		else{
			$inviteData = array('userId' => $getUser['userId'], 'acceptUser' => $getUser['userId'], 'sendUser' => $this->data['user']['userId'],
						  'type' => 'blog_contributor', 'itemId' => $output['post']['postId'], 'info' => array('request_type' => 'invite',
						  'post_title' => $output['post']['title'], 'request_role' => $role, 'request_share' => $share),
						  'class' => '\\App\\Blog\\Submissions_Model');	
		}
		
		$invite = $this->invite->sendInvite($inviteData);
		$contribData = array('postId' => $output['post']['postId'], 'inviteId' => $invite['inviteId'],
							'role' => $role, 'share' => $share);
		
		$add_contrib = $this->model->insert('blog_contributors', $contribData);
		Util\Session::flash('blog-message', 'Contributor request sent!', 'success');
		redirect($redirect_link);
	}
	
	protected function deleteContributor($output)
	{
		$redirect_link = $this->site.$this->data['app']['url'].'/'.$this->data['module']['url'].'/edit/'.$output['post']['postId'];
		$getContrib = $this->model->get('blog_contributors', @$this->args[6]);
		if(!$getContrib){
			$output['view'] = '404';
			return $output;
		}
		
		if($getContrib['postId'] != $output['post']['postId']){
			$output['view'] = '403';
			return $output;
		}
		
		$getInvite = $this->model->get('user_invites', $getContrib['inviteId']);
		$getUser = $this->model->get('users', $getInvite['userId'], array('userId', 'username', 'slug'));
		
		if(($getInvite['accepted'] == 0 AND $output['post']['userId'] == $this->data['user']['userId'])
			OR $this->data['perms']['canManageAllBlogs']
			OR ($getInvite['userId'] == $this->data['user']['userId'])){
			$delete = $this->model->delete('user_invites', $getContrib['inviteId']);
			if($delete){
				if($getInvite['accepted'] == 1){
					$notifyData = array();
					$notifyData['quitter'] = $getUser;
					$notifyData['post'] = $output['post'];
					$this->model->notifyContributors($output['post']['postId'], 'contributor_quit', $notifyData, 0);
				}
				Util\Session::flash('blog-message', $getUser['username'].' has been removed as a contributor.', 'success');
				redirect($redirect_link);		
			}
		}
		$output['view'] = '403';
		return $output;
	}
	
	protected function updateContributors($output)
	{
		$redirect_link = $this->site.$this->data['app']['url'].'/'.$this->data['module']['url'].'/edit/'.$output['post']['postId'];
		
		$changeRoles = false;
		$changeShares = false;
		
		if($output['post']['userId'] == $this->data['user']['userId']){
			$changeRoles = true;
		}
		
		if($this->data['perms']['canManageAllBlogs']){
			$changeRoles = true;
			$changeShares = true;
		}
		
		if(!$changeRoles AND !$changeShares){
			$output['view'] = '403';
			return $output;
		}
		
		$updateList = array();
		foreach($_POST as $k => $v){
			$exp = explode('_', $k);
			if(!isset($exp[1])){
				continue;
			}
			$itemId = false;
			if(($exp[0] == 'role' AND $changeRoles) OR ($exp[0] == 'share' AND $changeShares)){
				$itemId = intval($exp[1]);
				$getContrib = $this->model->get('blog_contributors', $itemId);
				if(!$getContrib){
					continue;
				}
			}
			
			if($itemId){
				if(!isset($updateList[$itemId])){
					$updateList[$itemId] = array();
				}
				$updateList[$itemId][$exp[0]] = $v;
			}
		}
		
		foreach($updateList as $itemId => $item){
			if(isset($item['share'])){
				$item['share'] = floatval($item['share']);
			}
			if(isset($item['role'])){
				$item['role'] = strip_tags($item['role']);
			}
			$edit = $this->model->edit('blog_contributors', $itemId, $item);
		}
		
		Util\Session::flash('blog-message', 'Contributor list updated!', 'success');
		redirect($redirect_link);	
	}
}
