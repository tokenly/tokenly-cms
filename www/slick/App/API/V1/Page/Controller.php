<?php
class Slick_App_API_V1_Page_Controller extends Slick_Core_Controller
{
	public $methods = array('GET');
	
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_Page_View_Model;
	}

	public function init($args = array())
	{
		$output = array();
		$this->args = $args;
		if(isset($args[1])){
			switch($args[1]){
				case 'list':
					$output = $this->listPages();
					break;
				case 'get':
					$output = $this->getPage();
					break;
				case 'menu':
					$output = $this->getMenu();
					break;
				case 'menu-list':
					$output = $this->listMenus();
					break;
				default:
					http_response_code(400);
					$output['error'] = 'Invalid endpoint';
					break;
			}
		}
		else{
			http_response_code(400);
			$output['error'] = 'Invalid endpoint';
		}
		
		return $output;
	}
	
	private function listPages()
	{
		$output = array();
		$getPages = $this->model->getAll('pages', array('siteId' => $this->args['data']['site']['siteId'], 'active' => 1), 
												  array('pageId', 'name', 'url', 'template', 'description'));
		$output['pages'] = $getPages;
		return $output;
	}
	
	private function getPage()
	{
		$output = array();
		if(!isset($this->args[2]) OR trim($this->args[2]) == ''){
			http_response_code(400);
			$output['error'] = 'No page ID or URL specified';
			return $output;
		}
		
		$getPage = $this->model->get('pages', $this->args[2], array('pageId', 'siteId'), 'url');
		if(!$getPage){
			$getPage = $this->model->get('pages', $this->args[2], array('pageId'));
			if(!$getPage){
				http_response_code(404);
				$output['error'] = 'Page not found';
				return $output;
			}
		}
		if($getPage['siteId'] != $this->args['data']['site']['siteId']){
			http_response_code(404);
			$output['error'] = 'Page not found';
			return $output;
		}
		
		$output['page'] = $this->model->getPageData($getPage['pageId']);
		$output['page']['pageId'] = $getPage['pageId'];
		
		return $output;
	}

	private function getMenu()
	{
		$output = array();
		if(!isset($this->args[2]) OR trim($this->args[2]) == ''){
			http_response_code(400);
			$output['error'] = 'No menu ID or slug specified';
			return $output;
		}
		
		$getMenu = $this->model->get('menus', $this->args[2], array('menuId', 'siteId'), 'slug');
		if(!$getMenu){
			$getMenu = $this->model->get('menus', $this->args[2], array('menuId', 'siteId'));
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

		$output['menu'] = Slick_App_View::getMenu($getMenu['menuId'], 0, 0, 1);
		
		return $output;
	}

	private function listMenus()
	{
		$output = array();
		$getMenus = $this->model->getAll('menus', array('siteId' => $this->args['data']['site']['siteId']), 
												  array('menuId', 'name', 'slug'));
		$output['menus'] = $getMenus;
		return $output;
	}

}

?>
