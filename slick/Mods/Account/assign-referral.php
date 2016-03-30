<?php

/* adds an option to account settings for choosing what user has referred you to the website (if none already) */

\Util\Filter::addFilter('App\Account\Settings_Model', 'getSettingsForm', 
	function($form, $args){
		
		$user = $args[0];
		
		$ref = new UI\Textbox('refUser');
		$ref->setLabel('Referred By (enter referral username)');
		if(isset($user['affiliate']) AND $user['affiliate']){
			$ref->addAttribute('disabled');
			$ref->setValue($user['affiliate']['username']);
		}
		$form->add($ref);		
		
		return $form;
	});


//edit page process code
\Util\Filter::addFilter('App\Account\Settings_Model', 'updateSettings', 
	function($result, $args){
		if(!$result){
			return false;
		}

		$user = $args[0];
		$data = $args[1];
		$model = new \Core\Model;

		if(isset($user['affiliate']) AND !$user['affiliate'] AND isset($data['refUser']) AND trim($data['refUser']) != ''){
			$getRef = $model->fetchSingle('SELECT userId FROM users WHERE LOWER(username) = :username', array(':username' => trim(strtolower($data['refUser']))));
			if($getRef AND $getRef['userId'] != $user['userId']){
				//check if its on of their own referrals
				$getRef2 = $model->fetchSingle('SELECT referralId FROM user_referrals WHERE userId = :refId AND affiliateId = :userId',
											array(':refId' => $getRef['userId'], ':userId' => $user['userId']));
				if($getRef2){
					throw new \Exception('You cannot be a referral of someone you already referred!');
				}
				$refVals = array('userId' => $user['userId'], 'affiliateId' => $getRef['userId'], 'refTime' => timestamp());
				$model->insert('user_referrals', $refVals);
			
			}
			else{
				throw new \Exception('Invalid referral username');
			}
		}

		return $result;
	});
