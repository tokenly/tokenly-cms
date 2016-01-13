<?php
namespace App\CMS;
use Core, UI, Util;
class MenuItems_Model extends Core\Model
{

	protected function getMenuPageForm($siteId, $itemId = 0)
	{
		$form = new UI\Form;
		
		$menuId = new UI\Select('menuId');
		$menuId->setLabel('Menu');
		$getMenus = $this->getAll('menus', array('siteId' => $siteId));
		foreach($getMenus as $menu){
			$menuId->addOption($menu['menuId'], $menu['name']);
		}
		$form->add($menuId);
		
		$pageId = new UI\Select('pageId', 'pageId');
		$pageId->setLabel('Page');
		$getPages = $this->getAll('pages', array('siteId' => $siteId));
		foreach($getPages as $page){
			$pageId->addOption($page['pageId'], $page['name']);
		}
		$form->add($pageId);
		
		$label = new UI\Textbox('label', 'label');
		$label->addAttribute('required');
		$label->setLabel('Label');
		$form->add($label);
		
		$rank = new UI\Textbox('rank', 'rank');
		$rank->setLabel('Order Rank');
		$form->add($rank);	
		
		$parentId = new UI\Select('parentId');
		$parentId->setLabel('Parent');
		$parentId->addOption('0-0-0', '-Root-');
		$form->add($parentId);
		$getMenus = $this->getAll('menus', array('siteId' => $siteId));
		foreach($getMenus as $menu){
			$items = $this->container->getMenuTree($menu['menuId']);
			foreach($items as $item){
				$isLink = 0;
				if($item['isLink']){
					$isLink = 1;
				}
				else{
					if($item['itemId'] == $itemId){
						continue;
					}
				}
				
				$parentId->addOption($menu['menuId'].'-'.$item['itemId'].'-'.$isLink, $item['label'].' ('.$item['rank'].')');
			}
		}
		
		return $form;
	}
	
	protected function getMenuTree($menuId, $getItems = false, $output = array(), $indent = 0)
	{
		if(!$getItems){
			$getItems = \App\View::getMenu($menuId);
		}

		foreach($getItems as $item){
			$space = '&nbsp;&nbsp;&nbsp;';
			$useSpace = '';
			for($i = 0; $i < $indent; $i++){
				$useSpace .= $space;
			}
			$item['label'] = $useSpace.$item['label'];
			$output[] = $item;
			if(isset($item['children'])){
				$output = $this->container->getMenuTree($menuId, $item['children'], $output, ($indent + 1));
			}
		}
		
		return $output;

	}
	
	protected function addMenuPage($data)
	{
		$req = array('pageId' => true, 'menuId' => true, 'rank' => false, 'label' => true, 'parentId' => true);
		$useData = array();
		foreach($req as $key => $required){
			if(!isset($data[$key])){
				if($required){
					throw new \Exception(ucfirst($key).' required');
				}
				else{
					$useData[$key] = '';
				}
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$expParent = explode('-', $useData['parentId']);
		$parentId = $expParent[1];
		$isLink = $expParent[2];
		
		$useData['parentId'] = $parentId;
		$useData['parentLink'] = $isLink;
		
		$add = $this->insert('menu_pages', $useData);
		if(!$add){
			throw new \Exception('Error adding menu page');
		}
		
		
		return $add;
	}


	protected function editMenuPage($id, $data)
	{
		$req = array('pageId' => true, 'menuId' => true, 'rank' => false, 'label' => true, 'parentId' => true);
		$useData = array();
		foreach($req as $key => $required){
			if(!isset($data[$key])){
				if($required){
					throw new \Exception(ucfirst($key).' required');
				}
				else{
					$useData[$key] = '';
				}
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$expParent = explode('-', $useData['parentId']);
		$parentId = $expParent[1];
		$isLink = $expParent[2];
		
		$useData['parentId'] = $parentId;
		$useData['parentLink'] = $isLink;
		
		$edit = $this->edit('menu_pages', $id, $useData);
		if(!$edit){
			throw new \Exception('Error editing menu page');
		}
		
		
		return $edit;
	}

	protected function addMenuLink($data)
	{
		$req = array('url' => true, 'menuId' => true, 'rank' => false, 'label' => true, 'parentId' => true);
		$useData = array();
		foreach($req as $key => $required){
			if(!isset($data[$key])){
				if($required){
					throw new \Exception(ucfirst($key).' required');
				}
				else{
					$useData[$key] = '';
				}
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$expParent = explode('-', $useData['parentId']);
		$parentId = $expParent[1];
		$isLink = $expParent[2];
		
		$useData['parentId'] = $parentId;
		$useData['parentLink'] = $isLink;

		$add = $this->insert('menu_links', $useData);
		if(!$add){
			throw new \Exception('Error adding menu link');
		}
		
		return $add;
	}


	protected function editMenuLink($id, $data)
	{
		$req = array('url' => true, 'menuId' => true, 'rank' => false, 'label' => true, 'parentId' => true);
		$useData = array();
		foreach($req as $key => $required){
			if(!isset($data[$key])){
				if($required){
					throw new \Exception(ucfirst($key).' required');
				}
				else{
					$useData[$key] = '';
				}
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$expParent = explode('-', $useData['parentId']);
		$parentId = $expParent[1];
		$isLink = $expParent[2];
		
		$useData['parentId'] = $parentId;
		$useData['parentLink'] = $isLink;

		$edit = $this->edit('menu_links', $id, $useData);
		if(!$edit){
			throw new \Exception('Error editing menu link');
		}
		
		return $edit;
	}

	protected function getMenuLinkForm($siteId, $itemId = 0)
	{
		$form = new UI\Form;
		
		$menuId = new UI\Select('menuId');
		$menuId->setLabel('Menu');
		$getMenus = $this->getAll('menus', array('siteId' => $siteId));
		foreach($getMenus as $menu){
			$menuId->addOption($menu['menuId'], $menu['name']);
		}
		$form->add($menuId);
		
		$url = new UI\Textbox('url', 'url');
		$url->addAttribute('required');
		$url->setLabel('URL');
		$form->add($url);
		
		$label = new UI\Textbox('label', 'label');
		$label->addAttribute('required');
		$label->setLabel('Label');
		$form->add($label);
		
		$rank = new UI\Textbox('rank', 'rank');
		$rank->setLabel('Order Rank');
		$form->add($rank);	

		$parentId = new UI\Select('parentId');
		$parentId->setLabel('Parent');
		$parentId->addOption('0-0-0', '-Root-');
		$form->add($parentId);
		$getMenus = $this->getAll('menus', array('siteId' => $siteId));
		foreach($getMenus as $menu){
			$items = $this->container->getMenuTree($menu['menuId']);
			foreach($items as $item){
				$isLink = 0;
				if($item['isLink']){
					$isLink = 1;
					if($item['itemId'] == $itemId){
						continue;
					}
				}
				
				$parentId->addOption($menu['menuId'].'-'.$item['itemId'].'-'.$isLink, $item['label'].' ('.$item['rank'].')');
			}
		}
		return $form;
	}
}
