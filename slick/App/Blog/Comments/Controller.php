<?php
namespace App\Blog;
/*
 * @module-type = dashboard
 * @menu-label = Blog Comments
 * 
 * */
class Comments_Controller extends \App\ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        $this->model = new Comments_Model;
    }
    
    protected function init()
    {
		$output = parent::init();
        
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'delete':
					$output = $this->container->deleteBlogComment();
					break;
				default:
					$output = $this->container->showComments();
					break;
			}
		}
		else{
			$output = $this->container->showComments();
		}
		$output['template'] = 'admin';
        
        return $output;
    }
    
    protected function showComments()
    {
		$output = array('view' => 'list');
		$output['commentList'] = $this->model->getCommentList($this->data['site']['siteId']);
		return $output;
	}
	
	protected function deleteBlogComment()
	{
		if(isset($this->args[3])){
			$getBlogComments = $this->model->get('blog_comments', $this->args[3]);
			if($getBlogComments){
				$delete = $this->model->delete('blog_comments', $this->args[3]);
			}
		}
		redirect($this->site.$this->moduleUrl);
	}
}
