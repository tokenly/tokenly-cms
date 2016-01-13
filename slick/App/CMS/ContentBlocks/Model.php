<?php
namespace App\CMS;
use Core, UI, Util;
class ContentBlocks_Model extends Core\Model
{
	protected function getBlockForm($blockId = 0)
	{
		$getBlock = false;
		if($blockId != 0){
			$getBlock = $this->get('content_blocks', $blockId);
		}
		$form = new UI\Form;
		
		$name = new UI\Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Block Name');
		$form->add($name);
		
		$slug = new UI\Textbox('slug');
		$slug->addAttribute('required');
		$slug->setLabel('Slug');
		$form->add($slug);	

		
		$formatType = new UI\Select('formatType');
		$formatType->addOption('markdown', 'Markdown');
		$formatType->addOption('wysiwyg', 'WYSIWYG');
		$formatType->addOption('raw', 'Raw Text');
		$formatType->setLabel('Formatting Type (Save/Submit to change)');
		$form->add($formatType);

		$active = new UI\Checkbox('active');
		$active->setLabel('Active');
		$active->setBool(1);
		$active->setValue(1);
		$form->add($active);
		
		if(!$getBlock OR $getBlock['formatType'] == 'markdown'){
			$content = new UI\Markdown('content', 'markdown');
			$content->setLabel('Content');
			$form->add($content);
		}
		elseif($getBlock['formatType'] == 'wysiwyg'){
			$content = new UI\Textarea('content', 'html-editor');
			$content->setLabel('Content');
			$form->add($content);
		}
		else{
			$content = new UI\Textarea('content');
			$content->setLabel('Content');
			$form->add($content);
		}

		return $form;
	}

	protected function addBlock($data)
	{
		$req = array('name', 'slug', 'siteId', 'active', 'content', 'formatType');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new \Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$add = $this->insert('content_blocks', $useData);
		if(!$add){
			throw new \Exception('Error adding block');
		}
			
		return $add;
	}
		
	protected function editBlock($id, $data)
	{
		$getBlock = $this->get('content_blocks', $id);
		$req = array('name', 'slug', 'siteId', 'active', 'content', 'formatType');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new \Exception(ucfirst($key).' required');
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
			throw new \Exception('Error editing block');
		}
		return true;
	}
	
	protected function deleteBlock($id)
	{
		$getBlock = $this->get('content_blocks', $id);
		$delete = false;
		if($getBlock){
			$delete = $this->delete('content_blocks', $id);
		}	
		return $delete;	
	}
}
