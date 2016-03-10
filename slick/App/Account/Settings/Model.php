<?php
namespace App\Account;
use Core, UI, Util, API, App\Tokenly;
class Settings_Model extends Core\Model
{
	protected function getSettingsForm($user, $adminView = false)
	{
		$app = get_app('account');
		$form = new UI\Form;
		$form->setFileEnc();
		
		if($adminView){
			$username = new UI\Textbox('username');
			$username->setLabel('Username');
			$username->addAttribute('required');
			$form->add($username);
		}
		
		$email = new UI\Textbox('email');
		$email->setLabel('Email Address');
		$form->add($email);
		
		
		if(!$adminView){
			$pass = new UI\Password('password');
			$pass->setLabel('New Password');
			$pass->addAttribute('autocomplete', 'off');
			$form->add($pass);
			
			$pass2 = new UI\Password('password2');
			$pass2->setLabel('New Password (repeat)');
			$pass2->addAttribute('autocomplete', 'off');
			$form->add($pass2);
		}
		
		$getSite = $this->get('sites', $_SERVER['HTTP_HOST'], array(), 'domain');
		
		$getTokenField = $this->get('profile_fields', PRIMARY_TOKEN_FIELD);
		if($getTokenField){
			$getVal = $this->fetchSingle('SELECT * FROM user_profileVals WHERE userId = :userId AND fieldId = :fieldId',
										array(':userId' => $user['userId'], ':fieldId' => PRIMARY_TOKEN_FIELD));
			
			$addressModule = $this->get('modules', 'address-manager', array(), 'slug');
			if($getVal AND $addressModule){
				$addressApp = $this->get('apps', $addressModule['appId']);
				
				$getAddress = $this->getAll('coin_addresses', array('userId' => $user['userId'], 'address' => $getVal['value']));
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
		
		
		$ref = new UI\Textbox('refUser');
		$ref->setLabel('Referred By (enter referral username)');
		if(isset($user['affiliate']) AND $user['affiliate']){
			$ref->addAttribute('disabled');
			$ref->setValue($user['affiliate']['username']);
		}
		$form->add($ref);				
		
		$showEmail = new UI\Checkbox('showEmail');
		$showEmail->setLabel('Show email address in profile?');
		$showEmail->setBool(1);
		$showEmail->setValue(1);
		$form->add($showEmail);
		
		$pubProf = new UI\Checkbox('pubProf');
		$pubProf->setLabel('Make profile public?');
		$pubProf->setBool(1);
		$pubProf->setValue(1);
		$form->add($pubProf);
		
		$emailNotify = new UI\Checkbox('emailNotify');
		$emailNotify->setLabel('Email me when notification is received?');
		$emailNotify->setBool(1);
		$emailNotify->setValue(1);
		$form->add($emailNotify);
		
		$dropList = new UI\Checkbox('dropList');
		$dropList->setLabel('Receive <a href="/forum/post/counterwallet-asset-drop-list-signup" target="_blank">occasional free tokens</a> to my Counterparty compatible address?');
		$dropList->setBool(1);
		$dropList->setValue(1);
		$form->add($dropList);		
		
		$btcAccess = new UI\Checkbox('btc_access');
		$btcAccess->setLabel('Enable API account access via verified bitcoin address?');
		$btcAccess->setBool(1);
		$btcAccess->setValue(1);
		$form->add($btcAccess);
		
		if(!$adminView){
			$pass = new UI\Password('curPassword');
			$pass->setLabel('Enter current password to complete changes');
			$pass->addAttribute('required');
			$form->add($pass);
		}
		
		if($adminView){
			$activate = new UI\Checkbox('activated');
			$activate->setBool(1);
			$activate->setValue(1);
			$activate->setLabel('Account Active?');
			$form->add($activate);
		}

		return $form;
	}
	
	protected function updateSettings($user, $data, $isAPI = false, $adminView = false)
	{
		$app = get_app('account');
		$auth_model = new Auth_Model;
		$data['admin_mode'] = $adminView;
		$data['is_api'] = $isAPI;
		$auth_model->updateAccount($user['userId'], $data);
		
		//turn this into a mod later	
		if(isset($user['affiliate']) AND !$user['affiliate'] AND isset($data['refUser']) AND trim($data['refUser']) != ''){
			$getRef = $this->fetchSingle('SELECT userId FROM users WHERE LOWER(username) = :username', array(':username' => trim(strtolower($data['refUser']))));
			if($getRef){
				//check if its on of their own referrals
				$getRef2 = $this->fetchSingle('SELECT referralId FROM user_referrals WHERE userId = :refId AND affiliateId = :userId',
											array(':refId' => $getRef['userId'], ':userId' => $user['userId']));
				if($getRef2){
					throw new \Exception('You cannot be a referral of someone you already referred!');
				}
				$refVals = array('userId' => $user['userId'], 'affiliateId' => $getRef['userId'], 'refTime' => timestamp());
				$this->insert('user_referrals', $refVals);
			
			}
			else{
				throw new \Exception('Invalid referral username');
			}
		}
		
		//turn this into a mod later
		if(isset($data['field-'.PRIMARY_TOKEN_FIELD]) AND trim($data['field-'.PRIMARY_TOKEN_FIELD]) != ''){
			$val = $data['field-'.PRIMARY_TOKEN_FIELD];
			$validate = new API\BTCValidate;
			if(!$validate->checkAddress($val)){
				throw new \Exception('Invalid bitcoin address!');
			}
			$getVal = $this->fetchSingle('SELECT * FROM user_profileVals WHERE userId = :userId AND fieldId = :fieldId',
										array(':userId' => $user['userId'], ':fieldId' => PRIMARY_TOKEN_FIELD));
										
			$getField = $this->get('profile_fields', PRIMARY_TOKEN_FIELD);
			if($getField){
				$insertData = array('value' => $val, 'lastUpdate' => timestamp());
				if($getVal){
					$update = $this->edit('user_profileVals', $getVal['profileValId'], $insertData);
				}
				else{
					//insert new one
					$insertData['userId'] = $user['userId'];
					$insertData['fieldId'] = PRIMARY_TOKEN_FIELD;
					$update = $this->insert('user_profileVals', $insertData);
				}
				
				if($update){
					$addressModel = new Tokenly\Address_Model;
					//change or insert new primary coin address
					$getAddress = $this->getAll('coin_addresses', array('userId' => $user['userId'], 'address' => $val));
				
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
		

		$meta = new \App\Meta_Model;
		if(isset($data['pubProf']) AND intval($data['pubProf']) === 1){
			$meta->updateUserMeta($user['userId'], 'pubProf', 1);
		}
		else{
			$meta->updateUserMeta($user['userId'], 'pubProf', 0);
		}
		if(isset($data['showEmail'])){
			$meta->updateUserMeta($user['userId'], 'showEmail', $data['showEmail']);
		}
		if(isset($data['emailNotify'])){
			$meta->updateUserMeta($user['userId'], 'emailNotify', $data['emailNotify']);
		}
		
		if(isset($data['btc_access'])){
			$meta->updateUserMeta($user['userId'], 'btc_access', $data['btc_access']);
		}

		$avWidth = $app['meta']['avatarWidth'];
		$avHeight = $app['meta']['avatarHeight'];
		
		
		//keep this in for API compatibility for now
		//turn this into a mod
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
		
		//turn this into a mod later
		if(isset($data['dropList'])){
			$dropGroup = $this->get('groups', 'drop-list', array(), 'slug');
			if($dropGroup){
				$inGroup = $this->getAll('group_users', array('userId' => $user['userId'], 'groupId' => $dropGroup['groupId']));
				if($inGroup AND count($inGroup) > 0){
					$inGroup = $inGroup[0];
				}
				else{
					$inGroup = false;
				}
				
				if(intval($data['dropList']) == 1){
					if(!$inGroup){
						$this->insert('group_users', array('userId' => $user['userId'], 'groupId' => $dropGroup['groupId']));
					}
				}
				else{
					if($inGroup){
						$this->sendQuery('DELETE FROM group_users WHERE userId = :userId AND groupId = :groupId',
										array(':userId' => $user['userId'], ':groupId' => $dropGroup['groupId']));
					}
				}
			}
		}
		return true;
	}
	
	protected function getSettingsInfo($user)
	{
		$meta = new \App\Meta_Model;
		$output = array('email' => $user['email']);
		$output['pubProf'] = $meta->getUserMeta($user['userId'], 'pubProf');
		$output['showEmail'] = $meta->getUserMeta($user['userId'], 'showEmail');
		$output['emailNotify'] = $meta->getUserMeta($user['userId'], 'emailNotify');
		$output['btc_access'] = $meta->getUserMeta($user['userId'], 'btc_access');
		$output['username'] = $user['username'];
		if(!isset($user['activated'])){
			$user['activated'] = 1;
		}
		$output['activated'] = $user['activated'];
		
		return $output;
	}
	
	protected function getDeleteForm()
	{
		$form = new UI\Form;

		$pass = new UI\Password('password');
		$pass->setLabel('Enter your password to close and delete your account');
		$pass->addAttribute('required');
		$form->add($pass);

		return $form;	
	}
	
	protected function deleteAccount($user, $data)
	{
		$getUser = $this->get('users', $user['userId']);
		if(!isset($data['password'])){
			throw new \Exception('Current password required to complete deletion');
		}
		
		$checkPass = hash('sha256', $getUser['spice'].$data['password']);
		if($checkPass != $getUser['password']){
			throw new \Exception('Incorrect password!');
		}
		
		$delete = $this->delete('users', $getUser['userId']);
		if(!$delete){
			throw new \Exception('Error deleting account, please try again');
		}
		
		Util\Session::clear('accountAuth');
		
		return true;
		
	}
	
	protected function checkEmailInUse($userId, $email)
	{
		$get = $this->getAll('users', array('email' => $email), array('userId'));
		if(!$get){
			return false;
		}
		foreach($get as $row){
			if($row['userId'] != $userId){
				return true;
			}
		}

		return false;
	}
}
