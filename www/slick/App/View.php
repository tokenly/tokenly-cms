<?php
class Slick_App_View extends Slick_Core_View
{
	public static $menuData = array();
	
    public function load($data)
    {
		$req = array('theme', 'template', 'view', 'site');
		foreach($req as $required){
			if(!isset($data[$required])){
				die(ucfirst($required).' not set');
			}
		}
		
		if($data['view'] == '404'){
			$data['template'] = 'default';
			http_response_code(404);
		}
		if($data['view'] == '403'){
			$data['template'] = 'default';
			http_response_code(403);
		}
		
		$this->data = $data;
        $templatePath = SITE_PATH.'/themes/'.$data['theme'].'/templates/'.$data['template'].'.php';
        if(!file_exists($templatePath)){
			$data['template'] = 'default';
			$templatePath = SITE_PATH.'/themes/'.$data['theme'].'/templates/'.$data['template'].'.php';
			if(!file_exists($templatePath)){
				die('Template not found: '.$data['template']);
			}
		}
        $path = '/views/';
        if($data['view'] != '404' AND $data['view'] != '403'){
			if(isset($data['app'])){
				if($data['module']){
					$path .= $data['app']['location'].'/'.str_replace('_', '/', $data['module']['location']).'/';
				}
				else{
					$path .= $data['app']['location'].'/';
				}
			}
		}
		elseif($data['view'] == '403'){
			$data['template'] = 'default';
			$data['title'] = '403 Not Authorized';
		}
		else{
			$data['template'] = 'default';
			$data['title'] = '404 Page Not Found';
		}
		
		
		if(isset($data['force-view'])){
			$path = '';
			$data['view'] = '/views/'.$data['force-view'];
		}
        $viewPath = SITE_PATH.'/themes/'.$data['theme'].$path.$data['view'].'.php';
        if(!file_exists($viewPath)){
			$viewPath = SITE_PATH.'/themes/'.$path.$data['view'].'.php';
			if(!file_exists($viewPath)){
				die('View not found: '.$path.$data['view']);
			}
		}
		
		foreach($data as $key => $val){
			$$key = $val;
		}
		
		
		define('SITE_URL', $data['site']['url']);
		define('THEME_PATH', SITE_PATH.'/themes/'.$data['theme']);
		define('THEME_URL', SITE_URL.'/themes/'.$data['theme']);
		
		if(!isset($pageRequest['params'])){
			$pageRequest['params'] = '';
			
		}
	
		require_once($templatePath);
        
    }
    
    /**
     *  Accepts menuId or slug
     * 
     * */
    public static function getMenu($id, $parentId = 0, $parentLink = 0, $apiMode = 0)
    {
		$model = new Slick_Core_Model;
		$curSite = $model->get('sites', $_SERVER['HTTP_HOST'], array(), 'domain');
		$get = $model->fetchSingle('SELECT * FROM menus WHERE slug = :id AND siteId = :siteId', array(':id' => $id, ':siteId' => $curSite['siteId']));
		if(!$get){
			$get = $model->fetchSingle('SELECT * FROM menus WHERE menuId = :id AND siteId = :siteId', array(':id' => $id, ':siteId' => $curSite['siteId']));
		}
		
		if(!$get){
			return false;
		}
		
		$tca = new Slick_App_LTBcoin_TCA_Model;
		$accountModel = new Slick_App_Account_Home_Model;
		$pageModule = $tca->get('modules', 'page-view', array(), 'slug');
		$userId = 0;
		if(isset($_SESSION['accountAuth'])){
			$getUser = $accountModel->checkSession($_SESSION['accountAuth']);
			if($getUser){
				$userId = $getUser['userId'];
			}
		}
		$checkTCA = $tca->checkItemAccess($userId, $pageModule['moduleId'], $get['menuId'], 'menu');
		if(!$checkTCA){
			return false;
		}				
		
		if(!isset(self::$menuData[$get['menuId']])){
			self::$menuData[$get['menuId']] = array();
			self::$menuData[$get['menuId']]['pages'] = $model->fetchAll('SELECT m.*, p.url
																		  FROM menu_pages m
																		  LEFT JOIN pages p ON p.pageId = m.pageId
																		  WHERE m.menuId = :id AND p.active = 1',
																		 array(':id' => $get['menuId']));
			self::$menuData[$get['menuId']]['links'] = $model->getAll('menu_links', array('menuId' => $get['menuId']));																	 
		}
		
		$getSite = $curSite;
		$menuData = self::$menuData[$get['menuId']];
		
		$output = array();
		/*$getLinks = $model->getAll('menu_links', array('menuId' => $get['menuId'], 'parentId' => $parentId, 'parentLink' => $parentLink));
		$getPages = $model->fetchAll('SELECT m.*, p.url
									  FROM menu_pages m
									  LEFT JOIN pages p ON p.pageId = m.pageId
									  WHERE m.menuId = :id
									  AND parentId = :parentId
									  AND parentLink = :parentLink
									  AND p.active = 1', array(':id' => $get['menuId'], ':parentId' => $parentId, ':parentLink' => $parentLink));*/
		$getLinks = extract_row($menuData['links'], array('parentId' => $parentId, 'parentLink' => $parentLink), true);
		$getPages = extract_row($menuData['pages'], array('parentId' => $parentId, 'parentLink' => $parentLink), true);
		$items = array_merge($getPages, $getLinks);
		
		aasort($items, 'rank');

		foreach($items as $item){
			$addOutput = array('url' => $item['url'], 'label' => $item['label'], 'rank' => $item['rank'], 'target' => '');
			if(isset($item['linkId'])){
				$addOutput['isLink'] = true;
				$addOutput['itemId'] = $item['linkId'];
				if(substr($item['url'], 0, 1) == '/'){
					$addOutput['url'] = $getSite['url'].$item['url'];
				}
				else{
					if(preg_match('/^http\:\/\//', $item['url']) OR preg_match('/^https\:\/\//', $item['url'])){
						$addOutput['target'] = '_blank';
					}
				}
				$tcaSlug = 'menu-link';
			}
			else{
				$addOutput['isLink'] = false;
				$addOutput['itemId'] = $item['menuPageId'];
				$addOutput['url'] = $getSite['url'].'/'.$item['url'];
				$tcaSlug = 'menu-page';
			}
			
			$checkTCA = $tca->checkItemAccess($userId, $pageModule['moduleId'], $addOutput['itemId'], $tcaSlug);
			if(!$checkTCA){
				continue;
			}						
			
			$isLink = 0;
			if($addOutput['isLink']){
				$isLink = 1;
			}
			$getChildren = Slick_App_View::getMenu($id, $addOutput['itemId'], $isLink, $apiMode);
			if(count($getChildren)){
				$addOutput['children'] = $getChildren;
			}
			
			if($addOutput['isLink'] == 1){
				$addOutput['actionUrl'] = 'link/'.$addOutput['itemId'];
			}
			else{
				$addOutput['actionUrl'] = 'page/'.$addOutput['itemId'];
			}
			
			if($apiMode == 1){
				unset($addOutput['actionUrl']);
				unset($addOutput['itemId']);
				unset($addOutput['isLink']);
			}

			$output[] = $addOutput;
			
		}
		
		return $output;
	}
	
	
	/**
	 *  Menu can be an ID or slug to grab from menu table, or can be array of custom 
	 * 
	 * */
	public function displayMenu($menu, $children = 1, $class = '', $urlParam = '')
	{
		if(!is_array($menu)){
			$getMenu = $this->getMenu($menu);
			if(!$getMenu){
				return false;
			}
			$menu = $getMenu;
		}
		

		if(count($menu) > 0){
			$output = '<ul class="'.$class.'">';
			foreach($menu as $item){
				$target = '';
				$url = $item['url'];
				if(isset($item['target']) AND $item['target'] != ''){
					$target = 'target="'.$item['target'].'"';
				}
				
				$itemClass = '';
				if($urlParam != ''){
					if($item['url'] == SITE_URL.'/'.$urlParam){
						$itemClass .= ' active';
					}
				}
				//debug($item['url']);
				
				if(isset($item['no_link']) AND $item['no_link']){
					$output .= '<li  class="'.$itemClass.'">'.$item['label'];
				}
				else{
					$output .= '<li  class="'.$itemClass.'"><a href="'.$item['url'].'" '.$target.'>'.$item['label'].'</a>';
				}
				
				
	
				if(isset($item['children']) AND $children == 1){
					
					$output .= $this->displayMenu($item['children'], 1);
				}	
				
				$output .= '</li>';

			}
		
			$output .= '</ul>';
			return $output;
		}
		return false;
		
	}
	
