<?php
namespace UI;
class Select extends FormObject
{
	protected $options = array();
	protected $optionAttributes = array();
	protected $selected = '';
	
	function __construct($name, $id = '')
	{
		parent::__construct();
		$this->name = $name;
		$this->id = $id;
		
	}
	
	public function display($elemWrap = ''){
		
		$classText = '';
		if(count($this->classes) > 0){
			$classText = 'class="'.$this->getClassesText().'"';
		}
		
		$idText = '';
		if($this->id != ''){
			$idText = 'id="'.$this->id.'"';
		}
		
		$attributeText = $this->getAttributeText();		
		
		$output = $this->label.'<select name="'.$this->name.'" '.$idText.' '.$classText.' '.$attributeText.'>';
		
		foreach($this->options as $key => $value){
			
			$attrText = '';
			if(isset($this->optionAttributes[$key])){
				$attributes = $this->optionAttributes[$key];
				
				foreach($attributes as $attrKey => $attrVal){
					$attrText .= $attrKey.'="'.$attrVal.'" ';
				}
				
			}
			
			$selectText = '';
			if($this->selected == $key){
				$selectText = 'selected';
			}
			
			$output .= '<option value="'.$key.'" '.$selectText.' '.$attrText.'>'.$value.'</option>';
			
		}
		
		$output .= '</select>';

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
	
	public function addOptAttribute($key, $attr, $value = '')
	{
		if(!isset($this->optionAttributes[$key])){
			$this->optionAttributes[$key] = array();
		}
		
		$this->optionAttributes[$key][$attr] = $value;
		
	}
	
	public function removeOptAttribute($key, $attr)
	{
		if(isset($this->optionAttributes[$key][$attr])){
			unset($this->optionAttributes[$key][$attr]);
		}
	}
	
	public function getOptAttributes($key)
	{
		if(isset($this->optionAttributes[$key])){
			return $this->optionAttributes[$key];
		}
		
		return false;
	}
	
}
