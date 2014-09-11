<?php
$tca = new Slick_App_LTBcoin_TCA_Model;
$profileModule = $tca->get('modules', 'user-profile', array(), 'slug');

$checkTCA = $tca->checkItemAccess($data['post']['userId'], $profileModule['moduleId'], $data['user']['userId'], 'user-profile');
$culprit = $data['user']['username'];
if($checkTCA){
	$culprit = '<a href="'.$data['site']['url'].'/profile/user/'.$data['user']['slug'].'">'.$culprit.'</a>';
}

echo '
	posted a comment on your blog post: '.$culprit.'
	<a href="'.$data['site']['url'].'/'.$data['app']['url'].'/'.$data['module']['url'].'/'.$data['post']['url'].'#comment-'.$data['postId'].'">'.$data['post']['title'].'</a>.';
