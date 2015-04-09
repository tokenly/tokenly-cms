<?php
class Slick_App_Blog_Post_Controller extends Slick_App_ModControl
{
	public $args;
	public $data;
	
    function __construct()
    {
        parent::__construct();
        $this->model = new Slick_App_Blog_Post_Model;
        $this->submitModel = new Slick_App_Blog_Submissions_Model;
    }
    
    public function init()
    {
		$output = parent::init();
		
		if($this->itemId == null){
			if(!isset($this->args[2])){
				$this->redirect($this->data['site']['url'].'/'.$this->data['app']['url']);
				return false;
			}
		
			$getPost = $this->model->getPost($this->args[2], $this->data['site']['siteId']);
			if($getPost){
				//check for indexed version
				$getIndex = $this->model->getAll('page_index', array('itemId' => $getPost['postId'], 'moduleId' => $this->data['module']['moduleId']));
				if($getIndex AND count($getIndex) > 0){
					$this->redirect($this->data['site']['url'].'/'.$getIndex[count($getIndex) - 1]['url']);
					die();
					return false;
				}
			}
		}
		else{
			$getPost = $this->model->getPost($this->itemId, $this->data['site']['siteId']);
		}
		
		$checkApproved = false;
		if($getPost){
			$checkApproved = boolval($this->submitModel->checkPostApproved($getPost['postId']));
		}
		
		if(!$getPost OR $getPost['status'] != 'published' OR !$checkApproved){
			$output['view'] = '404';
			return $output;
		}
		
		$tca = new Slick_App_Tokenly_TCA_Model;
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
		$checkTCA = $tca->checkItemAccess($this->data['user'], $this->data['module']['moduleId'], $getPost['postId'], 'blog-post');
		if(!$checkTCA){
			$output['view'] = '403';
			return $output;
		}
		
		if($this->data['user']){
			$this->data['perms'] = $tca->checkPerms($this->data['user'], $this->data['perms'], $this->data['module']['moduleId'], $getPost['postId'], 'blog-post');
		}
		
		$getCats = $this->model->getAll('blog_postCategories', array('postId' => $getPost['postId']));
		$cats = array();
		foreach($getCats as $cat){
			$getCat = $this->model->get('blog_categories', $cat['categoryId']);
			$cats[] = $getCat;
		}
		$getPost['categories'] = $cats;
		
		foreach($getPost['categories'] as $cat){
			$catTCA = $tca->checkItemAccess($this->data['user'], $catModule['moduleId'], $cat['categoryId'], 'blog-category');
			if(!$catTCA){
			$output['view'] = '403';
			return $output;
			}
		}

		$output['post'] = $getPost;
		$output['view'] = 'post';
		$output['title'] = $getPost['title'];
		$output['comments'] = $this->model->getPostComments($getPost['postId']);
		$output['commentError'] = '';
		$output['disableComments'] = false;
		$output['user'] = Slick_App_Account_Home_Model::userInfo();
		$output['canonical'] = $this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/'.$getPost['url'];
		
		$metaDesc = $getPost['excerpt'];
		if(trim($metaDesc) == ''){
			$metaDesc = shortenMsg(strip_tags($getPost['content']), 500);
		}
		if($getPost['formatType'] == 'markdown'){
			$metaDesc = markdown($metaDesc);
		}
		$metaDesc = strip_tags($metaDesc);
		$output['metaDescription'] = $metaDesc;
		
		
		$commentsEnabled = $this->data['app']['meta']['enableComments'];
		if(!$output['user'] OR (intval($commentsEnabled) == 0)){
			$output['commentForm'] = false;
			if(intval($commentsEnabled) == 0){
				$output['disableComments'] = true;
			}

		}
		else{
			$output['commentForm'] = $this->model->getCommentForm();
		}
		$output['commentTitle'] = 'Post a new comment';
		

		if(isset($this->args[3]) AND isset($this->args[4])){
			switch($this->args[3]){
				case 'delete-comment':
					$commentId = intval($this->args[4]);
					if($output['user']){
						$getComment = $this->model->get('blog_comments', $commentId);
						if($getComment){
							if(($getComment['userId'] == $output['user']['userId'] AND $this->data['perms']['canDeleteSelfComment'])
								OR ($getComment['userId'] != $output['user']['userId'] AND $this->data['perms']['canDeleteOtherComment'])){
								$this->model->edit('blog_comments', $commentId, array('buried' => 1, 'message' => '[deleted]'));
							}
						}
						$this->redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/'.$getPost['url']);
						return $output;
					}
					break;
				case 'edit-comment':
					$commentId = intval($this->args[4]);
					$valid = 0;
					if($output['user']){
						$getComment = $this->model->get('blog_comments', $commentId);
						if($getComment){
							if(($getComment['userId'] == $output['user']['userId'] AND $this->data['perms']['canEditSelfComment'])
							 OR ($getComment['userId'] != $output['user']['userId'] AND $this->data['perms']['canEditOtherComment'])){
								$valid = 1;
							}
						}
						
					}
					if($valid === 1){
						if(posted()){
							$data = $output['commentForm']->grabData();
							if(trim($data['message']) != ''){
								mention($data['message'], '%username% has mentioned you in a 
										<a href="'.$this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/'.$getPost['url'].'#comment-'.$getComment['commentId'].'">blog comment.</a>',
										$this->data['user']['userId'], $getComment['commentId'], 'blog-reply');
								$this->model->edit('blog_comments', $commentId, array('message' => $data['message'], 'editTime' => timestamp()));
								$this->redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/'.$getPost['url']);
								return $output;
							}
							
						}
						else{
							$output['commentForm']->setValues($getComment);
							$output['commentTitle'] = 'Edit Comment';
						}
					}
					else{
						$this->redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/'.$getPost['url']);
						return $output;
					}
					break;
				
			}
		}

		if(posted() AND $output['user'] AND $this->data['perms']['canPostComment']){
			$data = $output['commentForm']->grabData();
			$data['postId'] = $getPost['postId'];
			$data['userId'] = $output['user']['userId'];
			try{
				$this->data['post'] = $getPost;
				$comment = $this->model->postComment($data, $this->data);
			}
			catch(Exception $e){
				$output['commentError'] = $e->getMessage();
				$comment = false;
			}
			
			if($comment){
				$this->redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/'.$getPost['url']);
				return $output;
			}

		}
		
		if(!isset($_SESSION['viewed_posts'])){
			$_SESSION['viewed_posts'] = array();
		}
		if(!in_array($getPost['postId'], $_SESSION['viewed_posts'])){
			$newViews = $getPost['views'] + 1;
			$this->model->edit('blog_posts', $getPost['postId'], array('views' => $newViews));
			$_SESSION['viewed_posts'][] = $getPost['postId'];
		}

		if($this->data['user']){
			Slick_App_Tokenly_POP_Model::recordFirstView($this->data['user']['userId'], $this->data['module']['moduleId'], $getPost['postId']);
		}

		
		return $output;
	}
	
	
}
