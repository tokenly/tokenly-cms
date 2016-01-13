<?php
namespace App\Blog;
/*
 * @module-type = dashboard
 * @menu-label = Newsroom
 * 
 * */
use Util, Core;
class Newsroom_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Newsroom_Model;
		$this->submitModel = new Submissions_Model;
		$this->postModel = new Post_Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		$this->data['perms'] = \App\Meta_Model::getUserAppPerms($this->data['user']['userId'], 'blog');
        if(isset($this->args[2])){
			$output = $this->container->showBlogNewsroom($output);
		}
		else{
			$output = $this->container->showNewsroom($output);
		}
		$output['template'] = 'admin';
        $output['perms'] = $this->data['perms'];	
        return $output;
	}
	
	protected function showBlogNewsroom($output)
	{
		$output['view'] = 'newsroom';
		
		$getBlog = $this->model->get('blogs', strtolower($this->args[2]), array(), 'slug');
		if(!$getBlog){
			Util\Session::flash('blog-message', 'Invalid blog', 'error');
			redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url']);	
		}
		
		$max_load = 25; //default number of posts to load
		if(isset($_GET['load'])){
			if($_GET['load'] == 'all'){
				$max_load = false; //load everything
			}
			else{
				$new_load = intval($_GET['load']);
				if($new_load > 0){
					$max_load = $new_load;
				}
			}
		}
		$output['max_load'] = $max_load;
		
		$output['blog_rooms'] = $this->model->getBlogRooms($this->data, $getBlog['blogId'], $max_load);
		$output['blogs'] = $this->model->getBlogs($this->data);
		$output['blog'] = false;
		foreach($output['blogs'] as $blog){
			if($blog['slug'] == strtolower($this->args[2])){
				$output['blog'] = $blog;
				break;
			}
		}
		$output['blog_room'] = false;
		if($output['blog'] AND isset($output['blog_rooms'][$output['blog']['blogId']])){
			$output['blog_room'] = $output['blog_rooms'][$output['blog']['blogId']];
		}
		if(!$output['blog']){
			Util\Session::flash('blog-message', 'Invalid blog', 'error');
			redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url']);			
		}
		if(posted()){
			if(isset($_POST['update-categories'])){
				return $this->container->updateCategories($output);
			}
		}
		
		return $output;
	}
	
	protected function showNewsroom($output)
	{
		$output['view'] = 'index';
		$output['blogs'] = $this->model->getBlogs($this->data);
		
		if(count($output['blogs']) == 1){
			$output['blogs'] = array_values($output['blogs']);
			redirect($this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/'.$output['blogs'][0]['slug']);
		}
	
		return $output;
	}
	
	protected function updateCategories($output)
	{
		$getPost = $this->model->get('blog_posts', $_POST['update-categories']);
		if($getPost){
			//checks if this user is allowed to update categories for this post
			$checkAccess = $this->submitModel->checkPostBlogRole($getPost['postId'], $this->data['user']['userId']);
			if(!$checkAccess AND !$this->data['perms']['canManageAllBlogs']){
				$output['view'] = '403';
				return $output;
			}
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
				Util\Session::flash('blog-message', 'Failed updating '.($numChange - $success).'/'.$numChange.' categories for post "'.$getPost['title'].'"', 'error');
			}
			else{
				Util\Session::flash('blog-message', 'Categories for post "'.$getPost['title'].'" updated!', 'success');
			}
			
			$andBlog = '';
			if(isset($_GET['blog']) AND trim($_GET['blog']) != ''){
				$andBlog = '/'.$_GET['blog'];
			}
			
			redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url'].$andBlog);			
		}
	}
}
