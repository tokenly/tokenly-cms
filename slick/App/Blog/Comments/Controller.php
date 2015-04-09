<?php
/*
 * @module-type = dashboard
 * @menu-label = Blog Comments
 * 
 * */
class Slick_App_Blog_Comments_Controller extends Slick_App_ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        
        $this->model = new Slick_App_Blog_Comments_Model;
        
        
    }
    
    public function init()
    {
		$output = parent::init();
        
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'delete':
					$output = $this->deleteBlogComment();
					break;
				default:
					$output = $this->showComments();
					break;
			}
		}
		else{
			$output = $this->showComments();
		}
		$output['template'] = 'admin';
        
        return $output;
    }
    
    private function showComments()
    {
		$output = array('view' => 'list');
		$output['commentList'] = $this->model->getCommentList($this->data['site']['siteId']);
		
		return $output;
		
	}
	
	private function deleteBlogComment()
	{
		if(!isset($this->args[3])){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		
		$getBlogComments = $this->model->get('blog_comments', $this->args[3]);
		if(!$getBlogComments){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$delete = $this->model->delete('blog_comments', $this->args[3]);
		$this->redirect($this->site.'/'.$this->moduleUrl);
		return true;
	}
	


}

?>
