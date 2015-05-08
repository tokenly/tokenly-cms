<?php
namespace UI;
class CheckboxList extends FormObject
{
	protected $options = array();
	protected $selected = array();
	protected $labelDir = 'L';
	protected $elemWrap = '';
	protected $elemClass = '';
	protected $elemData = array();
	
	function __construct($name, $id = '')
	{
		parent::__construct();
		$this->name = $name;
		$this->id = $id;
		$this->addClass('checkboxList');
		
	}
	
	public function display($elemWrap = ''){
		

		$labelDir = $this->labelDir;
		
		$classText = '';
		if(count($this->classes) > 0){
			$classText = 'class="'.$this->getClassesText().'"';
		}
		
		$idText = '';
		if($this->id != ''){
			$idText = 'id="'.$this->id.'"';
		}
		
		$attributeText = $this->getAttributeText();		
		
		$checkArray = '';
		if(count($this->options) > 1){
			$checkArray = '[]';
		}
		
		$output = $this->label.'<div '.$classText.' '.$idText.' '.$attributeText.'>';
		foreach($this->options as $val => $label){
			
			if($labelDir == 'L'){
				$output .= '<label for="chk'.$val.'">'.$label.'</label>';
			}
			
			$selectText = '';
			foreach($this->selected as $selKey => $selVal){
				if($selVal == $val){
					$selectText = 'checked';
					break 1;
				}
			}
		
			
			$output .= '<input type="checkbox" name="'.$this->name.$checkArray.'" id="chk'.$val.'-'.$this->name.'" value="'.$val.'" '.$selectText.' />';

			if($labelDir == 'R'){
				$output .= '<label for="chk'.$val.'-'.$this->name.'">'.$label.'</label>';
			}
			
			
		}
		$output .= '</div>';
		
		if($this->elemWrap != ''){
			$this->elemWrap = trim($this->elemWrap);
			$elemData = '';
			if(count($this->elemData) > 0){
				foreach($this->elemData as $k => $d){
					$elemData .= ' data-'.$k.'="'.$d.'" ';
				}
			}
			$output = '<'.$this->elemWrap.' class="'.$this->elemClass.'" '.$elemData.'>'.$output.'</'.$this->elemWrap.'>';
		}
	
		return $output;
		
	}
	
	public function getOptions()
	{
		return $this->options;
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
		$this->selected[] = $key;
	}

	public function setLabelDir($dir)
	{
		$this->labelDir = $dir;
	}
	
	public function setElemWrap($elem)
	{
		$this->elemWrap = $elem;
	}
	
	public function getElemWrap()
	{
		return $this->elemWrap;
	}
	
	public function setElemClass($class)
	{
		$this->elemClass = $class;
	}
	
	public function getElemClass()
	{
		return $this->elemClass;
	}
	
	public function setElemData($key, $data)
	{
		$this->elemData[$key] = $data;
	}
	
}
