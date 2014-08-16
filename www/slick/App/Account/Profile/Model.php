<?php
class Slick_App_Account_Profile_Model extends Slick_Core_Model
{
	public function getProfileForm($user, $siteId, $app)
	{
		$form = new Slick_UI_Form;
		
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
					$elem = new Slick_UI_Textbox($slug);
					break;
				case 'textarea':
					$elem = new Slick_UI_Textarea($slug);
					break;
				case 'select':
					$elem = new Slick_UI_Select($slug);
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
		
		$avatar = new Slick_UI_File('avatar');
		$avatar->setLabel('Profile Avatar (resizes to '.$avatarWidth.'x'.$avatarHeight.')');
		$form->add($avatar);		
		$form->setFileEnc();
		
		return $form;
	}
	
	public function updateProfile($user, $data, $app, $isAPI = false)
	{
		
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
					$validate = new Slick_API_BTCValidate;
					$check = $validate->checkAddress($val);
					if(!$check){
						throw new Exception('Invalid bitcoin address: '.$val);
					}
					break;
				case 'email':
					if(!filter_var($val, FILTER_VALIDATE_EMAIL)){
						throw new Exception('Invalid email address');
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
		
		$meta = new Slick_App_Meta_Model;
		$avWidth = $app['meta']['avatarWidth'];
		$avHeight = $app['meta']['avatarHeight'];

		if(!$isAPI){
			if(isset($_FILES['avatar']['tmp_name']) AND trim($_FILES['avatar']['tmp_name']) != ''){
				$picName = md5($user['username'].$_FILES['avatar']['name']).'.jpg';
				$upload = Slick_Util_Image::resizeImage($_FILES['avatar']['tmp_name'], SITE_PATH.'/files/avatars/'.$picName, $avWidth, $avHeight);
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
						$upload = Slick_Util_Image::resizeImage('/tmp/'.$tmpName, SITE_PATH.'/files/avatars/'.$picName, $avWidth, $avHeight);
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
	
	public function getProfileInfo($user)
	{
		$getProfile = $this->getAll('user_profileVals', array('userId' => $user['userId']));
		$output = array();
		
		foreach($getProfile as $row){
			$output['field-'.$row['fieldId']] = $row['value'];
		}
		
		return $output;
	}

}

?>
