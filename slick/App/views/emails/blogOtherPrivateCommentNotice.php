<?php
$tca = new \App\Tokenly\TCA_Model;
$profileModule = $tca->get('modules', 'user-profile', array(), 'slug');

$checkTCA = $tca->checkItemAccess($data['post']['userId'], $profileModule['moduleId'], $data['user']['userId'], 'user-profile');
$culprit = $data['user']['username'];
if($checkTCA){
	$culprit = '<a href="'.$data['site']['url'].'/profile/user/'.$data['user']['slug'].'">'.$culprit.'</a>';
}

echo '
'.$culprit.' posted an editorial comment on a blog article you  have discussed: 
	<a href="'.$data['site']['url'].'/'.$data['app']['url'].'/'.$data['module']['url'].'/edit/'.$data['post']['postId'].'#comment-'.$data['postId'].'">'.$data['post']['title'].'</a>.';
