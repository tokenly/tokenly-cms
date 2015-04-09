<?php
class Slick_UI_Button extends Slick_UI_FormObject
{
	protected $buttonType;
	
	function __construct($name, $id = '', $type = 'button')
	{
		parent::__construct();
		$this->name = $name;
		$this->id = $id;
		$this->buttonType = $type;
		
	}

	public function display($elemWrap = '')
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

		$output = '<input type="'.$this->buttonType.'" name="'.$this->name.'" '.$idText.' '.$classText.' '.$attributeText.' value="'.$this->value.'" />';

		if($elemWrap != ''){
			$misc = new Slick_UI_Misc;
			$output = $misc->wrap($elemWrap, $output);
		}
		
		return $output;
		
	}
	
}


?>
