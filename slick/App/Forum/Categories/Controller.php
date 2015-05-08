<?php
namespace App\Forum;
/*
 * @module-type = dashboard
 * @menu-label = Manage Categories
 * 
 * */
class Categories_Controller extends \App\ModControl
{
    function __construct()
    {
        parent::__construct();
        $this->model = new Categories_Model;
    }
    
    public function init()
    {
		$output = parent::init();
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->showCategories();
					break;
				case 'add':
					$output = $this->addCategory();
					break;
				case 'edit':
					$output = $this->editCategory();
					break;
				case 'delete':
					$output = $this->deleteCategory();
					break;
				default:
					$output = $this->showCategories();
					break;
			}
		}
		else{
			$output = $this->showCategories();
		}
		$output['template'] = 'admin';
        return $output;
    }
    
    private function showCategories()
    {
		$output = array('view' => 'list');
		$output['categoryList'] = $this->model->getAll('forum_categories', array('siteId' => $this->data['site']['siteId']), array(), 'rank', 'asc');
		return $output;
	}
	
	
	private function addCategory()
	{
		$output = array('view' => 'form');
		$output['form'] = $this->model->getCategoryForm();
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->addCategory($data);
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
	
	private function editCategory()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getCategory = $this->model->get('forum_categories', $this->args[3]);
		if(!$getCategory){
			redirect($this->site.$this->moduleUrl);
		}
		
		$output = array('view' => 'form');
		$output['form'] = $this->model->getCategoryForm($this->args[3]);
		$output['formType'] = 'Edit';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->editCategory($this->args[3], $data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->site.$this->moduleUrl);
			}
		}
		$output['form']->setValues($getCategory);
		return $output;
	}

	private function deleteCategory()
	{
		if(isset($this->args[3])){
			$getCategory = $this->model->get('forum_categories', $this->args[3]);
			if($getCategory){
				$delete = $this->model->delete('forum_categories', $this->args[3]);
			}			
		}
		redirect($this->site.$this->moduleUrl);
	}
}
