<?php

/* adds a field to account settings for assigning a bitcoin address to receive reward tokens to */

/* to do: make this not use the "custom profile fields" feature, bring code up to date */


\Util\Filter::addFilter('App\Account\Settings_Model', 'getSettingsForm', 
	function($form, $args){
		
		$user = $args[0];
		$getSite = currentSite();
		$model = new \Core\Model;
		
		$getTokenField = $model->get('profile_fields', PRIMARY_TOKEN_FIELD);
		if($getTokenField AND $getTokenField['active'] == 1){
			$getVal = $model->fetchSingle('SELECT * FROM user_profileVals WHERE userId = :userId AND fieldId = :fieldId',
										array(':userId' => $user['userId'], ':fieldId' => PRIMARY_TOKEN_FIELD));
			
			$addressModule = $model->get('modules', 'address-manager', array(), 'slug');
			if($getVal AND $addressModule){
				$addressApp = $model->get('apps', $addressModule['appId']);
				
				$getAddress = $model->getAll('coin_addresses', array('userId' => $user['userId'], 'address' => $getVal['value']));
				if(count($getAddress) > 0){
					$getAddress = $getAddress[0];
					if($getAddress['verified'] == 0){
						
						$getTokenField['label'] .= ' <em><a href="'.$getSite['url'].'/'.$addressApp['url'].'/'.$addressModule['url'].'/verify/'.$getAddress['address'].'" target="_blank">(unverified)</a></em>';
					}
				}
			}	
			
			$token = new UI\Textbox('field-'.PRIMARY_TOKEN_FIELD);
			$token->setLabel($getTokenField['label']);
			$form->add($token);
		}
		
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
		
		if(isset($data['field-'.PRIMARY_TOKEN_FIELD]) AND trim($data['field-'.PRIMARY_TOKEN_FIELD]) != ''){
			$val = $data['field-'.PRIMARY_TOKEN_FIELD];
			$validate = new API\BTCValidate;
			if(!$validate->checkAddress($val)){
				throw new \Exception('Invalid bitcoin address!');
			}
			$getVal = $model->fetchSingle('SELECT * FROM user_profileVals WHERE userId = :userId AND fieldId = :fieldId',
										array(':userId' => $user['userId'], ':fieldId' => PRIMARY_TOKEN_FIELD));
										
			$getField = $model->get('profile_fields', PRIMARY_TOKEN_FIELD);
			if($getField){
				$insertData = array('value' => $val, 'lastUpdate' => timestamp());
				if($getVal){
					$update = $model->edit('user_profileVals', $getVal['profileValId'], $insertData);
				}
				else{
					//insert new one
					$insertData['userId'] = $user['userId'];
					$insertData['fieldId'] = PRIMARY_TOKEN_FIELD;
					$update = $model->insert('user_profileVals', $insertData);
				}
				
				if($update){
					$addressModel = new Tokenly\Address_Model;
					//change or insert new primary coin address
					$getAddress = $model->getAll('coin_addresses', array('userId' => $user['userId'], 'address' => $val));
				
					if(count($getAddress) > 0){
						$getAddress = $getAddress[0];
						$addressModel->editAddress($getAddress['addressId'], array('isPrimary' => 1, 'isXCP' => 1, 'label' => 'LTBcoin Compatible Address'));
					}
					else{
						//insert new address
						$addrData = array('userId' => $user['userId'], 'type' => 'btc', 'address' => $val, 'isXCP' => 1, 'isPrimary' => 1,
										  'label' => $getField['label']);
						$addressModel->addAddress($addrData);
						
					}

				}
			}
		}

	});
