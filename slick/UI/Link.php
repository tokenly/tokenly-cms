<?php
class Slick_UI_Link extends Slick_UI_Object
{
	public function display($url, $text = '', $target = '')
	{
		$idText = '';
		if($this->id != ''){
			$idText = 'id="'.$this->id.'"';
		}
		
		$classText = '';
		if(count($this->classes) > 0){
			$classText = 'class="'.$this->getClassesText().'"';
		}
		
		$targetText = '';
		if($target != ''){
			$targetText = 'target="'.$target.'"';
		}
		
		$output = '<a href="'.$url.'" '.$targetText.' '.$classText.' '.$idText.' '.$this->getAttributeText().'>'.$text.'</a>';
		
		return $output;
		
	}
	
	
	
}


?> 
