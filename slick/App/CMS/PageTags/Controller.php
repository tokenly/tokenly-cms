<?php
/*
 * @module-type = dashboard
 * @menu-label = Page Tags
 * 
 * */
class Slick_App_CMS_PageTags_Controller extends Slick_App_ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        
        $this->model = new Slick_App_CMS_PageTags_Model;
        
        
    }
    
    public function init()
    {
		$output = parent::init();
        
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->showPageTags();
					break;
				case 'add':
					$output = $this->addTag();
					break;
				case 'edit':
					$output = $this->editTag();
					break;
				case 'delete':
					$output = $this->deleteTag();
					break;
				default:
					$output = $this->showPageTags();
					break;
			}
		}
		else{
			$output = $this->showPageTags();
		}
		$output['template'] = 'admin';
        
        return $output;
    }
    
    private function showPageTags()
    {
		$output = array('view' => 'list');
		$getPageTags = $this->model->getAll('page_tags');
		$output['tagList'] = $getPageTags;

		
		return $output;
		
	}
	
	
	private function addTag()
	{
		$output = array('view' => 'form');
		$output['form'] = $this->model->getTagForm();
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->addTag($data);
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
	

	
	private function editTag()
	{
		if(!isset($this->args[3])){
			$this->redirect('/');
			return false;
		}
		
		$getTag = $this->model->get('page_tags', $this->args[3]);
		if(!$getTag){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$output = array('view' => 'form');
		$output['form'] = $this->model->getTagForm($this->args[3]);
		$output['formType'] = 'Edit';
		
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->editTag($this->args[3], $data);
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
		$output['form']->setValues($getTag);
		
		return $output;
		
	}
	

	
	
	private function deleteTag()
	{
		if(!isset($this->args[3])){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		
		$getTag = $this->model->get('page_tags', $this->args[3]);
		if(!$getTag){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$delete = $this->model->delete('page_tags', $this->args[3]);
		$this->redirect($this->site.'/'.$this->moduleUrl);
		return true;
	}
	


}

?>
