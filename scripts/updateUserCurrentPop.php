<?php
ini_set('display_errors', 1);
$noForceSSL = true;
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');
$_SERVER['HTTP_HOST'] = SITE_DOMAIN;

if(!isset($argv[1])){
	die("User ID required \n");
}

$stats = new \Tags\LTBStats;
$meta = new \App\Meta_Model;

$id = intval($argv[1]);
$user = user($id);
if(!$user){
	die("Invalid user ID \n");
}


$getScore = $stats->getUserPopScore($user['userId']);
$update = $meta->updateUserMeta($user['userId'], 'recent_pop_score', json_encode($getScore));
$update2 = $meta->updateUserMeta($user['userId'], 'recent_pop_update_time', time());
if(!$update){
	echo 'Error updating PoP score for '.$user['username']."\n";
}
else{
	echo $user['username'].' recent PoP score updated!'."\n";
}
