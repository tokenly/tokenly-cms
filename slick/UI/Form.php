<?php
namespace UI;
use Util;
class Form
{
	protected $formName = '';
	public $fields = array();
	protected $method;
	protected $action;
	protected $submitText = 'Submit';
	protected $enctype = '';
	
	
	function __construct($name = '', $action = '', $method = 'post')
	{
		$this->formName = $name;
		$this->action = $action;
		$this->method = $method;
	}

	
	public function setMethod($method)
	{
		$this->method = $method;
	}
	
	public function open()
	{
		$encText = '';
		if($this->enctype != ''){
			$encText = 'enctype="'.$this->enctype.'" ';
		}
		
		return '<form action="'.$this->action.'" method="'.$this->method.'" name="'.$this->formName.'" '.$encText.' >';
	}
	
	public function close()
	{
		return '</form>';	
	}
	
	public function displayFields($elemWrap = 'div')
	{
		$output = '';
		foreach($this->fields as $name => $object)
		{
			$output .= $object->display($elemWrap);
			
		}
		
		return $output;
	}
	
	public function displaySubmit($elemWrap = '')
	{
		if(isset($this->fields['submit'])){
			return $this->field('submit')->display($elemWrap);
		}
		$button = new Button('submit', '', 'submit');
		$button->setValue($this->submitText);
		
		return $button->display($elemWrap);
		
	}
	
	public function setSubmitText($text)
	{
		$this->submitText = $text;
		
	}
	
	public function display($elemWrap = 'div')
	{
		$output = $this->open();
		$output .= $this->displayFields($elemWrap);
		$output .= $this->displaySubmit($elemWrap);
		$output .= $this->close();
		
		return $output;
	}
	
	public function setFields($fields = array())
	{
		$this->fields = $fields;	
	}
	
	public function add($field, $name = '')
	{
		if($name == ''){
			$name = $field->getName();
		}
		$this->fields[$name] = $field;
	}
	
	public function remove($field)
	{
		unset($this->fields[$field]);
		
	}
	
	public function getData()
	{
		$data = array();
		foreach($this->fields as $key => $field){
			$data[$key] = $field->getValue();
		}
		
		return $data;
		
	}
	
	public function setValues($data = array())
	{	
		
		foreach($data as $key => $value)
		{
			if(!isset($this->fields[$key])){
				continue;
			}
			
			$class = get_class($this->field($key));
			
			if($class == 'UI\\Checkbox'){
				if($this->field($key)->isBool == 1){
					$this->field($key)->setChecked($value);
				}
				else{
					$this->field($key)->setValue($value);
				}
			}
			elseif($class == 'UI\\Select'){
				$this->field($key)->setSelected($value);	
			}
			elseif($class == 'UI\\CheckboxList'){
				if(is_array($value)){
					foreach($value as $valKey => $val){
						$this->field($key)->setSelected($val);
					}
				}
			}
			else{
				$this->field($key)->setValue($value);
			}
		}
	}
	
	public function field($field)
	{
		if(!isset($this->fields[$field])){
			return false;
		}
		return $this->fields[$field];
	}
	
	public function getFields()
	{
		$output = array();
		foreach($this->fields as $k => $field){
			$var = get_object_vars_all($field);
			$output[$k] = $var;
		}
		return $output;
	}
	
	public function grabData()
	{
		$data = new Util\Data;
		$getThisData = $data->getFormData($this);
		
		return $getThisData;
	}
	
	public function setFileEnc()
	{
		$this->enctype = 'multipart/form-data';
	}
	
}
