<?php
class Slick_App_CMS_ContentBlocks_Model extends Slick_Core_Model
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
			$content = new Slick_UI_Markdown('content', 'markdown');
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
		
		$add = $this->insert('content_blocks', $useData);
		if(!$add){
			throw new Exception('Error adding block');
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

}

?>
