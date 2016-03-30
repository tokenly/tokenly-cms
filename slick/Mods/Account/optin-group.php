<?php

/* adds the user to a special group if they choose this option */

define('OPTIN_GROUP_SLUG', 'drop-list');
define('OPTIN_GROUP_OPT_LABEL', 'Receive <a href="/forum/post/counterwallet-asset-drop-list-signup" target="_blank">occasional free tokens</a> to my Counterparty compatible address?');

\Util\Filter::addFilter('App\Account\Settings_Model', 'getSettingsForm', 
	function($form, $args){
		$dropList = new UI\Checkbox('optin_group');
		$dropList->setLabel(OPTIN_GROUP_OPT_LABEL);
		$dropList->setBool(1);
		$dropList->setValue(1);
		$form->add($dropList); 

		return $form;
	});


\Util\Filter::addFilter('App\Account\Settings_Model', 'updateSettings', 
	function($result, $args){
		if(!$result){
			return false;
		}

		$user = $args[0];
		$data = $args[1];
		$model = new \Core\Model;
		if(!isset($data['optin_group'])){
			$data['optin_group'] = false;
		}
		$dropGroup = $model->get('groups', OPTIN_GROUP_SLUG, array(), 'slug');
		if($dropGroup){
			$inGroup = $model->getAll('group_users', array('userId' => $user['userId'], 'groupId' => $dropGroup['groupId']));
			if($inGroup AND count($inGroup) > 0){
				$inGroup = $inGroup[0];
			}
			else{
				$inGroup = false;
			}
			
			if(intval($data['optin_group']) == 1){
				if(!$inGroup){
					$model->insert('group_users', array('userId' => $user['userId'], 'groupId' => $dropGroup['groupId']));
				}
			}
			else{
				if($inGroup){
					$model->sendQuery('DELETE FROM group_users WHERE userId = :userId AND groupId = :groupId',
									array(':userId' => $user['userId'], ':groupId' => $dropGroup['groupId']));
				}
			}
		}
			

		return $result;
	});
