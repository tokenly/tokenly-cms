<?php
namespace UI;
class Img extends Object
{
	
	public function display($src, $alt = '')
	{
		
		$idText = '';
		if($this->id != ''){
			$idText = 'id="'.$this->id.'"';
		}
		
		$classText = '';
		if(count($this->classes) > 0){
			$getClassText = $this->getClassesText();
			$classText = 'classes="'.$getClassText.'"';
		}
		
		$output = '<img src="'.$src.'" alt="'.$alt.'" '.$idText.' '.$classText.' '.$this->getAttributeText().' />';
		
		return $output;
		
	}
}
