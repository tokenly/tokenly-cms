<?php
namespace App\CMS;
use Core, UI, Util;
class PageTags_Model extends Core\Model
{
	protected function getTagForm($tagId = 0)
	{
		$form = new UI\Form;
		$tag = new UI\Textbox('tag');
		$tag->addAttribute('required');
		$tag->setLabel('Tag');
		$form->add($tag);
		
		$class = new UI\Textbox('class');
		$class->addAttribute('required');
		$class->setLabel('Class');
		$form->add($class);	
		
		return $form;
	}
	
	protected function addTag($data)
	{
		$req = array('tag', 'class');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new \Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$add = $this->insert('page_tags', $useData);
		if(!$add){
			throw new \Exception('Error adding tag');
		}
		
		return $add;	
	}
		
	protected function editTag($id, $data)
	{
		$req = array('tag', 'class');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new \Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		$edit = $this->edit('page_tags', $id, $useData);
		if(!$edit){
			throw new \Exception('Error editing tag');
		}
		return true;
	}
}
