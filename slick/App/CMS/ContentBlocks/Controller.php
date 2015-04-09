<?php
/*
 * @module-type = dashboard
 * @menu-label = Content Blocks
 * 
 * */
class Slick_App_CMS_ContentBlocks_Controller extends Slick_App_ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        
        $this->model = new Slick_App_CMS_ContentBlocks_Model;
        
        
    }
    
    public function init()
    {
		$output = parent::init();
        
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->showContentBlocks();
					break;
				case 'add':
					$output = $this->addBlock();
					break;
				case 'edit':
					$output = $this->editBlock();
					break;
				case 'delete':
					$output = $this->deleteBlock();
					break;
				default:
					$output = $this->showContentBlocks();
					break;
			}
		}
		else{
			$output = $this->showContentBlocks();
		}
		$output['template'] = 'admin';
        
        return $output;
    }
    
    private function showContentBlocks()
    {
		$output = array('view' => 'list');
		$getContentBlocks = $this->model->getAll('content_blocks', array('siteId' => $this->data['site']['siteId']));
		$output['blockList'] = $getContentBlocks;

		
		return $output;
		
	}
	
	
	private function addBlock()
	{
		$output = array('view' => 'form');
		$output['form'] = $this->model->getBlockForm();
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->addBlock($data);
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
	

	
	private function editBlock()
	{
		if(!isset($this->args[3])){
			$this->redirect('/');
			return false;
		}
		
		$getBlock = $this->model->get('content_blocks', $this->args[3]);
		if(!$getBlock){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$output = array('view' => 'form');
		$output['form'] = $this->model->getBlockForm($this->args[3]);
		$output['formType'] = 'Edit';
		$output['thisBlock'] = $getBlock;
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->editBlock($this->args[3], $data);
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
		$output['form']->setValues($getBlock);
		
		return $output;
		
	}
	

	
	
	private function deleteBlock()
	{
		if(!isset($this->args[3])){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		
		$getBlock = $this->model->get('content_blocks', $this->args[3]);
		if(!$getBlock){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$delete = $this->model->delete('content_blocks', $this->args[3]);
		$this->redirect($this->site.'/'.$this->moduleUrl);
		return true;
	}
	


}

?>
