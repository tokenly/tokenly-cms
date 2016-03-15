<?php
namespace App\API\V1;
use API;
class Blog_Controller extends \Core\Controller
{
	public $methods = array('GET', 'POST','PATCH','DELETE');
	
	function __construct()
	{
		parent::__construct();
		$this->model = new Blog_Model;
		
	}
	
	protected function init($args = array())
	{
		$this->args = $args;
		$output = array();
		
		if(isset($args[1])){
			switch($args[1]){
				case 'categories':
					$output = $this->container->category();
					break;
				case 'archive':
					$output = $this->container->archive();
					break;
				case 'posts':
					$output = $this->container->getAllPosts();
					break;
				case 'meta':
					if(isset($args[2])){
						switch($args[2]){
							case 'types':
								$output = $this->container->getMetaTypes();
								break 2;
						}
					}
				default:
					http_response_code(400);
					$output['error'] = 'Invalid Request';
					break;
				
			}
		}
		
		return $output;
	}
	
	protected function getMetaTypes()
	{
		$output = array();
		$getTypes = $this->model->getAll('blog_postMetaTypes', array('siteId' => $this->args['data']['site']['siteId']), array(), 'rank', 'asc');
		$output['meta-types'] = array();
		foreach($getTypes as $type){
			$output['meta-types'][] = $type['slug'];
		}
		
		return $output;
		
	}
	
