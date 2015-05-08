<?php
namespace App\CMS;
/*
 * @module-type = dashboard
 * @menu-label = Content Blocks
 * 
 * */
class ContentBlocks_Controller extends \App\ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        
        $this->model = new ContentBlocks_Model;
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
	

	
	private function editBlock()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getBlock = $this->model->get('content_blocks', $this->args[3]);
		if(!$getBlock){
			redirect($this->site.$this->moduleUrl);
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
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->site.$this->moduleUrl);
			}
		}
		$output['form']->setValues($getBlock);
		return $output;
	}
	
	private function deleteBlock()
	{
		if(isset($this->args[3])){
			$getBlock = $this->model->get('content_blocks', $this->args[3]);
			if($getBlock){
				$delete = $this->model->delete('content_blocks', $this->args[3]);
			}
		}
		redirect($this->site.$this->moduleUrl);
	}
}
