<?php
class Slick_App_CMS_Settings_Model extends Slick_Core_Model
{
	function __construct()
	{
		parent::__construct();
		
	}
	
	public function getSettings()
	{
		$sql = 'SELECT * FROM settings';
		return $this->fetchAll($sql);
		
	}
	
	public function editSettings($data)
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
			$this->editSetting($key, $value);
		}
		
		return true;
	}
	
	public function editSetting($key, $value)
	{
		$sql = 'UPDATE settings SET settingValue = :val WHERE settingKey = :key';
		return $this->sendQuery($sql, array(':val' => $value, ':key' => $key));
	}
	
	public function getSetting($key)
	{
		$sql = 'SELECT * FROM settings WHERE settingKey = :key';
		$fetch = $this->fetchSingle($sql, array(':key' => $key));
		if(!$fetch){
			return;
		}
		
		return $fetch['settingValue'];
	}
	
	public function getSettingsForm($settings)
	{
		
		$form = new Slick_UI_Form;
		
		foreach($settings as $setting){		
			$key = new Slick_UI_Hidden('key-'.$setting['settingKey']);
			if($setting['bool'] == 1){
				$value = new Slick_UI_Select($setting['settingKey'].'-value');
				$value->addOption(1, 'Yes');
				$value->addOption(0, 'No');
				
				if($setting['settingValue'] == 1){
					$value->setSelected(1);
				}
				else{
					$value->setSelected(0);
				}
			}
			elseif($setting['textarea'] == 1){
				$value = new Slick_UI_Textarea($setting['settingKey'].'-value');
				$value->setValue($setting['settingValue']);
			}
			else{
				$value = new Slick_UI_Textbox($setting['settingKey'].'-value');
				$value->setValue($setting['settingValue']);
			}
			$value->addAttribute('required');
			
			$value->setLabel($setting['label']);
			
			$form->add($key);
			$form->add($value);
		}
		
		return $form;
	}
	
}


?>



