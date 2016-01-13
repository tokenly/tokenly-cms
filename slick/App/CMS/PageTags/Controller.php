<?php
namespace App\CMS;
/*
 * @module-type = dashboard
 * @menu-label = Page Tags
 * 
 * */
class PageTags_Controller extends \App\ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        
        $this->model = new PageTags_Model;    
    }
    
    protected function init()
    {
		$output = parent::init();
        
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->container->showPageTags();
					break;
				case 'add':
					$output = $this->container->addTag();
					break;
				case 'edit':
					$output = $this->container->editTag();
					break;
				case 'delete':
					$output = $this->container->deleteTag();
					break;
				default:
					$output = $this->container->showPageTags();
					break;
			}
		}
		else{
			$output = $this->container->showPageTags();
		}
		$output['template'] = 'admin';
        
        return $output;
    }
    
    protected function showPageTags()
    {
		$output = array('view' => 'list');
		$getPageTags = $this->model->getAll('page_tags');
		$output['tagList'] = $getPageTags;

		return $output;
	}
	
	
	protected function addTag()
	{
		$output = array('view' => 'form');
		$output['form'] = $this->model->getTagForm();
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->addTag($data);
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
	
	
	protected function editTag()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getTag = $this->model->get('page_tags', $this->args[3]);
		if(!$getTag){
			redirect($this->site.$this->moduleUrl);
		}
		
		$output = array('view' => 'form');
		$output['form'] = $this->model->getTagForm($this->args[3]);
		$output['formType'] = 'Edit';
		
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->editTag($this->args[3], $data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->site.$this->moduleUrl);
			}
		}
		$output['form']->setValues($getTag);
		return $output;
	}
	
	protected function deleteTag()
	{
		if(isset($this->args[3])){
			$getTag = $this->model->get('page_tags', $this->args[3]);
			if($getTag){
				$delete = $this->model->delete('page_tags', $this->args[3]);
			}
		}
		redirect($this->site.$this->moduleUrl);
	}
}
