<?php
ini_set('display_errors', 1);
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');

$meta = new \App\Meta_Model;
$get = $meta->fetchAll('SELECT DISTINCT(userId) FROM forum_topics WHERE trollPost = 0');
$get2 = $meta->fetchAll('SELECT DISTINCT(userId) FROM forum_posts WHERE trollPost = 0');

$user_list = array();
foreach($get as $row){
	$user_list[] = $row['userId'];
}
foreach($get2 as $row){
	if(!in_array($row['userId'], $user_list)){
		$user_list[] = $row['userId'];
	}
}
foreach($user_list as $userId){
	$meta->updateUserMeta($userId, 'forum_approved', 1);
	echo $userId.' updated'.PHP_EOL;
}
