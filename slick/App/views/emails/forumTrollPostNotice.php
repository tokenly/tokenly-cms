<?php
$tca = new \App\Tokenly\TCA_Model;
$profileModule = $tca->get('modules', 'user-profile', array(), 'slug');

$checkTCA = $tca->checkItemAccess($data['sub']['userId'], $profileModule['moduleId'], $data['user']['userId'], 'user-profile');
$culprit = $data['user']['username'];
if($checkTCA){
	$culprit = '<a href="'.$data['site']['url'].'/profile/user/'.$data['user']['slug'].'">'.$culprit.'</a>';
}

	$notification = '[Mod] '.$culprit.' posted a new 
					reply in a forum topic and it may need moderator approval: <a href="'.$data['site']['url'].'/'.$data['app']['url'].'/'.$data['module']['url'].'/'.$data['topic']['url'].$data['page'].'?trollVision=1#post-'.$data['postId'].'">'.$data['topic']['title'].'</a>';


	echo $notification;
