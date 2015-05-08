<?php
namespace UI;
class List extends Object
{
	protected $itemClass = '';
	protected $singleClasses = array();

	
	public function display($items = array(), $type = 'ul')
	{
		if(!is_array($items)){
			$items = explode(',', $items);
		}

		$classText = '';
		if(count($this->classes) > 0){
			$classText = 'class="'.$this->getClassesText().'"';
		}
		
		$idText = '';
		if($this->id != ''){
			$idText = 'id="'.$this->id.'"';
		}
		
		$attributeText = $this->getAttributeText();

		$output = '<'.$type.' '.$classText.' '.$idText.' '.$attributeText.' >';
		
		foreach($items as $key => $item){
			
			$itemText = $item;
			if(is_object($item) AND method_exists($item, 'display')){
				$itemText = $item->display();
			}
			
			$classText = '';
			$extraClass = '';
			if($this->itemClass != '' OR isset($this->singleClasses[$key])){
				if(isset($this->singleClasses[$key])){
					$extraClass = $this->singleClasses[$key];
				}
				$classText = 'class="'.$this->itemClass.' '.$extraClass.'"';
			}
			$output .= '<li '.$classText.'>'.$itemText.'</li>';
			
		}
		
		$output .= '</'.$type.'>';
		
		return $output;
		
	}
	
	public function setItemClass($class)
	{
		$this->itemClass = $class;
		
	}
	
	public function setSingleClass($key, $class)
	{
		$this->singleClasses[$key] = $class;
	}
}