	protected function comments()
	{
		$output = array();
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'add':
					$output = $this->container->addComment();
					break;
				case 'edit':
					$output = $this->container->editComment();
					break;
				case 'delete':
					$output = $this->container->deleteComment();
					break;
				case 'get':
					$output = $this->container->getComment();
					break;
				default:
					$output = $this->container->getPostComments();
					break;
				
			}
			
		}
		else{
			$output = $this->container->getPostComments();
		}
		
		
		return $output;
	}
	
	protected function getComment()
	{
		$output = array();
		
		if(!isset($this->args[4])){
			http_response_code(400);
			$output['error'] = 'Invalid request';
			return $output;
		}


		$model = new \App\Blog\Post_Model;
		
		$getPost = $model->get('blog_posts', $this->args[2], array('postId'), 'url');
		if(!$getPost){
			$getPost = $model->get('blog_posts', $this->args[2], array('postId'));
			if(!$getPost){
				http_response_code(400);
				$output['error'] = 'Post not found';
				return $output;
			}
		}
		
		try{
			$thisUser = Auth_Model::getUser($this->args['data']);
		}
		catch(\Exception $e){
			$thisUser = false;
		}		
		
		$tca = new \App\Tokenly\TCA_Model;
		$profileModule = get_app('profile.user-profile');			
		
		/* Disqus Comments Code */
		$disqus = new API\Disqus;
		$profModel = new \App\Profile\User_Model;
		$getComment = $disqus->getPost($this->args[4]);
		
		$comment = array();
		$comment['commentId'] = $getComment['id'];
		if($comment['commentId'] == null){
			http_response_code(404);
			$output['error'] = 'Comment not found';
			return $output;
		}
		$comment['postId'] = $getPost['postId'];
		$comment['message'] = $getComment['message'];
		$comment['commentDate'] = $getComment['createdAt'];
		$comment['buried'] = 0;
		if($getComment['isDeleted'] == 1 || $getComment['isSpam'] == 1){
			$comment['buried'] = 1;
		}
		$comment['editTime'] = null;
		$author = array();
		$author['username'] = $getComment['author']['name'];
		$author['slug'] = genURL($getComment['author']['name']);
		if(!isset($getComment['author']['joinedAt'])){
			$author['regDate'] = $comment['commentDate'];
		}
		else{
			$author['regDate'] = $getComment['author']['joinedAt'];
		}
		$author['profile'] = array();
		$author['avatar'] = $getComment['author']['avatar']['permalink'];
	
		$getComUser = $model->get('users', $author['username'], array('userId'), 'username');
		if($getComUser){
			$getComProf = $profModel->getUserProfile($getComUser['userId'], $this->args['data']['site']['siteId']);
			if($getComProf){
				$comTCA = $tca->checkItemAccess($thisUser, $profileModule['moduleId'], $getComProf['userId'], 'user-profile');
				if($comTCA){
					$author['profile'] = $getComProf['profile'];
				}				
				$author['regDate'] = $getComProf['regDate'];
				$author['slug'] = $getComProf['slug'];
			}
		}
		$comment['author'] = $author;
		$output['comment'] = $comment;
		
		
		/* -- native site comments code --
		$getComment = $model->getComment($this->args[4]);
		if(!$getComment OR $getComment['postId'] != $getPost['postId']){
			http_response_code(400);
			$output['error'] = 'Comment not found';
			return $output;
		}
		$output['comment'] = $getComment;
		*/

		return $output;
	}
	
	protected function addComment()
	{
		$output = array();
		if($this->useMethod != 'POST'){
			http_response_code(400);
			$output['error'] = 'Invalid request method';
			$output['methods'] = array('POST');
			return $output;
		}
				
		try{
			$user = Auth_Model::getUser($this->args['data']);
		}
		catch(\Exception $e){
			http_response_code(403);
			$output['error'] = $e->getMessage();
			return $output;
		}
		
		$this->args['data']['user'] = $user;

		$model = new \App\Blog\Post_Model;
		
		$getPost = $model->get('blog_posts', $this->args[2], array('postId'), 'url');
		if(!$getPost){
			$getPost = $model->get('blog_posts', $this->args[2], array('postId'));
			if(!$getPost){
				http_response_code(400);
				$output['error'] = 'Post not found';
				return $output;
			}
		}

		$this->args['data']['postId'] = $getPost['postId'];
		
		try{
			$meta = new \App\Meta_Model;
			$blogApp = $meta->get('apps', 'blog', array(), 'slug');
			$appMeta = $meta->appMeta($blogApp['appId']);
			$blogApp['meta'] = $appMeta;
			$appData = array();
			$appData['post'] = $meta->get('blog_posts', $getPost['postId']);;
			$appData['user'] = $user;
			$appData['app'] = $blogApp;
			$appData['module'] = $meta->get('modules', 'blog-post', array(), 'slug');
			$appData['site'] = $meta->get('sites', $_SERVER['HTTP_HOST'], array(), 'domain');
			$add = $this->model->addComment($this->args['data'], $appData);
		}
		catch(\Exception $e){
			http_response_code(400);
			$output['error'] = $e->getMessage();
			return $output;
		}
		
		$output['result'] = $add;
				
		return $output;
	}
	
	protected function editComment()
	{
		$output = array();
		if($this->useMethod != 'PATCH'){
			http_response_code(400);
			$output['error'] = 'Invalid request method';
			$output['methods'] = array('PATCH');
			return $output;
		}
				
		try{
			$user = Auth_Model::getUser($this->args['data']);
		}
		catch(\Exception $e){
			http_response_code(403);
			$output['error'] = $e->getMessage();
			return $output;
		}
		
		if(!isset($this->args[4])){
			http_response_code(400);
			$output['error'] = 'Invalid request';
			return $output;
		}
		
		$getPost = $this->model->get('blog_posts', $this->args[2], array('postId'), 'url');
		if(!$getPost){
			$getPost = $this->model->get('blog_posts', $this->args[2], array('postId'));
			if(!$getPost){
				http_response_code(400);
				$output['error'] = 'Post not found';
				return $output;
			}
		}
		
		/* Disqus Comments */
		$disqus = new API\Disqus;
		$profModel = new \App\Profile\User_Model;
		$getComment = $disqus->getPost($this->args[4]);
		if(!$getComment || $getComment['isDeleted'] == 1 || $getComment['isSpam'] == 1){
			http_response_code(400);
			$output['error'] = 'Comment not found';
			return $output;
		}
		
		if($getComment['author']['name'] != $user['username']){
			http_response_code(403);
			$output['error'] = 'Comment does not belong to you';
			return $output;
		}
		
		if(!isset($this->args['data']['message'])){
			http_response_code(400);
			$output['error'] = 'Message required';
			return $output;
		}
		
		
		$userProf = $profModel->getUserProfile($user['userId'], $this->args['data']['site']['siteId']);
		$editData = array('postId' => $this->args[4], 'message' => $this->args['data']['message']);
		$editPost = $disqus->editPost($editData);
		if(!$editPost){
			http_response_code(400);
			$output['error'] = 'Error editing comment';
			return $output;
		}
		
		$output['result'] = array('commentId' => $getComment['id'], 'postId' => $getPost['postId'],
								  'message' => $this->args['data']['message'], 'commentDate' => $getComment['createdAt'],
								  'buried' => 0, 'editTime' => timestamp());
		
		/*
		-- native comment system code --
		$get = $this->model->get('blog_comments', $this->args[4]);
		if(!$get OR $get['buried'] == 1 OR $get['postId'] != $getPost['postId']){
			http_response_code(400);
			$output['error'] = 'Comment not found';
			return $output;
		}
		
		if($get['userId'] != $user['userId']){
			http_response_code(403);
			$output['error'] = 'Comment does not belong to you';
			return $output;
		}
		
		if(!isset($this->args['data']['message'])){
			http_response_code(400);
			$output['error'] = 'Message required';
			return $output;
		}
		
		$stamp = timestamp();
		$edit = $this->model->edit('blog_comments', $get['commentId'], array('message' => $this->args['data']['message'], 'editTime' => $stamp));
		
		if(!$edit){
			http_response_code(400);
			$output['error'] = 'Error editing comment';
			return $output;
		}
		
		$get['message'] = $this->args['data']['message'];
		$get['editTime'] = $stamp;
		unset($get['userId']);
		$output['result'] = $get;
		*/
		
		return $output;
	}
	
	protected function deleteComment()
	{
		$output = array();
		if($this->useMethod != 'DELETE'){
			http_response_code(400);
			$output['error'] = 'Invalid request method';
			$output['methods'] = array('DELETE');
			return $output;
		}
				
		try{
			$user = Auth_Model::getUser($this->args['data']);
		}
		catch(\Exception $e){
			http_response_code(403);
			$output['error'] = $e->getMessage();
			return $output;
		}
		
		if(!isset($this->args[4])){
			http_response_code(400);
			$output['error'] = 'Invalid request';
			return $output;
		}
		
		$getPost = $this->model->get('blog_posts', $this->args[2], array('postId'), 'url');
		if(!$getPost){
			$getPost = $this->model->get('blog_posts', $this->args[2], array('postId'));
			if(!$getPost){
				http_response_code(400);
				$output['error'] = 'Post not found';
				return $output;
			}
		}
		
		
		/* Disqus Comments */
		$disqus = new API\Disqus;
		$getComment = $disqus->getPost($this->args[4]);
		if(!$getComment || $getComment['isDeleted'] == 1 || $getComment['isSpam'] == 1){
			http_response_code(400);
			$output['error'] = 'Comment not found';
			return $output;
		}
		if($getComment['author']['name'] != $user['username']){
			http_response_code(403);
			$output['error'] = 'Comment does not belong to you';
			return $output;
		}
		
		$delete = $disqus->deletePost($this->args[4]);
		if(!$delete){
			http_response_code(400);
			$output['error'] = 'Error deleting comment';
			return $output;
		}
		$output['result'] = 'success';
		
		
		/*
		-- native comment system code --
		$get = $this->model->get('blog_comments', $this->args[4]);
		if(!$get OR $get['buried'] == 1){
			http_response_code(400);
			$output['error'] = 'Comment not found';
			return $output;
		}
		
		if($get['userId'] != $user['userId']){
			http_response_code(403);
			$output['error'] = 'Comment does not belong to you';
			return $output;
		}
		
		
		$edit = $this->model->edit('blog_comments', $get['commentId'], array('buried' => 1, 'message' => '[deleted]'));
		
		if(!$edit){
			http_response_code(400);
			$output['error'] = 'Error deleting comment';
			return $output;
		}
		
		$output['result'] = 'success';
		*/
				
		return $output;
	}
	
	
	protected function getPostComments()
	{
		$output = array();
			
		if(isset($this->args[4])){
			switch($this->useMethod){
				case 'GET':
					return $this->container->getComment();
					break;
				case 'PATCH':
					return $this->container->editComment();
					break;
				case 'DELETE':
					return $this->container->deleteComment();
					break;
			}
		}
		else{
			if($this->useMethod == 'POST'){
				return $this->container->addComment();
			}
		}
		if($this->useMethod != 'GET'){
			http_response_code(400);
			$output['error'] = 'Invalid request method';
			$output['methods'] = array('GET');
			return $output;
		}

		$model = new \App\Blog\Post_Model;
		
		$getPost = $model->get('blog_posts', $this->args[2], array('postId', 'url', 'published'), 'url');
		if(!$getPost){
			$getPost = $model->get('blog_posts', $this->args[2], array('postId','url', 'published'));
			if(!$getPost){
				http_response_code(400);
				$output['error'] = 'Post not found';
				return $output;
			}
		}
		
		try{
			$thisUser = Auth_Model::getUser($this->args['data']);
		}
		catch(\Exception $e){
			$thisUser = false;
		}

		$tca = new \App\Tokenly\TCA_Model;
		$profileModule = get_app('profile.user-profile');	
		$postModule = get_app('blog.blog-post');
		
		$postTCA = $tca->checkItemAccess($thisUser, $postModule['moduleId'], $getPost['postId'], 'blog-post');	
		if(!$postTCA OR $getPost['published'] == 0){
			http_response_code(403);
			$output['error'] = 'You cannot view this post';
			return $output;
		}				

		/* Disqus Comments Code */
		$disqus = new API\Disqus;
		$profModel = new \App\Profile\User_Model;
		$getIndex = $model->getAll('page_index', array('itemId' => $getPost['postId'], 'moduleId' => 28));
		$postURL = $this->args['data']['site']['url'].'/blog/post/'.$getPost['url'];
		if($getIndex AND count($getIndex) > 0){
			$postURL = $this->args['data']['site']['url'].'/'.$getIndex[count($getIndex) - 1]['url'];
			
		}


		$getThread = $disqus->getThread($postURL);
	    if(!$getThread){
			$output['comments'] = array();
			return $output;
		}
		
		if(!isset($this->args['data']['strip-html'])){
			$this->args['data']['strip-html'] = true;
		}
		
		$comments = $getThread['posts'];
		$output['comments'] = array();
		foreach($comments as $com){
			
			$comment = array();
			$comment['commentId'] = $com['id'];
			$comment['postId'] = $getPost['postId'];
			
			if(isset($this->args['data']['strip-html']) AND ($this->args['data']['strip-html'] == 'true' || $this->args['data']['strip-html'] === true)){
				$com['message'] = strip_tags($com['message']);
			}
			
			$comment['message'] = $com['message'];
			$comment['commentDate'] = $com['createdAt'];
			$comment['buried'] = 0;
			if($com['isDeleted'] == 1 || $com['isSpam'] == 1){
				//$comment['buried'] = 1;
				continue;
			}
			$comment['editTime'] = null;
			$author = array();
			$author['username'] = $com['author']['name'];
			$author['slug'] = genURL($com['author']['name']);
			$author['regDate'] = $com['author']['joinedAt'];
			$author['profile'] = array();
			//$author['avatar'] = $this->args['data']['site']['url'].'/files/avatars/default.jpg';
			$author['avatar'] = $com['author']['avatar']['permalink'];
		
			$getComUser = $model->get('users', $author['username'], array('userId'), 'username');
			if($getComUser){
				$getComProf = $profModel->getUserProfile($getComUser['userId'], $this->args['data']['site']['siteId']);
				if($getComProf){
					$comTCA = $tca->checkItemAccess($thisUser, $profileModule['moduleId'], $getComProf['userId'], 'user-profile');
					if($comTCA){
						$author['profile'] = $getComProf['profile'];
					}
					$author['regDate'] = $getComProf['regDate'];
					$author['slug'] = $getComProf['slug'];
				}
			}
			
			$comment['author'] = $author;
			
			$output['comments'][] = $comment;
		}

		
		/*
		--native comment system code--
		$getComments = $model->getPostComments($getPost['postId']);
		foreach($getComments as $k => $row){
			$author = $row['author'];
			if(is_array($author)){
				unset($author['userId']);
				unset($author['lastActive']);
				unset($author['lastAuth']);
				if($author['pubProf'] == 0){
					unset($author['email']);
					unset($author['profile']);
				}
				else{
					if($author['showEmail'] == 0){
						unset($author['email']);
					}
				}
				unset($author['showEmail']);
				unset($author['pubProf']);
			}
			$row['author'] = $author;
			$getComments[$k] = $row;
		}
		$output['comments'] = $getComments;
		*/
		
		http_response_code(200);
		return $output;
	}
	
	protected function post()
	{
		$output = array();
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'get':
					$output = $this->container->getPost();
					break;
				default:
					http_response_code(400);
					$output['error'] = 'Invalid request';
					return $output;
			}
		}
		else{
			http_response_code(400);
			$output['error'] = 'Invalid request';
			return $output;
		}
		
		return $output;
		
	}
	
	protected function getPost()
	{
		$output = array();
		
		if(!isset($this->args[2])){
			http_response_code(400);
			$output['error'] = 'Invalid request';
			return $output;
		}
		
		if(isset($this->args[3]) AND $this->args[3] == 'comments'){
			return $this->container->getPostComments();
		}
		
		$model = new \App\Blog\Post_Model;
		$submitModel = new \App\Blog\Submissions_Model;
		$getPost = $model->getPost($this->args[2], $this->args['data']['site']['siteId']);
		$approved = false;
		if($getPost){
			$approved = $submitModel->checkPostApproved($getPost['postId']);
		}
		if(!$getPost OR !$approved){
			http_response_code(400);
			$output['error'] = 'Post not found';
			return $output;
		}
		
		try{
			$thisUser = Auth_Model::getUser($this->args['data']);
		}
		catch(\Exception $e){
			$thisUser = false;
		}		
		
		$tca = new \App\Tokenly\TCA_Model;
		$profileModule = get_app('profile.user-profile');
		$postModule = get_app('blog.blog-post');
		
		$postTCA = $tca->checkItemAccess($thisUser, $postModule['moduleId'], $getPost['postId'], 'blog-post');	
		if(!$postTCA OR $getPost['published'] == 0){
			http_response_code(403);
			$output['error'] = 'You cannot view this post';
			return $output;
		}
		
		$authorTCA = $tca->checkItemAccess($thisUser, $profileModule['moduleId'], $getPost['userId'], 'user-profile');		
		
		unset($getPost['userId']);
		unset($getPost['siteId']);
		unset($getPost['author']['userId']);
		unset($getPost['author']['lastActive']);
		unset($getPost['author']['lastAuth']);
		if($getPost['author']['pubProf'] == 0){
			unset($getPost['author']['email']);
			unset($getPost['author']['profile']);
		}
		else{
			if($getPost['author']['showEmail'] == 0){
				unset($getPost['author']['email']);
			}
		}
		unset($getPost['author']['pubProf']);
		unset($getPost['author']['showEmail']);
		
		if(trim($getPost['image']) != ''){
			$getPost['image'] = $this->args['data']['site']['url'].'/files/blogs/'.$getPost['image'];
		}
		else{
			$getPost['image'] = null;
		}
		
		if(trim($getPost['coverImage']) != ''){
			$getPost['coverImage'] = $this->args['data']['site']['url'].'/files/blogs/'.$getPost['coverImage'];
		}
		else{
			$getPost['coverImage'] = null;
		}
		
		if(!$authorTCA){
			$getPost['author']['profile'] = array();
		}
		
		
		if(isset($this->args['data']['strip-html']) AND ($this->args['data']['strip-html'] == 'true' || $this->args['data']['strip-html'] === true)){
			$getPost['excerpt'] = strip_tags($getPost['excerpt']);
			$getPost['content'] = strip_tags($getPost['content']);
		}
		
		$getCats = $this->model->getAll('blog_postCategories', array('postId' => $getPost['postId']));
		$cats = array();
		foreach($getCats as $cat){
			$getCat = $this->model->get('blog_categories', $cat['categoryId']);
			if($getCat['image'] != ''){
				$getCat['image'] = $this->args['data']['site']['url'].'/files/blogs/'.$getCat['image'];
			}
			$cats[] = $getCat;
		}
		$getPost['categories'] = $cats;
		
		$output['post'] = $getPost;
		http_response_code(200);
		return $output;
	}
	
	protected function category()
	{
		$output = array();
		if(isset($this->args[2])){
			$output = $this->container->getCategory();
		}
		else{
			$output = $this->container->getCategoryList();
			
		}
		
		return $output;
	}
	
	protected function getCategory()
	{
		if(isset($this->args[3]) AND $this->args[3] == 'posts'){
			return $this->container->getCategoryPosts();
		}
		
		$output = array();
		if(!isset($this->args[2])){
			http_response_code(400);
			$output['error'] = 'No category ID set';
			return $output;
		}
		
		$model = new \App\Blog\Categories_Model;
		$get = $this->model->get('blog_categories', $this->args[2]);
		if(!$get){
			$get = $this->model->get('blog_categories', $this->args[2], array(), 'slug');
			if(!$get){
				http_response_code(400);
				$output['error'] = 'Category not found';
				return $output;
			}
			$getBlog = $this->model->get('blogs', $get['blogId']);
			if(!$getBlog OR $getBlog['active'] == 0){
				http_response_code(400);
				$output['error'] = 'Category not found';
				return $output;
			}
		}
		if($get['image'] != ''){
			$get['image'] = $this->args['data']['site']['url'].'/files/blogs/'.$get['image'];
		}
		
		try{
			$thisUser = Auth_Model::getUser($this->args['data']);
		}
		catch(\Exception $e){
			$thisUser = false;
		}			
		
		$tca = new \App\Tokenly\TCA_Model;
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
		$catTCA = $tca->checkItemAccess($thisUser, $catModule['moduleId'], $get['categoryId'], 'blog-category');
		if(!$catTCA){
			http_response_code(403);
			$output['error'] = 'You may not view this category';
			return $output;
		}
		
		
		$children = $model->getCategories($this->args['data']['site']['siteId'], $get['categoryId']);
		if(count($children) > 0){
			$get['children'] = $children;
		}
		
		$output['category'] = $get;
		
		return $output;
		
	}
	
	protected function getCategoryPosts()
	{
		$output = array();
		$output = array();
		if(!isset($this->args[2])){
			http_response_code(400);
			$output['error'] = 'No category ID set';
			return $output;
		}
		
		$model = new \App\Blog\Category_Model;
		$get = $this->model->get('blog_categories', $this->args[2]);
		if(!$get){
			$get = $this->model->get('blog_categories', $this->args[2], array(), 'slug');
			if(!$get){
				http_response_code(400);
				$output['error'] = 'Category not found';
				return $output;
			}
		}
		
		$limit = 15;
		$page = false;
		if(isset($this->args['data']['limit'])){
			$limit = intval($this->args['data']['limit']);
		}
		if(isset($this->args['data']['page'])){
			$page = $this->args['data']['page'];
		}
		
		$getPosts = $model->getCategoryPosts($get['categoryId'], $this->args['data']['site']['siteId'], $limit, array(), $page);
		$postModel = new \App\Blog\Post_Model;
		foreach($getPosts as $k => $row){
			unset($row['userId']);
			$author = $row['author'];
			unset($author['userId']);
			unset($author['lastActive']);
			unset($author['lastAuth']);
			if($author['pubProf'] == 0){
				unset($author['profile']);
				unset($author['email']);
			}
			else{
				if($author['showEmail'] == 0){
					unset($author['email']);
				}
			}
			unset($author['pubProf']);
			unset($author['showEmail']);

			$row['author'] = $author;
			$getPosts[$k] = $row;
			if(trim($row['image']) != ''){
				$getPosts[$k]['image'] = $this->args['data']['site']['url'].'/files/blogs/'.$row['image'];
			}
			else{
				$getPosts[$k]['image'] = null;
			}
			$getMeta = $postModel->getPostMeta($post['postId']);
			foreach($getMeta as $mkey => $val){
				if(!isset($getPosts[$k][$mkey])){
					$getPosts[$k][$mkey] = $val;
				}
			}
		}
		$output['posts'] = $getPosts;
		
		return $output;
	}
	
	protected function getCategoryList()
	{
		$model = new \App\Blog\Categories_Model;
		$getCats = $model->getCategories($this->args['data']['site']['siteId']);
		
		$output['categories'] = $getCats;
		return $output;
	}

	protected function archive()
	{
		$output = array();
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'all':
					$output = $this->container->getAllPosts();
					break;
				case 'archives':
					$output = $this->container->getArchiveList();
					break;
				default:
					$output = $this->container->getArchive();
					break;
			}
		}
		else{
			http_response_code(400);
			$output['error'] = 'Invalid request';
			return $output;
		}
		
		return $output;
	}
	
	protected function getAllPosts()
	{
		$output = array();
		
		if(isset($this->args[2])){
			return $this->container->getPost();
		}
		
		try{
			$this->args['data']['user'] = Auth_Model::getUser($this->args['data']);
		}
		catch(\Exception $e){
			$this->args['data']['user'] = false;
		}

		try{
			$output['posts'] = $this->model->getAllPosts($this->args['data']);
		}
		catch(\Exception $e){
			http_response_code(400);
			$output['error'] = $e->getMessage();
			return $output;
		}

		http_response_code(200);
		
		return $output;
	}
	
	protected function getArchiveList()
	{
		$catModel = new \App\Blog\Categories_Model;
		$output = array();
		
		$getArchive = $catModel->getArchiveList($this->args['data']['site']['siteId']);
		$output['archives'] = $getArchive;
		
		return $output;
	}
	
	
	protected function getArchive()
	{
		$output = array();
		
		if(!isset($this->args[2])){
			http_response_code(400);
			$output['error'] = 'Invalid request';
			return $output;
		}
		

		$month = 1;
		$useMonth = 0;
		if(isset($this->args[3])){
			$month = intval($this->args[3]);
			$useMonth = 1;
		}
		$day = 1;
		$useDay = 0;
		if(isset($this->args[4])){
			$useDay = 1;
			$day = intval($this->args[4]);
		}
		$minYear = 2013;
		$maxYear = date('Y');
		$year = intval($this->args[2]);
		
		
		if($year < $minYear || $year > $maxYear || $day < 1 || $day > 31 || $month < 1 || $month > 12){
			http_response_code(400);
			$output['error'] = 'Invalid request';
			return $output;
		}
		
		$title = $year;
		if($useMonth == 1 AND $useDay == 0){
			$title = date('F, Y', strtotime($year.'-'.$month.'-1'));
		}
		if($useMonth == 1 AND $useDay == 1){
			$title = date('F jS, Y', strtotime($year.'-'.$month.'-'.$day));
		}
		
		if(!isset($this->args['data']['limit'])){
			$postLimit = 15;
		}
		else{
			$postLimit = intval($this->args['data']['limit']);
		}
		
		if(isset($this->args['data']['page'])){
			$_GET['page'] = intval($this->args['data']['page']);
		}
		
		$model = new \App\Blog\Archive_Model;
		$postModel = new \App\Blog\Post_Model;
		$getPosts = $model->getArchivePosts($this->args['data']['site']['siteId'], $postLimit, $year, $month, $day, $useMonth, $useDay);
		foreach($getPosts as $key => $row){
			unset($row['userId']);
			$author = $row['author'];
			unset($author['userId']);
			unset($author['lastActive']);
			unset($author['lastAuth']);
			if($author['pubProf'] == 0){
				unset($author['profile']);
				unset($author['email']);
			}
			else{
				if($author['showEmail'] == 0){
					unset($author['email']);
				}
			}
			unset($author['pubProf']);
			unset($author['showEmail']);

			$row['author'] = $author;
			$getPosts[$key] = $row;
			if(trim($row['image']) != ''){
				$getPosts[$key]['image'] = $this->args['data']['site']['url'].'/files/blogs/'.$row['image'];
			}
			else{
				$getPosts[$key]['image'] = null;
			}
			$getMeta = $postModel->getPostMeta($post['postId']);
			foreach($getMeta as $mkey => $val){
				if(!isset($getPosts[$key][$mkey])){
					$getPosts[$key][$mkey] = $val;
				}
			}
		}
		$output['posts'] = $getPosts;
		
		return $output;
	}
}
