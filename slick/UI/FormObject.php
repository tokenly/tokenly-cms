<?php
namespace UI;
class FormObject extends Object
{
	protected $name = '';
	protected $value = '';
	protected $label = '';
	protected $label_raw = '';
	protected $wrap_class = 'form-group';
	
	function __construct()
	{
		parent::__construct();
		$this->addClass(get_class($this));
	}
	
	public function setName($name)
	{
		$this->name = $name;
	}
	
	public function getName()
	{
		return $this->name;
	}
	
	public function setValue($value)
	{
		$this->value = $value;
	}
	
	public function getValue()
	{
		return $this->value;
	}
	
	public function setLabel($text, $class='', $id = '')
	{
		$label = new Label;
		
		if($class != ''){
			if(is_array($class)){
				$label->setClasses($class);
			}
			else{
				$label->addClass($class);
			}
		}
		$label->addClass(get_class($this).'_Label');
		
		if($this->id != ''){
			$label->addAttribute('for', $this->id);
		}
		
		if($id != ''){
			$label->setId($id);
		}
		
		$this->label = $label->display($text);
		$this->label_raw = $text;
		
	}
	
	public function getLabel()
	{
		return $this->label;
	}
	
	public function getRawLabel()
	{
		return $this->label_raw;
	}
	
	public function getWrapClass()
	{
		return $this->wrap_class;
	}
	
	public function setWrapClass($class)
	{
		$this->wrap_class = $class;
	}
}
