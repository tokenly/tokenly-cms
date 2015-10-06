<?php
namespace UI;
class Radio extends FormObject
{
	protected $options = array();
	protected $selected = '';
	
	function __construct($name, $id = '')
	{
		parent::__construct();
		$this->name = $name;
		$this->id = $id;
		
	}
	
	public function display($elemWrap = '', $labelDir = 'L'){
		
		$classText = '';
		if(count($this->classes) > 0){
			$classText = 'class="'.$this->getClassesText().'"';
		}
		
		$idText = '';
		if($this->id != ''){
			$idText = 'id="'.$this->id.'"';
		}
		
		$attributeText = $this->getAttributeText();		
		
		$output = '<div '.$classText.' '.$idText.' '.$attributeText.'>';
		$output .= $this->label;
		foreach($this->options as $val => $label){
			
			if($labelDir == 'L'){
				$output .= '<label for="rad'.$val.'">'.$label.'</label>';
			}
			
			$selectText = '';
			if($this->selected == $val){
				$selectText = 'checked';
			}
			
			$output .= '<input type="radio" name="'.$this->name.'" id="rad'.$val.'" value="'.$val.'" '.$selectText.' />';

			if($labelDir == 'R'){
				$output .= '<label for="rad'.$val.'">'.$label.'</label>';
			}
			
			
		}
		$output .= '</div>';

		if($elemWrap != ''){
			$misc = new Misc;
			$output = $misc->wrap($elemWrap, $output, $this->wrap_class);
		}
	
		return $output;
		
	}
	
	public function getOptions()
	{
		return $this->options();
	}
	
	public function setOptions($options = array())
	{
		$this->options = $options;
	}
	
	public function addOption($key, $value)
	{
		$this->options[$key] = $value;
	}
	
	public function removeOption($key)
	{
		unset($this->options[$key]);
	}
	
	public function setSelected($key)
	{
		$this->selected = $key;
		
	}
}
