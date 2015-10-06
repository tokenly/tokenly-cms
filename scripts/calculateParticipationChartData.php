<?php
ini_set('display_errors', 1);
$_SERVER['HTTP_HOST'] = 'letstalkbitcoin.com';
$noForceSSL = true;
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');

$tokenly_app = get_app('tokenly');
$stats = new \Tags\LTBStats;
$meta = new \App\Meta_Model;

$types = array('pop', 'poq', 'pov');
foreach($types as $type){
	$pop_pool = $stats->getPoolData($type, true);
	$limit = 16;
	$num = 0;
	$average_points = array();
	$user_points = array();
	foreach($pop_pool as $week){
		$num++;
		if($num > $limit){
			break;
		}
		$average_points[$week['distribute']['completeDate']] = ($week['report']['totalPoints'] / count($week['report']['info']));
		foreach($week['report']['info'] as $user){
			if(!isset($user_points[$user['userId']])){
				$user_points[$user['userId']] = array();
			}
			$user_points[$user['userId']][$week['distribute']['completeDate']] = $user['score'];
		}
	}

	$save_average = $meta->updateAppMeta($tokenly_app['appId'], $type.'_chart_averages', json_encode($average_points));
	if($save_average){
		echo "$type chart averages updated!\n";
	}
	else{
		echo "Error saving $type chart averages\n";
		die();
	}
	foreach($user_points as $userId => $points){
		$save_user = $meta->updateUserMeta($userId, $type.'_chart_scores', json_encode($points));
		if($save_user){
			echo $userId." updated \n";
		}
		else{
			echo 'Error updating user #'.$userId."\n";
		}
	}
}

