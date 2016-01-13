<?php
namespace App\Store;
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
    
    protected function init()
    {
		$output = parent::init();
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->container->showStoreCategories();
					break;
				case 'add':
					$output = $this->container->addStoreCategory();
					break;
				case 'edit':
					$output = $this->container->editStoreCategory();
					break;
				case 'delete':
					$output = $this->container->deleteStoreCategory();
					break;
				default:
					$output = $this->container->showStoreCategories();
					break;
			}
		}
		else{
			$output = $this->container->showStoreCategories();
		}
		$output['template'] = 'admin';
        
        return $output;
    }
    
    protected function showStoreCategories()
    {
		$output = array('view' => 'list');
		$output['catList'] = $this->model->getCategories($this->data['site']['siteId']);

		return $output;
	}
	
	protected function addStoreCategory()
	{
		$output = array('view' => 'form');
		$output['form'] = $this->model->getStoreCategoryForm($this->data['site']['siteId']);
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->addStoreCategory($data);
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

	protected function editStoreCategory()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getStoreCategory = $this->model->get('store_categories', $this->args[3]);
		if(!$getStoreCategory){
			redirect($this->site.$this->moduleUrl);
		}
		
		$output = array('view' => 'form');
		$output['form'] = $this->model->getStoreCategoryForm($this->data['site']['siteId'], $this->args[3]);
		$output['formType'] = 'Edit';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->editStoreCategory($this->args[3], $data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->site.$this->moduleUrl);
			}
		}
		$output['form']->setValues($getStoreCategory);
		return $output;
	}

	protected function deleteStoreCategory()
	{
		if(isset($this->args[3])){
			$getStoreCategory = $this->model->get('store_categories', $this->args[3]);
			if($getStoreCategory){
				$delete = $this->model->delete('store_categories', $this->args[3]);
			}
		}
		redirect($this->site.$this->moduleUrl);
	}
}
