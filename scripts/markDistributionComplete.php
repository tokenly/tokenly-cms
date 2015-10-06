<?php
ini_set('display_errors', 1);
$_SERVER['HTTP_HOST'] = 'letstalkbitcoin.com';
$noForceSSL = true;
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');

$model = new \App\Tokenly\Distribute_Model;

$id = intval($argv[1]);
$get = $model->get('xcp_distribute', $id);

if(!$get){
	die("Distribution not found\n");
}

$get['addressList'] = json_decode($get['addressList'], true);
$get['txInfo'] = json_decode($get['txInfo'], true);


if($get['status'] == 'complete'){
	die("Distribution already complete \n");
}

$updateVals = array();
$updateVals['status'] = 'complete';
$updateVals['completeDate'] = timestamp();
$updateVals['complete'] = 1;

//notify creator
if($get['userId'] != 0){
	$_SERVER['HTTP_HOST'] = SITE_DOMAIN;
	$message = 'Your distribution of '.$get['asset'].' has been completed. <a href="/dashboard/xcp-distribute/tx/'.$get['address'].'" target="_blank">Click here to view details</a>';
	$notify = \App\Meta_Model::notifyUser($get['userId'], $message, $get['distributeId'], 'distribute-complete');
}
//notify users
foreach($get['addressList'] as $addr => $amnt){
	$lookup = $model->lookupAddress($addr);
	if($lookup){
		foreach($lookup['users'] as $xcpuser){
			if($get['divisible'] == 1){
				$amnt = $amnt / SATOSHI_MOD;
			}
			$message = 'You have received a distribution of '.$amnt.' '.$get['asset'].' to '.$addr;
			if(trim($get['name']) != ''){
				$message .= ' - '.$get['name'];
			}
			$notify = \App\Meta_Model::notifyUser($xcpuser['userId'], $message, $get['distributeId'], 'distribute-notify');
		}
	}
}

$updateTx = $model->edit('xcp_distribute', $get['distributeId'], $updateVals);
if(!$updateTx){
	echo 'Failed updating distribution tx info ['.$get['address']."] \n";
}
else{
	echo 'Distribution complete ['.$get['address'].'] - '.timestamp()."\n";
}
