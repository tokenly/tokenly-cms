<?php
namespace App\CMS;
/*
 * @module-type = dashboard
 * @menu-label = Menu Items
 * 
 * */
class MenuItems_Controller extends \App\ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        $this->model = new MenuItems_Model;
    }
    
    public function init()
    {
		$output = parent::init();
        
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'add-page':
					$output = $this->addMenuPage();
					break;
				case 'edit-page':
					$output = $this->editMenuPage();
					break;
				case 'delete-page':
					$output = $this->deleteMenuPage();
					break;
				case 'add-link':
					$output = $this->addMenuLink();
					break;
				case 'edit-link':
					$output = $this->editMenuLink();
					break;
				case 'delete-link':
					$output = $this->deleteMenuLink();
					break;
				default:
					$output = $this->showMenuItems();
					break;
			}
			
		}
		else{
			$output = $this->showMenuItems();
		}

		$output['template'] = 'admin';
		
        return $output;
    }
    
    private function showMenuItems()
    {
		$output = array();
		$output['view'] = 'list';
		$getMenus = $this->model->getAll('menus', array('siteId' => $this->data['site']['siteId']));
		foreach($getMenus as $key => $menu){
			$items = \App\View::getMenu($menu['menuId']);
			$getSite = $this->model->get('sites', $menu['siteId']);
			
			foreach($items as $iKey => $item){	
				$items[$iKey]['url'] = str_replace($getSite['url'], '', $item['url']);
			}
			$getMenus[$key]['items'] = $items;
		}
		$output['menus'] = $getMenus;
		return $output;
	}
	
	private function addMenuPage()
	{
		$output = array('view' => 'pageForm');
		$output['form'] = $this->model->getMenuPageForm($this->data['site']['siteId']);
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->addMenuPage($data);
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
	
	private function editMenuPage()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getPage = $this->model->get('menu_pages', $this->args[3]);
		$getPage['parentId'] = $getPage['menuId'].'-'.$getPage['parentId'].'-'.$getPage['parentLink'];
		if(!$getPage){
			redirect($this->site.$this->moduleUrl);
		}
		
		$output = array('view' => 'pageForm');
		$output['form'] = $this->model->getMenuPageForm($this->data['site']['siteId'], $this->args[3]);
		$output['formType'] = 'Edit';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$edit = $this->model->editMenuPage($this->args[3], $data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$edit = false;
			}
			
			if($edit){
				redirect($this->site.$this->moduleUrl);
			}
		}
		$output['form']->setValues($getPage);
		return $output;
	}
	
	private function deleteMenuPage()
	{
		if(isset($this->args[3])){

		}
		
		$getPage = $this->model->get('menu_pages', $this->args[3]);
		if($getPage){

		}
		
		$delete = $this->model->delete('menu_pages', $this->args[3]);
		redirect($this->site.$this->moduleUrl);
	}
	
	private function addMenuLink()
	{
		$output = array('view' => 'linkForm');
		$output['form'] = $this->model->getMenuLinkForm($this->data['site']['siteId']);
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->addMenuLink($data);
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
	
	private function editMenuLink()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getLink = $this->model->get('menu_links', $this->args[3]);
		$getLink['parentId'] = $getLink['menuId'].'-'.$getLink['parentId'].'-'.$getLink['parentLink'];
		if(!$getLink){
			redirect($this->site.$this->moduleUrl);
		}
		
		$output = array('view' => 'linkForm');
		$output['form'] = $this->model->getMenuLinkForm($this->data['site']['siteId'], $this->args[3]);
		$output['formType'] = 'Edit';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$edit = $this->model->editMenuLink($this->args[3], $data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$edit = false;
			}
			
			if($edit){
				redirect($this->site.$this->moduleUrl);
			}
		}
		$output['form']->setValues($getLink);
		return $output;
	}
	
	private function deleteMenuLink()
	{
		if(isset($this->args[3])){
			$getLink = $this->model->get('menu_links', $this->args[3]);
			if($getLink){
				$delete = $this->model->delete('menu_links', $this->args[3]);
			}
		}
		redirect($this->site.$this->moduleUrl);
	}
}
