<?php

\Util\Filter::addFilter('Drivers\Auth\Native_Model', 'getRegisterForm', 
	function($form, $args){
		
		$hny = new UI\Textbox('website');
		$hny->addClass('hny');
		$hny->setLabel('Your Website:', 'hny');
		$form->add($hny);
		
		return $form;
	});


\Util\Filter::addFilter('Drivers\Auth\Native_Model', 'registerAccount', 
	function($userId, $args){
		$data = $args[0];
		//check honeypot, mark as spammer if true
		$spammer = false;
		if(isset($data['website']) AND $data['website'] != ''){
			$spammer = true;
		}
		else{
			//check stopforumspam API
			$getSpam = @file_get_contents(STOPFORUMSPAM_API.'?email='.$data['email'].'&f=json');
			if($getSpam){
				$checkSpam = json_decode($getSpam, true);
				$spamLimit = 1;
				if(isset($checkSpam['email'])){
					if($checkSpam['email']['frequency'] >= $spamLimit){
						$spammer = true;
					}
				}
			}
		}
		if($spammer){
			//add to "troll" user group, posts auto hidden on forums
			$model = new \App\Meta_Model;
			$getTrollGroup = $model->get('groups', 'forum-troll', array(), 'slug');
			if($getTrollGroup){
				$model->insert('group_users', array('userId' => $userId, 'groupId' => $getTrollGroup['groupId']));
			}
			$model->updateUserMeta($userId, 'site_referral', 'spammer');
		}		
		return $userId;
	});

