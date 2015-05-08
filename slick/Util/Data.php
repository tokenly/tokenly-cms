<?php
namespace Util;
use Core;

class Data extends Core\Model
{
	public function getFormData($form)
	{
		if(!isset($_POST) OR count($_POST) == 0){
			return false;
		}
		
		$fields = $form->fields;
		
		$checkVars = array();
		$data = array();
		
		foreach($fields as $field){
			$class = get_class($field);
			
			if($class == '\\UI\\Date' || $class == '\\UI\\DateTime'){
				$_POST[$field->getName()] = $field->getPostValue();
			}
			elseif($class == '\\UI\\Inkpad' AND trim($field->getInkpad()) != ''){
				$_POST[$field->getName()] = $field->getValue();
			}
			$checkVars[] = $field->getName();
		}
		
		foreach($checkVars as $var){
			if(isset($_POST[$var])){
				$data[$var] = $_POST[$var];
			}
			else{
				$data[$var] = '';
			}
			
		}
		
		return $data;
	}
}
