<?php
namespace App\API\V1;
class Pages_Controller extends \Core\Controller
{
	public $methods = array('GET');
	
	function __construct()
	{
		parent::__construct();
		$this->model = new \App\Page\View_Model;
		$this->tca = new \App\Tokenly\TCA_Model;
		$this->pageModule = $this->model->get('modules', 'page-view', array(), 'slug');
	}

	protected function init($args = array())
	{
		$output = array();
		$this->args = $args;
		try{
			$this->user = Auth_Model::getUser($this->args['data']);
		}
		catch(\Exception $e){
			$this->user = false;
		}				
				
		if(isset($args[1])){
			switch($args[1]){
				case 'menus':
					$output = $this->container->listMenus();
					break;
				default:
					$output = $this->container->getPage();
					break;
			}
		}
		else{
			$output = $this->container->listPages();
		}
		
		return $output;
	}
	
	protected function listPages()
	{
		$output = array();
		$getPages = $this->model->getAll('pages', array('siteId' => $this->args['data']['site']['siteId'], 'active' => 1), 
												  array('pageId', 'name', 'url', 'template', 'description'));
												 
		foreach($getPages as $k => $page){
			$checkTCA = $this->tca->checkItemAccess($this->user, $this->pageModule['moduleId'], $page['pageId'], 'page');
			if(!$checkTCA){
				unset($getPages[$k]);
				continue;
			}
		}
		
		$output['pages'] = $getPages;
		return $output;
	}
	
	protected function getPage()
	{
		$output = array();
		if(!isset($this->args[1]) OR trim($this->args[1]) == ''){
			http_response_code(400);
			$output['error'] = 'No page ID or URL specified';
			return $output;
		}
		
		$getPage = $this->model->get('pages', $this->args[1], array('pageId', 'siteId'), 'url');
		if(!$getPage){
			$getPage = $this->model->get('pages', $this->args[1], array('pageId', 'siteId'));
			if(!$getPage){
				http_response_code(404);
				$output['error'] = 'Page not found';
				return $output;
			}
		}
		
		$checkTCA = $this->tca->checkItemAccess($this->user, $this->pageModule['moduleId'], $getPage['pageId'], 'page');
	
		if(!$checkTCA OR $getPage['siteId'] != $this->args['data']['site']['siteId']){
			http_response_code(404);
			$output['error'] = 'Page not found';
			return $output;
		}
		
		$output['page'] = $this->model->getPageData($getPage['pageId']);
		$output['page']['pageId'] = $getPage['pageId'];
		
		return $output;
	}

	protected function getMenu()
	{
		$output = array();
		if(!isset($this->args[2]) OR trim($this->args[2]) == ''){
			http_response_code(400);
			$output['error'] = 'No menu ID or slug specified';
			return $output;
		}
		
		$getMenu = $this->model->get('menus', $this->args[2], array('menuId', 'name', 'slug', 'siteId'), 'slug');
		if(!$getMenu){
			$getMenu = $this->model->get('menus', $this->args[2], array('menuId', 'name', 'slug', 'siteId'));
			if(!$getMenu){
				http_response_code(404);
				$output['error'] = 'Menu not found';
				return $output;
			}
		}
		if($getMenu['siteId'] != $this->args['data']['site']['siteId']){
			http_response_code(404);
			$output['error'] = 'Menu not found';
			return $output;
		}
		
		unset($getMenu['siteId']);
		$output = $getMenu;
		$output['items'] = \App\View::getMenu($getMenu['menuId'], 0, 0, 1);
		
		return $output;
	}

	protected function listMenus()
	{
		if(isset($this->args[2])){
			return $this->container->getMenu();
		}
		$output = array();
		$getMenus = $this->model->getAll('menus', array('siteId' => $this->args['data']['site']['siteId']), 
												  array('menuId', 'name', 'slug'));
		foreach($getMenus as $key => $row){
			$getMenus[$key]['items'] = \App\View::getMenu($row['menuId'], 0, 0, 1);
		}
		$output['menus'] = $getMenus;
		return $output;
	}
}
