<?php
ini_set('display_errors', 1);
$noForceSSL = true;
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');
$_SERVER['HTTP_HOST'] = SITE_DOMAIN;

if(!isset($argv[1])){
	die("Must supply list of post ID\n");
}
$ids = explode(',', $argv[1]);
$pageIndex = \App\Controller::$pageIndex;
$disqus = new \API\Disqus;
$model = new \Core\Model;
$postModule = get_app('blog.post');
$site = currentSite();
$stamp = timestamp();

foreach($ids as $postId){
	$row = $model->get('blog_posts', $postId);
	if(!$row){
		continue;
	}
	$getIndex = extract_row($pageIndex, array('itemId' => $postId, 'moduleId' => $postModule['moduleId']));
	$postURL = $site['url'].'/blog/post/'.$row['url'];
	if($getIndex AND count($getIndex) > 0){
		$postURL = $site['url'].'/'.$getIndex[count($getIndex) - 1]['url'];
	}
	$commentThread = $disqus->getThread($postURL, false);
	$update = false;
	if($commentThread AND isset($commentThread['thread']['posts']) AND $commentThread['thread']['posts'] > 0){
		$update = $model->edit('blog_posts', $postId, array('commentCheck' => $stamp, 'commentCount' => $commentThread['thread']['posts']));
	}
	else{
		$update = $model->edit('blog_posts', $postId, array('commentCheck' => $stamp));
	}
	if(!$update){
		echo 'Error updating comments for post #'.$postId."\n";
	}
	else{
		echo 'Post #'.$postId." comment counts updated \n";
	}
}
