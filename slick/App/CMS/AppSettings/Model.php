<?php
namespace App\CMS;
use Core, UI;
class AppSettings_Model extends \App\Meta_Model
{
	function __construct()
	{
		parent::__construct();
	}

	protected function getSettingsForm($settings)
	{
		$form = new UI\Form;	
		foreach($settings as $setting){		
			switch($setting['type']){
				case 'bool':
					$value = new UI\Select($setting['appMetaId'].'-value');
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
					$value = new UI\Textbox($setting['appMetaId'].'-value');
					$value->setValue($setting['metaValue']);
					break;
				case 'textarea':
					$value = new UI\Textarea($setting['appMetaId'].'-value');
					$value->setValue($setting['metaValue']);
					break;
				case 'select':
					$value = new UI\Select($setting['appMetaId'].'-value');
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
	
	protected function editSettings($data, $apps)
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
			throw new \Exception('No settings updated');
		}
		foreach($validSettings as $itemId => $value){
			$edit = $this->container->updateAppMeta($value['appId'], $value['key'], $value['value'], '', 1);
			if(!$edit){
				throw new \Exception('Failed updating setting ('.$value['key'].')');
			}
		}
		return true;
	}
}
