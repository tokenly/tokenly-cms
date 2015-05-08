<?php
$tca = new \App\Tokenly\TCA_Model;
$profileModule = $tca->get('modules', 'user-profile', array(), 'slug');

$checkTCA = $tca->checkItemAccess($data['notifyUser'], $profileModule['moduleId'], $data['user']['userId'], 'user-profile');
$culprit = $data['user']['username'];
if($checkTCA){
	$culprit = '<a href="'.$data['site']['url'].'/profile/user/'.$data['user']['slug'].'">'.$culprit.'</a>';
}


echo '<p>'.$culprit.' has requested a ban on the user <a href="'.$data['site']['url'].'/profile/user/'.$data['banUser']['slug'].'">'.$data['banUser']['username'].'</a>. Reason: "'.$data['banMessage'].'"</p>';
