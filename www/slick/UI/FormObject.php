<?php
class Slick_UI_FormObject extends Slick_UI_Object
{
	protected $name = '';
	protected $value = '';
	protected $label = '';
	
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
		$label = new Slick_UI_Label;
		
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
		
	}
	
	public function getLabel()
	{
		return $this->label;
	}
		

}

?>
