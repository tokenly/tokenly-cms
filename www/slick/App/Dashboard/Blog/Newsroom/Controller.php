<?php
class Slick_App_Dashboard_Blog_Newsroom_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_Dashboard_Blog_Newsroom_Model;
		$this->submitModel = new Slick_App_Dashboard_Blog_Submissions_Model;
	}
	
	public function init()
	{
		$output = parent::init();
		$this->data['perms'] = Slick_App_Meta_Model::getUserAppPerms($this->data['user']['userId'], 'blog');
        if(isset($this->args[2])){
			switch($this->args[2]){
				default:
					$output = $output['view'] = '404';
					break;
			}
		}
		else{
			$output = $this->showNewsroom($output);
		}
		$output['template'] = 'admin';
        $output['perms'] = $this->data['perms'];	
        return $output;
	}
	
	protected function showNewsroom($output)
	{
		$output['view'] = 'index';
		$output['blog_rooms'] = $this->model->getBlogRooms($this->data);
		$output['blogs'] = $this->model->getBlogs($this->data);
		
		if(posted()){
			if(isset($_POST['update-categories'])){
				return $this->updateCategories($output);
			}
		}
		
		return $output;
	}
	
	protected function updateCategories($output)
	{
		$getPost = $this->model->get('blog_posts', $_POST['update-categories']);
		if($getPost){
			$catList = array();
			$rejectList = array();
			foreach($_POST as $k => $v){
				$exp = explode('_', $k);
				if(isset($exp[1]) AND isset($exp[2]) AND $exp[0] == 'category'){
					if($exp[1] == $getPost['postId']){
						$getCat = $this->model->fetchSingle('SELECT c.*, pc.approved, pc.postId
															 FROM blog_postCategories pc
															 LEFT JOIN blog_categories c ON c.categoryId = pc.categoryId
															 WHERE pc.postId = :postId AND pc.categoryId = :categoryId
															 GROUP BY pc.categoryId',
															 array(':postId' => $getPost['postId'],
																   ':categoryId' => $exp[2]));
						
						if($getCat){
							switch($v){
								case 'pending':
									$catList[] = array('cat' => $getCat, 'approved' => 0);
									break;
								case 'approve':
									$catList[] = array('cat' => $getCat, 'approved' => 1);
									break;
								case 'reject':
									$rejectList[] = $getCat;
									break;
							}
						}
					}
				}
			}
			$success = 0;
			$numChange = count($catList) + count($rejectList);
			$notifyChangeList = array();
			foreach($catList as $cat){
				if($cat['cat']['approved'] != $cat['approved']){
					$statusText = 'Pending';
					if($cat['approved'] == 1){
						$statusText = 'Approved';
					}
					$notifyChangeList[] = $cat['cat']['name'].': '.$statusText;
				}
				$update = $this->model->sendQuery('UPDATE blog_postCategories SET approved = :approved
												   WHERE postId = :postId AND categoryId = :categoryId',
												   array(':postId' => $getPost['postId'], ':categoryId' => $cat['cat']['categoryId'],
														 ':approved' => $cat['approved']));
				if($update){
					$success++;
				}
			}
			
			foreach($rejectList as $reject){
				
				$notifyChangeList[] = $reject['name'].': Rejected';
				$delete = $this->model->sendQuery('DELETE FROM blog_postCategories
												   WHERE postId = :postId AND categoryId = :categoryId',
												    array(':postId' => $getPost['postId'], ':categoryId' => $reject['categoryId']));
												    
				//send contributors a notification
				
				if($delete){
					$success++;
				}
			}
			
			if(count($notifyChangeList) > 0){
				$notifyData = array();
				$notifyData['user'] = $this->data['user'];
				$notifyData['post'] = $getPost;
				$notifyData['cat_results'] = $notifyChangeList;
				$this->submitModel->notifyContributors($getPost['postId'], 'category_decision', $notifyData, $this->data['user']['userId']);
			}
			
			if($success < $numChange){
				Slick_Util_Session::flash('blog-message', 'Failed updating '.($numChange - $success).'/'.$numChange.' categories for post "'.$getPost['title'].'"', 'error');
			}
			else{
				Slick_Util_Session::flash('blog-message', 'Categories for post "'.$getPost['title'].'" updated!', 'success');
			}
			$this->redirect($this->site.'/'.$this->data['app']['url'].'/'.$this->data['module']['url']);	
			die();			
		}
		

	}
	
}
