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
    
    protected function init()
    {
		$output = parent::init();
        
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->container->showContentBlocks();
					break;
				case 'add':
					$output = $this->container->addBlock();
					break;
				case 'edit':
					$output = $this->container->editBlock();
					break;
				case 'delete':
					$output = $this->container->deleteBlock();
					break;
				default:
					$output = $this->container->showContentBlocks();
					break;
			}
		}
		else{
			$output = $this->container->showContentBlocks();
		}
		$output['template'] = 'admin';
        
        return $output;
    }
    
    protected function showContentBlocks()
    {
		$output = array('view' => 'list');
		$getContentBlocks = $this->model->getAll('content_blocks', array('siteId' => $this->data['site']['siteId']));
		$output['blockList'] = $getContentBlocks;

		return $output;
	}
	
	
	protected function addBlock()
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
	

	
	protected function editBlock()
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
	
	protected function deleteBlock()
	{
		if(isset($this->args[3])){
			$delete = $this->model->deleteBlock($this->args[3]);
		}
		redirect($this->site.$this->moduleUrl);
	}
}
