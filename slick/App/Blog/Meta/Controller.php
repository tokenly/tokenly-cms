<?php
namespace App\Blog;
/*
 * @module-type = dashboard
 * @menu-label = Custom Post Fields
 * 
 * */
class Meta_Controller extends \App\ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        
        $this->model = new Meta_Model;
    }
    
    protected function init()
    {
		$output = parent::init();
        
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->container->showBlogMeta();
					break;
				case 'add':
					$output = $this->container->addField();
					break;
				case 'edit':
					$output = $this->container->editField();
					break;
				case 'delete':
					$output = $this->container->deleteField();
					break;
				default:
					$output = $this->container->showBlogMeta();
					break;
			}
		}
		else{
			$output = $this->container->showBlogMeta();
		}
		$output['template'] = 'admin';
        
        return $output;
    }
    
    protected function showBlogMeta()
    {
		$output = array('view' => 'list');
		$getBlogMeta = $this->model->getAll('blog_postMetaTypes', array('siteId' => $this->data['site']['siteId']), array(), 'rank', 'asc');
		$output['fieldList'] = $getBlogMeta;

		return $output;
		
	}
	
	
	protected function addField()
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
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			if($add){
				redirect($this->site.$this->moduleUrl);
			}
		}
		return $output;
	}
	
	protected function editField()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getField = $this->model->get('blog_postMetaTypes', $this->args[3]);
		if(!$getField){
			redirect($this->site.$this->moduleUrl);
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
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->site.$this->moduleUrl);
			}
		}
		$output['form']->setValues($getField);
		
		return $output;
	}
	

	protected function deleteField()
	{
		if(isset($this->args[3])){
			$getField = $this->model->get('blog_postMetaTypes', $this->args[3]);
			if($getField AND $getField['siteId'] == $this->data['site']['siteId']){
				$delete = $this->model->delete('blog_postMetaTypes', $this->args[3]);
			}
		}
		redirect($this->site.$this->moduleUrl);
	}
}