	public static function getBlock($id)
	{
		$model = new Slick_Core_Model;
		$curSite = $model->get('sites', $_SERVER['HTTP_HOST'], array(), 'domain');
		$get = $model->fetchSingle('SELECT * FROM content_blocks WHERE slug = :id AND siteId = :siteId',
									array(':siteId' => $curSite['siteId'], ':id' => $id));		
		if(!$get){
			$get = $model->fetchSingle('SELECT * FROM content_blocks WHERE blockId = :id AND siteId = :siteId',
										array(':siteId' => $curSite['siteId'], ':id' => $id));
		}
		if(!$get){
			return false;
		}
		
		if($get['formatType'] == 'markdown'){
			$get['content'] = markdown($get['content']);
		}
		
		return $get;
		
	}
	
	public static function displayBlock($id)
	{
		$block = Slick_App_View::getBlock($id);
		if(!$block OR $block['active'] == 0){
			return false;
		}
		
		$tca = new Slick_App_LTBcoin_TCA_Model;
		$pageModule = $tca->get('modules', 'page-view', array(), 'slug');
		$userId = 0;
		$accountModel = new Slick_App_Account_Home_Model;
		if(isset($_SESSION['accountAuth'])){
			$getUser = $accountModel->checkSession($_SESSION['accountAuth']);
			if($getUser){
				$userId = $getUser['userId'];
			}
		}
		$checkTCA = $tca->checkItemAccess($userId, $pageModule['moduleId'], $block['blockId'], 'content-block');
		if(!$checkTCA){
			return false;
		}
		
		//remove any self referencing content blocks..
		$block['content'] = str_replace('[BLOCK:'.$block['blockId'].']', '', $block['content']);
		$block['content'] = str_replace('[BLOCK:'.$block['slug'].']', '', $block['content']);
		
		$block['content'] = Slick_App_Page_View_Model::parseContentBlocks($block['content'], $block['siteId']);
		$block['content'] = Slick_App_Page_View_Model::parsePageTags($block['content']);
		
		return $block['content'];
		
	}
	
	public static function displayFlash($name, $type = true)
	{
		$getFlash = Slick_Util_Session::getFlash($name);
		if(!$getFlash){
			return false;
		}
		$class = '';
		if($type){
			$class = Slick_Util_Session::getFlash($name.'-type');
		}
		return '<p class="'.$class.'">'.$getFlash.'</p>';
	}
	
	public static function displayTag($tag, $params = array())
	{
		$model = new Slick_Core_Model;
		$getTag = $model->get('page_tags', $tag, array(), 'tag');
		if(!$getTag){
			return false;
		}
		$class = new $getTag['class']($params);
		$class->params = $params;
		return $class->display();
	}
}


?>
