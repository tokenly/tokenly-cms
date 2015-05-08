<?php
$tca = new \App\Tokenly\TCA_Model;
$profileModule = $tca->get('modules', 'user-profile', array(), 'slug');

$checkTCA = $tca->checkItemAccess($data['editorId'], $profileModule['moduleId'], $data['user']['userId'], 'user-profile');
$culprit = $data['user']['username'];
if($checkTCA){
	$culprit = '<a href="'.$data['site']['url'].'/profile/user/'.$data['user']['slug'].'">'.$culprit.'</a>';
}

$message = 'A blog post by '.$culprit.' has 
			been marked as ready for publishing, please review (or pass along to the appropriate person): 
			<a href="'.$data['site']['url'].'/dashboard/submissions/edit/'.$data['post']['postId'].'" target="_blank">'.$data['post']['title'].'</a>';

echo $message;
