<?php
class Slick_App_Dashboard_BlogPost_Controller extends Slick_App_ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        
        $this->model = new Slick_App_Dashboard_BlogPost_Model;
        $this->user = Slick_App_Account_Home_Model::userInfo();
        
    }
    
    public function init()
    {
		$output = parent::init();
		$tca = new Slick_App_LTBcoin_TCA_Model;
		$postModule = $tca->get('modules', 'blog-post', array(), 'slug');
		$this->data['perms'] = Slick_App_Meta_Model::getUserAppPerms($this->data['user']['userId'], 'blog');
		$this->data['perms'] = $tca->checkPerms($this->data['user'], $this->data['perms'], $postModule['moduleId'], 0, '');
		
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->showPosts();
					break;
				case 'add':
					$output = $this->addPost();
					break;
				case 'edit':
					$output = $this->editPost();
					break;
				case 'delete':
					$output = $this->deletePost();
					break;
				case 'preview':
					$output = $this->previewPost($output);
					break;
				case 'checkInkpad':
					$output = $this->checkPostInkpad();
					break;
				default:
					$output = $this->showPosts();
					break;
			}
		}
		else{
			$output = $this->showPosts();
		}
		$output['template'] = 'admin';
        $output['perms'] = $this->data['perms'];
        
        return $output;
    }
    
    private function showPosts()
    {
		$output = array('view' => 'list');
		$getPosts = $this->model->getAll('blog_posts', array('siteId' => $this->data['site']['siteId'], 'trash' => 0), array(), 'postId');
		$tca = new Slick_App_LTBcoin_TCA_Model;
		$postModule = $tca->get('modules', 'blog-post', array(), 'slug');
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
		foreach($getPosts as $key => $row){
			$postPerms = $tca->checkPerms($this->data['user'], $this->data['perms'], $postModule['moduleId'], $row['postId'], 'blog-post');
			if(!$postPerms['canPublishPost'] AND !$postPerms['canEditOtherPost'] AND $row['userId'] != $this->data['user']['userId']){
				unset($getPosts[$key]);
				continue;
			}
			$postTCA = $tca->checkItemAccess($this->data['user'], $postModule['moduleId'], $row['postId'], 'blog-post');
			if(!$postTCA){
				unset($getPosts[$key]);
				continue;
			}
			$getCategories = $this->model->getAll('blog_postCategories', array('postId' => $row['postId']));
			foreach($getCategories as $cat){
				$catTCA = $tca->checkItemAccess($this->data['user'], $catModule['moduleId'], $cat['categoryId'], 'blog-category');
				if(!$catTCA){
					unset($getPosts[$key]);
					continue 2;
				}
			}
			$getAuthor = $this->model->get('users', $row['userId'], array('username'));
			$getPosts[$key]['author'] = $getAuthor['username'];
			$getPosts[$key]['perms'] = $postPerms;
		}
		$output['postList'] = $getPosts;

		
		return $output;
		
	}
	
	
	private function addPost()
	{
		$output = array('view' => 'form');
		if(!$this->data['perms']['canWritePost']){
			$output['view'] = '403';
			return $output;
		}
		
		$output['form'] = $this->model->getPostForm(0, $this->data['site']['siteId']);
		$output['formType'] = 'Add';

		if(!$this->data['perms']['canPublishPost']){
			$output['form']->field('status')->removeOption('published');
			$output['form']->remove('featured');
		}
		if(!$this->data['perms']['canSetEditStatus']){
			$output['form']->field('status')->removeOption('editing');
		}
		if(!$this->data['perms']['canChangeEditor']){
			$output['form']->remove('editedBy');
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
			if(!$this->data['perms']['canSetEditStatus']){
				if(isset($data['status']) AND $data['status'] == 'editing'){
					$data['status'] = 'draft';
				}
			}			
			try{
				$add = $this->model->addPost($data, $this->data);
			}
			catch(Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				$this->redirect($this->site.'/'.$this->moduleUrl);
				return true;
			}
			
		}
		
		$output['form']->field('publishDate')->setValue(timestamp());
		
		return $output;
		
	}
	

	
	private function editPost()
	{
		if(!isset($this->args[3])){
			return array('view' => '404');
		}
		
		$getPost = $this->model->get('blog_posts', $this->args[3]);
		if(!$getPost){
			return array('view' => '404');
		}

		$tca = new Slick_App_LTBcoin_TCA_Model;
		$postModule = $tca->get('modules', 'blog-post', array(), 'slug');
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');	

		$this->data['perms'] = $tca->checkPerms($this->data['user'], $this->data['perms'], $postModule['moduleId'], $getPost['postId'], 'blog-post');
		
		if(($getPost['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canEditSelfPost'])
		OR ($getPost['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canEditOtherPost'])){
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
		
		$getPost['categories'] = $this->model->getPostFormCategories($getPost['postId']);
		
		$output = array('view' => 'form');
		$output['form'] = $this->model->getPostForm($this->args[3], $this->data['site']['siteId']);
		$output['formType'] = 'Edit';
		$output['post'] = $getPost;
		
		if(isset($this->data['perms']['canUseMagicWords']) AND !$this->data['perms']['canUseMagicWords']){
			$getField = $this->model->get('blog_postMetaTypes', 'magic-word', array(), 'slug');
			if($getField){
				$output['form']->remove('meta_'.$getField['metaTypeId']);
			}
		}		
		
		$this->data['post'] = $getPost;
		
		if(!$this->data['perms']['canPublishPost']){
			$output['form']->field('status')->removeOption('published');
			$output['form']->remove('featured');
		}
		if(!$this->data['perms']['canChangeEditor']){
			$output['form']->remove('editedBy');
		}
		if(!$this->data['perms']['canSetEditStatus']){
			$output['form']->field('status')->removeOption('editing');
		}
		if(!$this->data['perms']['canChangeAuthor']){
			$output['form']->remove('userId');
		}
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			if(!$this->data['perms']['canChangeAuthor']){
				$data['userId'] = false;
			}
			//$data['userId'] = $this->user['userId'];
			if(!$this->data['perms']['canPublishPost']){
				if(isset($data['status']) AND $data['status'] == 'published'){
					$data['status'] = 'draft';
				}
				if(isset($data['featured'])){
					unset($data['featured']);
				}
			}
			if(!$this->data['perms']['canSetEditStatus']){
				if(isset($data['status']) AND $data['status'] == 'editing'){
					$data['status'] = 'draft';
				}
			}
			try{
				$add = $this->model->editPost($this->args[3], $data, $this->data);
			}
			catch(Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				$this->redirect($this->site.'/'.$this->moduleUrl);
				return true;
			}
			
		}
		//$getPost['status'] = '';
		if($getPost['published'] == 1){
			$getPost['status'] = 'published';
		}
		elseif($getPost['ready'] == 1){
			$getPost['status'] = 'ready';
		}
		/*else{
			$getPost['status'] = 'draft';
		}*/
		
		$output['form']->setValues($getPost);
		
		return $output;
		
	}
	

	
	
	private function deletePost()
	{
		if(!isset($this->args[3])){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$getPost = $this->model->get('blog_posts', $this->args[3]);
		if(!$getPost){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		if(($getPost['userId'] == $this->data['user']['userId'] AND !$this->data['perms']['canDeleteSelfPost'])
		OR ($getPost['userId'] != $this->data['user']['userId'] AND !$this->data['perms']['canDeleteOtherPost'])){
			return array('view' => '403');
		}

		if($getPost['published'] == 1 AND !$this->data['perms']['canPublishPost']){
			return array('view' => '403');
		}
		
		$tca = new Slick_App_LTBcoin_TCA_Model;
		$postModule = $tca->get('modules', 'blog-post', array(), 'slug');
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
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
		
		$delete = $this->model->delete('blog_posts', $this->args[3]);
		$this->redirect($this->site.'/'.$this->moduleUrl);
		return true;
	}
	
	private function previewPost($output)
	{
		if(!isset($this->args[3])){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$model = new Slick_App_Blog_Post_Model;
		$getPost = $model->getPost($this->args[3], $this->data['site']['siteId']);
		if(!$getPost){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$tca = new Slick_App_LTBcoin_TCA_Model;
		$postModule = $tca->get('modules', 'blog-post', array(), 'slug');
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
		$this->data['perms'] = $tca->checkPerms($this->data['user'], $this->data['perms'], $postModule['moduleId'], $getPost['postId'], 'blog-post');
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
		
		
		if($getPost['formatType'] == 'markdown'){
			$getInkpad = $this->model->getPostMetaVal($getPost['postId'], 'inkpad-url');
			$getExcerptInkpad = $this->model->getPostMetaVal($getPost['postId'], 'inkpad-excerpt-url');
			$inkpad = new Slick_UI_Inkpad('inkpad');
			if($getInkpad){
				$inkpad->setInkpad($getInkpad);
				$content = $inkpad->getValue();
				if($content){
					$getPost['content'] = $content;
				}
			}
			if($getExcerptInkpad){
				$inkpad->setInkpad($getExcerptInkpad);
				$excerpt = $inkpad->getValue();
				if($excerpt){
					$getPost['excerpt'] = $excerpt;
				}
			}
		}
		
		
		$cats = array();
		foreach($getCategories as $cat){
			$getCat = $this->model->get('blog_categories', $cat['categoryId']);
			$cats[] = $getCat;
		}
		$getPost['categories'] = $cats;
		
		$output['template'] = 'blog';
		$output['view'] = '../../Blog/Post/post';
		$output['post'] = $getPost;
		$output['disableComments'] = true;
		$output['user'] = Slick_App_Account_Home_Model::userInfo();
		$output['title'] = $getPost['title'];
		$output['commentError'] = '';
		$output['comments'] = array();
		

		return $output;
		
	}
	
	private function checkPostInkpad()
	{
		if(!isset($this->args[3])){
			return array('view' => '404');
		}
		
		$getPost = $this->model->get('blog_posts', $this->args[3]);
		if(!$getPost){
			return array('view' => '404');
		}
		
		$tca = new Slick_App_LTBcoin_TCA_Model;
		$postModule = $tca->get('modules', 'blog-post', array(), 'slug');
		$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
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
		
		ob_end_clean();
		header('Content-Type: application/json');		
		
		$output = array('result' => null, 'error' => null);
		$excerptPad = $this->model->getPostMetaVal($getPost['postId'], 'inkpad-excerpt-url');
		$contentPad = $this->model->getPostMetaVal($getPost['postId'], 'inkpad-url');
		
		$inkpad = new Slick_UI_Inkpad('inkpad');
		$inkpad2 = new Slick_UI_Inkpad('inkpad');
		if(!$excerptPad OR !$contentPad){
			$output['error'] = 'No Inkpad URL set';
		}
		else{
			$inkpad->setInkpad($excerptPad);
			$inkpad2->setInkpad($contentPad);
			
			$excerpt = $inkpad->getValue();
			$content = $inkpad2->getValue();
			if(!$excerpt OR !$content){
				$output['error'] = 'Error getting pad data';
			}
			else{
				$output['result'] = array('excerpt' => false, 'content' => false);
				if(md5($excerpt) == md5($getPost['excerpt'])){
					$output['result']['excerpt'] = true;
				}
				if(md5($content) == md5($getPost['content'])){
					$output['result']['content'] = true;
				}
			}
		}
		
		echo json_encode($output);
		die();
	}
	
}
?>
