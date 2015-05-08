<?php
$tca = new \App\Tokenly\TCA_Model;
$profileModule = $tca->get('modules', 'user-profile', array(), 'slug');

$checkTCA = $tca->checkItemAccess($data['sub']['userId'], $profileModule['moduleId'], $data['user']['userId'], 'user-profile');
$culprit = $data['user']['username'];
if($checkTCA){
	$culprit = '<a href="'.$data['site']['url'].'/profile/user/'.$data['user']['slug'].'">'.$culprit.'</a>';
}

$notification = $culprit.' posted in the board '.
	'<a href="'.$data['site']['url'].'/'.$data['app']['url'].'/board/'.$data['board']['slug'].'">'.$data['board']['name'].'</a> '.
	'that you are subscribed to.  The topic was '.
	'<a href="'.$data['site']['url'].'/'.$data['app']['url'].'/post/'.$data['topic']['url'].$data['page'].($data['postId'] ? '#post-'.$data['postId'] : '').'">'.$data['topic']['title'].'</a>';

if (strlen($data['postContent'])) {
	$notification .= "\n";
	$notification .= "<p></p>\n";
	$notification .= markdown($data['postContent'])."\n";
}

echo $notification;

