<?php
$getItem = $data['item'];

$tca = new \App\Tokenly\TCA_Model;
$profileModule = $tca->get('modules', 'user-profile', array(), 'slug');

$checkTCA = $tca->checkItemAccess($data['notifyUser'], $profileModule['moduleId'], $data['user']['userId'], 'user-profile');
$culprit = $data['user']['username'];
if($checkTCA){
	$culprit = '<a href="'.$data['site']['url'].'/profile/user/'.$data['user']['slug'].'">'.$culprit.'</a>';
}
$type = '';
if(isset($data['type'])){
	$type = $data['type'];
}
elseif(isset($_POST['type'])){
	$type = $_POST['type'];
}
$reportMessage = '';
switch($type){
	case 'topic':
	case 'thread':
		$reportMessage = ' the thread <a href="'.$data['site']['url'].'/'.$data['app']['url'].'/'.$data['module']['url'].'/'.$getItem['url'].'">'.$getItem['title'].'</a>';
		break;
	case 'post':
		if($getItem){
			$getTopic = $getItem['topic'];
			$getPoster = $getItem['poster'];
			$postPage = $getItem['postPage'];
			
			$reportMessage = ' a post by <a href="'.$data['site']['url'].'/profile/user/'.$getPoster['slug'].'">'.$getPoster['username'].'</a> in the thread <a href="'.$data['site']['url'].'/'.$data['app']['url'].'/'.$data['module']['url'].'/'.$getTopic['url'].'?page='.$postPage.'#post-'.$getItem['postId'].'">'.$getTopic['title'].'</a>';
		
		}
		break;
}
echo '<p>'.$culprit.' has flagged/reported '.$reportMessage.' - please investigate.</p>';
