<?php
namespace App\CMS;
use Core, UI;
class Settings_Model extends Core\Model
{
	function __construct()
	{
		parent::__construct();
	}
	
	protected function getSettings()
	{
		return $this->getAll('settings');
	}
	
	protected function editSettings($data)
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
			$this->container->editSetting($key, $value);
		}
		return true;
	}
	
	protected function editSetting($key, $value)
	{
		return $this->edit('settings', $key, array('settingValue' => $value), 'settingKey');
	}
	
	protected function getSetting($key)
	{
		$fetch = $this->get('settings', $key, array(), 'settingKey');
		if(!$fetch){
			return false;
		}
		return $fetch['settingValue'];
	}
	
	protected function getSettingsForm($settings)
	{
		$form = new UI\Form;
		foreach($settings as $setting){		
			$key = new UI\Hidden('key-'.$setting['settingKey']);
			if($setting['bool'] == 1){
				$value = new UI\Select($setting['settingKey'].'-value');
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
				$value = new UI\Textarea($setting['settingKey'].'-value');
				$value->setValue($setting['settingValue']);
			}
			else{
				$value = new UI\Textbox($setting['settingKey'].'-value');
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
