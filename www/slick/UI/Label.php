<?php
class Slick_UI_Label extends Slick_UI_Object
{
	public function display($text = '')
	{
		$classText = '';
		if(count($this->classes) > 0){
			$classText = 'class="'.$this->getClassesText().'"';
		}
		
		$idText = '';
		if($this->id != ''){
			$idText = 'id="'.$this->id.'"';
		}
		
		$attributeText = $this->getAttributeText();
		
		$output = '<label '.$classText.' '.$idText.' '.$attributeText.'>'.$text.'</label>';
		
		return $output;
		
	}
	
	
}

?>
