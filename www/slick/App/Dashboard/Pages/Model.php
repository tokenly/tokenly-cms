<?php
class Slick_App_Dashboard_Pages_Model extends Slick_Core_Model
{

	public function getPageForm($pageId = 0, $theme)
	{
		$getPage = false;
		if($pageId != 0){
			$getPage = $this->get('pages', $pageId);
		}
		
		$form = new Slick_UI_Form;
		
		$name = new Slick_UI_Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Page Name');
		$form->add($name);
		
		$url = new Slick_UI_Textbox('url');
		$url->setLabel('URL');
		$form->add($url);	

		
		$template = new Slick_UI_Select('template');
		$template->setLabel('Template');
		$template->addOption('default', 'default');
		$form->add($template);
		if($theme){
			$scanTheme = scandir(str_replace('/index.php', '', $_SERVER['SCRIPT_FILENAME']).'/themes/'.$theme['location'].'/templates');
			unset($scanTheme[0]);
			unset($scanTheme[1]);
			foreach($scanTheme as $file){
				$fileName = str_replace('.php', '', $file);
				$template->addOption($fileName, $fileName);
			}
			
		}
		
		$formatType = new Slick_UI_Select('formatType');
		$formatType->addOption('markdown', 'Markdown');
		$formatType->addOption('wysiwyg', 'WYSIWYG');
		$formatType->setLabel('Formatting Type (Save/Submit to change)');
		$form->add($formatType);		
		
		$active = new Slick_UI_Checkbox('active');
		$active->setLabel('Active');
		$active->setBool(1);
		$active->setValue(1);
		$form->add($active);
		
		
		if(!$getPage OR $getPage['formatType'] == 'markdown'){
			$content = new Slick_UI_Markdown('content', 'markdown');
			$content->setLabel('Content');
			$form->add($content);
		}
		else{
			$content = new Slick_UI_Textarea('content', 'html-editor');
			$content->setLabel('Content');
			$form->add($content);
		}
		
		$description = new Slick_UI_Textarea('description');
		$description->setLabel('Meta Description');
		$form->add($description);

		return $form;
	}
	


	public function addPage($data)
	{
		$req = array('name' => true, 'url' => false, 'siteId' => true, 'active' => false, 'content' => false, 'description' => false ,'template' => true, 'formatType' => false);
		$useData = array();
		foreach($req as $key => $required){
			if(!isset($data[$key])){
				if($required){
					throw new Exception(ucfirst($key).' required');
				}
				else{
					$useData[$key] = '';
				}
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		if(trim($useData['url']) == ''){
			$useData['url'] = $useData['name'];
		}
		$useData['url'] = genURL($useData['url']);
		$useData['url'] = $this->checkURLExists($useData['url'], $data['siteId']);
		
		
		$add = $this->insert('pages', $useData);
		if(!$add){
			throw new Exception('Error adding page');
		}
		
		$this->updatePageIndex($add, $useData['url'], $useData['siteId']);
		
		return $add;
		
		
	}
	
	public function updatePageIndex($pageId, $url, $siteId)
	{
		$pageModule = $this->get('modules', 'page-view', array(), 'slug');
		if(!$pageModule){
			throw new Exception('Page View module not installed');
		}
		$values = array(':moduleId' => $pageModule['moduleId'], ':id' => $pageId, ':siteId' => $siteId);
		$getIndex = $this->fetchSingle('SELECT * FROM page_index WHERE itemId = :id AND moduleId = :moduleId AND siteId = :siteId',
						$values);
		
		if($getIndex){
			$sql = 'UPDATE page_index SET url = :url WHERE itemId = :id AND moduleId = :moduleId AND siteId = :siteId';
		}
		else{
			$sql = 'INSERT INTO page_index(url, moduleId, itemId, siteId) VALUES(:url, :moduleId, :id, :siteId)';
		}
		$values[':url'] = $url;
		$update = $this->sendQuery($sql, $values);

		if(!$update){
			throw new Exception('Error updating page index');
		}
		
		return true;
						
		
		
	}
		
	public function editPage($id, $data)
	{
		$getPage = $this->get('pages', $id);
		$req = array('name' => true, 'url' => false, 'siteId' => true, 'active' => false, 'content' => false, 'description' => false ,'template' => true, 'formatType' => false);
		$useData = array();
		foreach($req as $key => $required){
			if(!isset($data[$key])){
				if($required){
					throw new Exception(ucfirst($key).' required');
				}
				else{
					$useData[$key] = '';
				}
			}
			else{
				$useData[$key] = $data[$key];
			}
		}

		if(trim($useData['url']) == ''){
			$useData['url'] = $useData['name'];
		}
		$useData['url'] = genURL($useData['url']);
		$useData['url'] = $this->checkURLExists($useData['url'], $useData['siteId'], $id);
		
		if($getPage['formatType'] == 'markdown' AND $useData['formatType'] != 'markdown'){
			$useData['content'] = markdown($useData['content']);
		}		
		
		$edit = $this->edit('pages', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing page');
		}
		
		$this->updatePageIndex($id, $useData['url'], $useData['siteId']);
		
		return true;
		
	}
	
	public function checkURLExists($url, $siteId, $ignore = 0, $count = 0)
	{
		$useurl = $url;
		if($count > 0){
			$useurl = $url.'-'.$count;
		}
		$pageModule = $this->get('modules', 'page-view', array(), 'slug');
		$values = array(':url' => $useurl, ':siteId' => $siteId);
				
		$get = $this->fetchSingle('SELECT * FROM page_index WHERE url = :url AND siteId = :siteId', $values);
		if($get AND $get['itemId'] != $ignore){
			//url exists already, search for next level of url
			$count++;
			return $this->checkURLExists($url, $siteId, $ignore, $count);
		}
		
		if($count > 0){
			$url = $url.'-'.$count;
		}

		return $url;
	}



}

?>
