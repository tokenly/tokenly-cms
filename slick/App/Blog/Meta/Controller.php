<?php
/*
 * @module-type = dashboard
 * @menu-label = Custom Post Fields
 * 
 * */
class Slick_App_Blog_Meta_Controller extends Slick_App_ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        
        $this->model = new Slick_App_Blog_Meta_Model;
        
        
    }
    
    public function init()
    {
		$output = parent::init();
        
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->showBlogMeta();
					break;
				case 'add':
					$output = $this->addField();
					break;
				case 'edit':
					$output = $this->editField();
					break;
				case 'delete':
					$output = $this->deleteField();
					break;
				default:
					$output = $this->showBlogMeta();
					break;
			}
		}
		else{
			$output = $this->showBlogMeta();
		}
		$output['template'] = 'admin';
        
        return $output;
    }
    
    private function showBlogMeta()
    {
		$output = array('view' => 'list');
		$getBlogMeta = $this->model->getAll('blog_postMetaTypes', array('siteId' => $this->data['site']['siteId']), array(), 'rank', 'asc');
		$output['fieldList'] = $getBlogMeta;

		
		return $output;
		
	}
	
	
	private function addField()
	{
		$output = array('view' => 'form');
		$output['form'] = $this->model->getFieldForm();
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->addField($data);
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
		
		return $output;
		
	}
	

	
	private function editField()
	{
		if(!isset($this->args[3])){
			$this->redirect('/');
			return false;
		}
		
		$getField = $this->model->get('blog_postMetaTypes', $this->args[3]);
		if(!$getField){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}

		$output = array('view' => 'form');
		$output['form'] = $this->model->getFieldForm($this->args[3]);
		$output['formType'] = 'Edit';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->editField($this->args[3], $data);
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
		$output['form']->setValues($getField);
		
		return $output;
		
	}
	

	
	
	private function deleteField()
	{
		if(!isset($this->args[3])){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		
		$getField = $this->model->get('blog_postMetaTypes', $this->args[3]);
		if(!$getField OR $getField['siteId'] != $this->data['site']['siteId']){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$delete = $this->model->delete('blog_postMetaTypes', $this->args[3]);
		$this->redirect($this->site.'/'.$this->moduleUrl);
		return true;
	}
	


}

?>
