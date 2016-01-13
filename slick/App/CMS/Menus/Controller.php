<?php
namespace App\CMS;
/*
 * @module-type = dashboard
 * @menu-label = Menus
 * 
 * */
class Menus_Controller extends \App\ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        
        $this->model = new Menus_Model;
    }
    
    protected function init()
    {
		$output = parent::init();
        
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->container->showMenus();
					break;
				case 'add':
					$output = $this->container->addMenu();
					break;
				case 'edit':
					$output = $this->container->editMenu();
					break;
				case 'delete':
					$output = $this->container->deleteMenu();
					break;
				default:
					$output = $this->container->showMenus();
					break;
			}
		}
		else{
			$output = $this->container->showMenus();
		}
		$output['template'] = 'admin';
        
        return $output;
    }
    
    protected function showMenus()
    {
		$output = array('view' => 'list');
		$output['menuList'] = $this->model->getAll('menus', array('siteId' => $this->data['site']['siteId']));;

		return $output;
	}
	
	
	protected function addMenu()
	{
		$output = array('view' => 'form');
		$output['form'] = $this->model->getMenuForm();
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->addMenu($data);
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
	

	protected function editMenu()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getMenu = $this->model->get('menus', $this->args[3]);
		if(!$getMenu){
			redirect($this->site.$this->moduleUrl);
		}
		
		$output = array('view' => 'form');
		$output['form'] = $this->model->getMenuForm($this->args[3]);
		$output['formType'] = 'Edit';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->editMenu($this->args[3], $data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->site.$this->moduleUrl);
			}
			
		}
		$output['form']->setValues($getMenu);
		
		return $output;
	}
	
	protected function deleteMenu()
	{
		if(isset($this->args[3])){
			$getMenu = $this->model->get('menus', $this->args[3]);
			if($getMenu){
				$delete = $this->model->delete('menus', $this->args[3]);
			}			
		}
		redirect($this->site.$this->moduleUrl);
	}
}
