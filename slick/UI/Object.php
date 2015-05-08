<?php
namespace UI;
class Object
{
	protected $classes = array();
	protected $id = '';
	protected $attributes = array();
	
	
	function __construct()
	{
		
		
	}
	
	public function setClasses($classList = array())
	{
		if(!is_array($classList)){
			return false;
		}
		
		$this->classes = $classList;
		return true;
		
	}

	
	public function addClass($class)
	{
		$this->classes[] = $class;
	}
	
	public function removeClass($class)
	{
		foreach($this->classes as $key => $thisClass){
			if($thisClass == $class){
				unset($this->classes[$key]);
				return true;
			}
		}
		
		return false;
	}
	
	public function setID($id)
	{
		$this->id = $id;
	}
	
	public function getID()
	{
		return $this->id;
	}
	
	
	public function getClasses()
	{
			return $this->classes();
	}
	
	public function getClassesText()
	{
		return implode(' ', $this->classes);
		
	}
	
	public function setAttributes($attributes)
	{
		$this->attributes = $attributes;
	}
	
	public function addAttribute($attr, $value='')
	{
		$this->attributes[$attr] = $value;
	}
	
	public function removeAttribute($attr)
	{
		unset($this->attributes[$attr]);
		
	}
	
	public function getAttributes()
	{
		return $this->attributes;
		
	}
	
	public function getAttributeText()
	{
		$output = '';
		
		foreach($this->attributes as $attr => $val){
			
			$output .= ' '.$attr;
			
			if($val != ''){
				$output .= '="'.$val.'"';
			}
		}
		
		return $output;
		
	}
}

