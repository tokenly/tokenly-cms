<?php
class Slick_App_Dashboard_AppSettings_Model extends Slick_App_Meta_Model
{
	function __construct()
	{
		parent::__construct();
		
	}
	

	public function getSettingsForm($settings)
	{
		
		$form = new Slick_UI_Form;
		
		foreach($settings as $setting){		
			$key = new Slick_UI_Hidden('key-'.$setting['metaKey']);
			
			switch($setting['type']){
				case 'bool':
					$value = new Slick_UI_Select($setting['metaKey'].'-value');
					$value->addOption(1, 'Yes');
					$value->addOption(0, 'No');
					
					if($setting['metaValue'] == 1){
						$value->setSelected(1);
					}
					else{
						$value->setSelected(0);
					}
					break;
				case 'textbox':
					$value = new Slick_UI_Textbox($setting['metaKey'].'-value');
					$value->setValue($setting['metaValue']);
					break;
				case 'textarea':
					$value = new Slick_UI_Textarea($setting['metaKey'].'-value');
					$value->setValue($setting['metaValue']);
					break;
				case 'select':
					$value = new Slick_UI_Select($setting['metaKey'].'-value');
					$options = explode("\n", $setting['options']);
					foreach($options as $option){
						$value->addOption($option, $option);
					}
					$value->setValue($setting['metaValue']);
					break;				
			}
			$value->setLabel($setting['label']);
			$form->add($key);
			$form->add($value);
		}
		
		return $form;
	}	
	
	public function editSettings($appId, $data)
	{
		$alteredKeys = array();
		foreach($data as $key => $item){
			if(substr($key, 0, 4) == 'key-'){
				$alteredKeys[] = substr($key, 4);
			}
		}
		
		$keyValues = array();
		foreach($data as $key => $item){
			$isValue = substr($key, -6);
			$thisKey = substr($key, 0, -6);
			if($isValue == '-value'){
				foreach($alteredKeys as $alter){
					if($thisKey == $alter){
						$keyValues[$thisKey] = $item;
					}
				}
			}
		}
		
		foreach($keyValues as $key => $value){
			$this->updateAppMeta($appId, $key, $value, '', 1);
		}
		
		return true;
	}
	
}
