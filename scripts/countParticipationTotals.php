<?php
ini_set('display_errors', 1);
$_SERVER['HTTP_HOST'] = 'letstalkbitcoin.com';
$noForceSSL = true;
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');


$stats = new \Tags\LTBStats;
$meta = new \App\Meta_Model;

$update_users = array();

$pop_types = array('pop', 'poq', 'pov', 'content');
foreach($pop_types as $type){
	$pop_leaders = $stats->getLeaderboardData($type);
	foreach($pop_leaders as $k => $leader){
		if(!isset($update_users[$leader['userId']])){
			$update_users[$leader['userId']] = array();
		}
		$update_users[$leader['userId']][$type] = $leader['score'];
		$update_users[$leader['userId']][$type.'_rank'] = $k+1;
	}
}

foreach($update_users as $userId => $scores){
	foreach($scores as $type => $score){
		if(strpos($type, '_rank')){
			$meta->updateUserMeta($userId, $type.'_cache', $score);
		}
		else{
			$meta->updateUserMeta($userId, $type.'_score_cache', $score);
		}
	}
	echo $userId." updated \n";
}
