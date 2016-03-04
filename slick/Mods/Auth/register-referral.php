<?php


\Util\Hook::addHook('App\AppControl', 'init',
	function(){
		if(isset($_GET['ref'])){
			\Util\Session::set('affiliate-ref', $_GET['ref']);
		}
	});

\Util\Filter::addFilter('Drivers\Auth\Native_Model', 'registerAccount', 
	function($userId, $args){
		
		$data = $args[0];
		
		if(isset($data['isAPI'])){
			if(!isset($data['site_referral'])){
				$data['site_referral'] = 'api';
			}
		}		
		$meta = new \App\Meta_Model;
		if(isset($data['site_referral'])){
			$meta->updateUserMeta($userId, 'site_referral', trim(htmlentities(strip_tags($data['site_referral']))));
		}
		
		$aff_ref = \Util\Session::get('affiliate-ref');
		if($aff_ref){
			$getRef = $this->get('user_meta', $aff_ref, array('userId'), 'metaValue');
			if($getRef){
				$meta->insert('user_referrals', array('userId' => $userId, 'affiliateId' => $getRef['userId'], 'refTime' => timestamp()));
				\Util\Session::clear('affiliate-ref');
			}
		}
		
		return $userId;
	});
