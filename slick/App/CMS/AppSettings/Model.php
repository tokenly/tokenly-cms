<?php
class Slick_App_CMS_AppSettings_Model extends Slick_App_Meta_Model
{
	function __construct()
	{
		parent::__construct();
	}

	public function getSettingsForm($settings)
	{
		$form = new Slick_UI_Form;	
		foreach($settings as $setting){		
			switch($setting['type']){
				case 'bool':
					$value = new Slick_UI_Select($setting['appMetaId'].'-value');
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
					$value = new Slick_UI_Textbox($setting['appMetaId'].'-value');
					$value->setValue($setting['metaValue']);
					break;
				case 'textarea':
					$value = new Slick_UI_Textarea($setting['appMetaId'].'-value');
					$value->setValue($setting['metaValue']);
					break;
				case 'select':
					$value = new Slick_UI_Select($setting['appMetaId'].'-value');
					$options = explode("\n", $setting['options']);
					foreach($options as $option){
						$value->addOption($option, $option);
					}
					$value->setValue($setting['appMetaValue']);
					break;				
			}
			$value->setLabel($setting['label']);
			$form->add($value);
		}
		
		return $form;
	}	
	
	public function editSettings($data, $apps)
	{
		$validApps = array();
		foreach($apps as $app){
			$validApps[] = $app['appId'];
		}
		$validSettings = array();
		foreach($data as $k => $val){
			$expkey = explode('-', $k);
			if(isset($expkey[1]) AND $expkey[1] == 'value'){
				$itemId = $expkey[0];
				$getSetting = $this->get('app_meta', $itemId);
				if($getSetting AND in_array($getSetting['appId'], $validApps) AND $getSetting['isSetting'] == 1){
					if($val != $getSetting['metaValue']){
						$validSettings[$itemId] = array('value' => $val, 'appId' => $getSetting['appId'], 'key' => $getSetting['metaKey']);
					}
				}
			}
		}
		if(count($validSettings) == 0){
			throw new Exception('No settings updated');
		}
		foreach($validSettings as $itemId => $value){
			$edit = $this->updateAppMeta($value['appId'], $value['key'], $value['value'], '', 1);
			if(!$edit){
				throw new Exception('Failed updating setting ('.$value['key'].')');
			}
		}
		return true;
	}
}
