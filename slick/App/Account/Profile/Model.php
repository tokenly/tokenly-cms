<?php
namespace App\Account;
use Core, UI, Util, API;

class Profile_Model extends Core\Model
{
	protected function getProfileForm($user, $siteId, $app)
	{
		$app = get_app('account');
		$meta = new \App\Meta_Model;
		$app['meta'] = $meta->appMeta($app['appId']);
		
		$form = new UI\Form;
		$groupIds = array();
		foreach($user['groups'] as $group){
			$groupIds[] = $group['groupId'];
		}
		$getFields = $this->fetchAll('SELECT f.*
									  FROM profile_fieldGroups g 
									  LEFT JOIN profile_fields f ON f.fieldId = g.fieldId
									  WHERE g.groupId IN('.join(',', $groupIds).')
									  AND f.active = 1 AND f.siteId = :siteId
									  GROUP BY g.fieldId
									  ORDER BY f.rank ASC', array(':siteId' => $siteId));
		foreach($getFields as $field){
			if($field['fieldId'] == PRIMARY_TOKEN_FIELD){
				continue; //skip token field
			}
			
			$slug = 'field-'.$field['fieldId'];
			switch($field['type']){
				case 'textbox':
					$elem = new UI\Textbox($slug);
					break;
				case 'textarea':
					$elem = new UI\Textarea($slug);
					break;
				case 'select':
					$elem = new UI\Select($slug);
					$options = explode("\n", $field['options']);
					foreach($options as $option){
						$option = trim($option);
						$elem->addOption($option, $option);
					}
					break;
			}
			if($field['public'] == 0){
				$field['label'] .= ' *private*';
				$elem->addClass('private');
			}
			$elem->setLabel($field['label']);
			$form->add($elem);
		}
	
		$avatarWidth = $app['meta']['avatarWidth'];
		$avatarHeight = $app['meta']['avatarHeight'];
		
		$avatar = new UI\File('avatar');
		$avatar->setLabel('Profile Avatar (resizes to '.$avatarWidth.'x'.$avatarHeight.')');
		$form->add($avatar);		
		$form->setFileEnc();
		
		return $form;
	}
	
	protected function updateProfile($user, $data, $isAPI = false)
	{
		$app = get_app('account');
		
		foreach($data as $key => $val){
			$fieldId = intval(str_replace('field-', '', $key));
			$getVal = $this->fetchSingle('SELECT * FROM user_profileVals WHERE userId = :userId AND fieldId = :fieldId',
										array(':userId' => $user['userId'], ':fieldId' => $fieldId));
										
			$getField = $this->get('profile_fields', $fieldId);
			if(!$getField){
				continue;
			}
			
			switch($getField['validation']){
				case 'btc':
					$validate = new API\BTCValidate;
					$check = $validate->checkAddress($val);
					if(!$check){
						throw new \Exception('Invalid bitcoin address: '.$val);
					}
					break;
				case 'email':
					if(!filter_var($val, FILTER_VALIDATE_EMAIL)){
						throw new \Exception('Invalid email address');
					}
					break;
				
			}
			
			$insertData = array('value' => strip_tags(trim($val)), 'lastUpdate' => timestamp());
			if($getVal){
				$update = $this->edit('user_profileVals', $getVal['profileValId'], $insertData);
			}
			else{
				//insert new one
				$insertData['userId'] = $user['userId'];
				$insertData['fieldId'] = $fieldId;
				$update = $this->insert('user_profileVals', $insertData);
			}
		}
		
		$meta = new \App\Meta_Model;
		$avWidth = $app['meta']['avatarWidth'];
		$avHeight = $app['meta']['avatarHeight'];

		if(!$isAPI){
			if(isset($_FILES['avatar']['tmp_name']) AND trim($_FILES['avatar']['tmp_name']) != ''){
				$picName = md5($user['username'].$_FILES['avatar']['name']).'.jpg';
				$upload = Util\Image::resizeImage($_FILES['avatar']['tmp_name'], SITE_PATH.'/files/avatars/'.$picName, $avWidth, $avHeight);
				if($upload){
					$meta->updateUserMeta($user['userId'], 'avatar', $picName);
				}
			}
		}
		else{
			if(isset($data['avatar'])){
				$tmpName = 'av-'.hash('sha256', mt_rand(0,10000).':'.$user['username'].time());
				$saveTmp = file_put_contents('/tmp/'.$tmpName, $data['avatar']);
				if($saveTmp){
					$getMime = @getimagesize('/tmp/'.$tmpName);
					if($getMime){
						$picName = md5($user['username'].$tmpName).'.jpg';
						$upload = Util\Image::resizeImage('/tmp/'.$tmpName, SITE_PATH.'/files/avatars/'.$picName, $avWidth, $avHeight);
						if($upload){
							$meta->updateUserMeta($user['userId'], 'avatar', $picName);
						}
					}
					unlink('/tmp/'.$tmpName);
				}
			}
		}		
		
		return true;
		
	}
	
	protected function getProfileInfo($user)
	{
		$getProfile = $this->getAll('user_profileVals', array('userId' => $user['userId']));
		$output = array();
		
		foreach($getProfile as $row){
			$output['field-'.$row['fieldId']] = $row['value'];
		}
		
		return $output;
	}
	

}
