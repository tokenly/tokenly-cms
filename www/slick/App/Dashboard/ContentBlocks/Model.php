<?php
class Slick_App_Dashboard_ContentBlocks_Model extends Slick_Core_Model
{

	public function getBlockForm($blockId = 0)
	{
		$getBlock = false;
		if($blockId != 0){
			$getBlock = $this->get('content_blocks', $blockId);
		}
		
		$form = new Slick_UI_Form;
		
		$name = new Slick_UI_Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Block Name');
		$form->add($name);
		
		$slug = new Slick_UI_Textbox('slug');
		$slug->addAttribute('required');
		$slug->setLabel('Slug');
		$form->add($slug);	

		
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
		
		
		
		if(!$getBlock OR $getBlock['formatType'] == 'markdown'){
			$pagePad = $this->getInkpadUrl($blockId);
			$content = new Slick_UI_Inkpad('content');
			$content->setInkpad($pagePad);
			$content->setLabel('Content');
			$form->add($content);
		}
		else{
			$content = new Slick_UI_Textarea('content', 'html-editor');
			$content->setLabel('Content');
			$form->add($content);
		}

		return $form;
	}
	


	public function addBlock($data)
	{
		$req = array('name', 'slug', 'siteId', 'active', 'content', 'formatType');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$getContent =false;
		if(isset($_POST['content_inkpad'])){
			$contentInkpad = new Slick_UI_Inkpad('content');
			$contentInkpad->setInkpad($_POST['content_inkpad']);
			$getContent = $contentInkpad->getValue();
			if($getContent){
				$useData['content'] = $getContent;
			}
		}			
		
		$add = $this->insert('content_blocks', $useData);
		if(!$add){
			throw new Exception('Error adding block');
		}
		
		if(isset($_POST['content_inkpad']) AND $getContent){
			$meta = new Slick_App_Meta_Model;
			$meta->updateBlockMeta($add, 'inkpad-url', $_POST['content_inkpad']);
		}		
		
		return $add;
		
		
	}
		
	public function editBlock($id, $data)
	{
		$getBlock = $this->get('content_blocks', $id);
		$req = array('name', 'slug', 'siteId', 'active', 'content', 'formatType');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		if($getBlock['formatType'] == 'markdown' AND $useData['formatType'] != 'markdown'){
			$useData['content'] = markdown($useData['content']);
		}				
		
		$edit = $this->edit('content_blocks', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing block');
		}
		
		return true;
		
	}

	public function getInkpadUrl($blockId)
	{
		$meta = new Slick_App_Meta_Model;
		if($blockId != 0){
			$getUrl = $meta->getBlockMeta($blockId, 'inkpad-url');
			if($getUrl){
				return $getUrl;
			}
		}

		//generate new inkpad
		$url = Slick_UI_Inkpad::getNewPad();

		if($blockId != 0){
			$meta->updateBlockMeta($blockId, 'inkpad-url', $url);
		}
		
		return $url;
	}



}

?>
