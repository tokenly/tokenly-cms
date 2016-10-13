<?php
namespace App\Blog;
use App\Tokenly, App\Account, Util;
class Post_Controller extends \App\ModControl
{
	public $args;
	public $data;
	
    function __construct()
    {
        parent::__construct();
        $this->model = new Post_Model;
        $this->submitModel = new Submissions_Model;
        $this->blogModel = new Multiblog_Model;
    }
    
    protected function init()
    {
		$output = parent::init();
		
		if($this->itemId == null){
			if(!isset($this->args[2])){
				redirect($this->data['site']['url'].'/'.$this->data['app']['url']);
			}
		
			$getPost = $this->model->getPost($this->args[2], $this->data['site']['siteId']);
			if($getPost){
				//check for indexed version
				$getIndex = $this->model->getAll('page_index', array('itemId' => $getPost['postId'], 'moduleId' => $this->data['module']['moduleId']));
				if($getIndex AND count($getIndex) > 0){
					redirect($this->data['site']['url'].'/'.$getIndex[count($getIndex) - 1]['url']);
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
		
		$tca = new Tokenly\TCA_Model;
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
		$checkTCA = $tca->checkItemAccess($this->data['user'], $this->data['module']['moduleId'], $getPost['postId'], 'blog-post');
		if(!$checkTCA){
			$output['view'] = '403';
			return $output;
		}
		
		if($this->data['user']){
			$this->data['perms'] = $tca->checkPerms($this->data['user'], $this->data['perms'], $this->data['module']['moduleId'], $getPost['postId'], 'blog-post');
		}
		
		$getBlog = $this->model->getPostFirstBlog($getPost['postId']);

		$getBlog['settings'] = $this->blogModel->getSingleBlogSettings($getBlog);
        if(isset($getBlog['settings']['domain']) AND trim($getBlog['settings']['domain']) != ''){
            define('SITE_URL', $getBlog['settings']['domain']);
            static_cache('ALT_DOMAIN', true);
            $parse_blog_url = parse_url($getBlog['settings']['domain']);
            if(isset($parse_blog_url['host'])){
                if($_SERVER['HTTP_HOST'] != $parse_blog_url['host']){
                    redirect($getBlog['settings']['domain'].$_SERVER['REQUEST_URI']);
                }
            }
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
			$blogTCA = $tca->checkItemAccess($this->data['user'], $catModule['moduleId'], $cat['blogId'], 'multiblog');
			if(!$catTCA OR !$blogTCA){
			$output['view'] = '403';
			return $output;
			}
		}
		
		if($getBlog){
			if($getBlog['themeId'] != 0){
				$getTheme = $this->model->get('themes', $getBlog['themeId']);
				if($getTheme){
					$output['theme'] = $getTheme['location'];
				}
			}
		}		
		
		$output['blog'] = $getBlog;		
		$output['post'] = $getPost;
		$output['view'] = 'post';
		$output['title'] = $getPost['title'];
		$output['comments'] = $this->model->getPostComments($getPost['postId']);
		$output['commentError'] = '';
		$output['disableComments'] = false;
		$output['user'] = Account\Auth_Model::userInfo();
		$output['canonical'] = $this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/'.$getPost['url'];
		
		$metaDesc = $getPost['excerpt'];
		if(trim($metaDesc) == ''){
			//auto gen a meta description based on content
			$metaDesc = shortenMsg(strip_tags($getPost['content']), 500);
		}
		if($getPost['formatType'] == 'markdown'){
			$metaDesc = markdown($metaDesc);
		}
		$metaDesc = strip_tags($metaDesc);
		$output['metaDescription'] = $metaDesc;
		$output['blog']['settings']['meta_description'] = $metaDesc;
		
		$commentsEnabled = intval($getBlog['settings']['enableComments']);
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
						redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/'.$getPost['url']);
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
								redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/'.$getPost['url']);
							}
							
						}
						else{
							$output['commentForm']->setValues($getComment);
							$output['commentTitle'] = 'Edit Comment';
						}
					}
					else{
						redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/'.$getPost['url']);
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
			catch(\Exception $e){
				$output['commentError'] = $e->getMessage();
				$comment = false;
			}
			
			if($comment){
				redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/'.$getPost['url']);
			}

		}
		
		$viewed_posts = Util\Session::get('viewed_posts', array());
		if(!in_array($getPost['postId'], $viewed_posts) AND !botdetect()){
			$newViews = $getPost['views'] + 1;
			$this->model->edit('blog_posts', $getPost['postId'], array('views' => $newViews));
			Util\Session::set('viewed_posts', $getPost['postId'], APPEND_ARRAY);
		}

		if($this->data['user']){
			Tokenly\POP_Model::recordFirstView($this->data['user']['userId'], $this->data['module']['moduleId'], $getPost['postId']);
		}

		return $output;
	}
}
