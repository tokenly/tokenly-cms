<?php
/*
 * @module-type = dashboard
 * @menu-label = Manage Categories
 * 
 * */
class Slick_App_Store_Categories_Controller extends Slick_App_ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        
        $this->model = new Slick_App_Store_Categories_Model;
        
        
    }
    
    public function init()
    {
		$output = parent::init();
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->showStoreCategories();
					break;
				case 'add':
					$output = $this->addStoreCategory();
					break;
				case 'edit':
					$output = $this->editStoreCategory();
					break;
				case 'delete':
					$output = $this->deleteStoreCategory();
					break;
				default:
					$output = $this->showStoreCategories();
					break;
			}
		}
		else{
			$output = $this->showStoreCategories();
		}
		$output['template'] = 'admin';
        
        return $output;
    }
    
    private function showStoreCategories()
    {
		$output = array('view' => 'list');
		$output['catList'] = $this->model->getCategories($this->data['site']['siteId']);

		
		return $output;
		
	}
	
	
	private function addStoreCategory()
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
	

	
	private function editStoreCategory()
	{
		if(!isset($this->args[3])){
			$this->redirect('/');
			return false;
		}
		
		$getStoreCategory = $this->model->get('store_categories', $this->args[3]);
		if(!$getStoreCategory){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
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
			catch(Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				$this->redirect($this->site.'/'.$this->moduleUrl);
				return true;
			}
			
		}
		$output['form']->setValues($getStoreCategory);
		
		return $output;
		
	}
	

	
	
	private function deleteStoreCategory()
	{
		if(!isset($this->args[3])){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		
		$getStoreCategory = $this->model->get('store_categories', $this->args[3]);
		if(!$getStoreCategory){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$delete = $this->model->delete('store_categories', $this->args[3]);
		$this->redirect($this->site.'/'.$this->moduleUrl);
		return true;
	}
	


}

?>
