<?php


\Util\Filter::addFilter('App\Controller', 'init',
	function(){
		$get = \Util\Session::get('affiliate-ref');
		if(isset($_GET['ref'])){
			\Util\Session::set('affiliate-ref', $_GET['ref']);
		}
	}, true);


$auth_driver = \Util\Driver::driverName('auth');

function apply_referral($userId, $args)
{
	$data = $args[0];
	$aff_ref = \Util\Session::get('affiliate-ref');
	if(isset($data['isAPI'])){
		if(!isset($data['site_referral'])){
			$data['site_referral'] = 'api';
		}
		if(isset($data['affiliate_ref'])){
			$aff_ref = $data['affiliate_ref'];
		}
	}		
	$meta = new \App\Meta_Model;
	if(isset($data['site_referral'])){
		$meta->updateUserMeta($userId, 'site_referral', trim(htmlentities(strip_tags($data['site_referral']))));
	}
	
	if($aff_ref){
		$getRef = $meta->get('user_meta', $aff_ref, array('userId'), 'metaValue');
		if($getRef){
			$meta->insert('user_referrals', array('userId' => $userId, 'affiliateId' => $getRef['userId'], 'refTime' => timestamp()));
			\Util\Session::clear('affiliate-ref');
		}
	}
	return $userId;
}

if($auth_driver == 'tokenpass'){
	\Util\Filter::addFilter('Drivers\Auth\Tokenpass_Model', 'generateUser', 'apply_referral');
}

\Util\Filter::addFilter('Drivers\Auth\\'.ucfirst($auth_driver).'_Model', 'registerAccount', 'apply_referral');	

