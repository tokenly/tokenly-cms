<?php

$tca = new \App\Tokenly\TCA_Model;
$profileModule = $tca->get('modules', 'user-profile', array(), 'slug');

$checkTCA = $tca->checkItemAccess($data['toUser'], $profileModule['moduleId'], $data['user']['userId'], 'user-profile');
$culprit = $data['user']['username'];
if($checkTCA){
	$culprit = '<a href="'.$data['site']['url'].'/profile/user/'.$data['user']['slug'].'">'.$culprit.'</a>';
}

echo '<p>'.$culprit.' has sent you a message:
				<a href="'.$data['site']['url'].'/'.$data['app']['url'].'/'.$data['module']['url'].'/view/'.$data['messageId'].'#message">'.$data['subject'].'</a></p>';
