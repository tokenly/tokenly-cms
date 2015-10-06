<?php
ini_set('display_errors', 1);
$_SERVER['HTTP_HOST'] = 'letstalkbitcoin.com';
$noForceSSL = true;
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');

$model = new \App\Tokenly\Distribute_Model;

$id = intval($argv[1]);
$get = $model->get('xcp_distribute', $id);

$get['addressList'] = json_decode($get['addressList'], true);
$get['txInfo'] = json_decode($get['txInfo'], true);

$missing = array();
foreach($get['addressList'] as $address => $amnt){
	$found = false;
	foreach($get['txInfo'] as $tx){
		if($tx['result']['code'] == 200 AND $tx['details'][1] == $address){
			$found = true;
			break;
		}
	}
	if(!$found){
		$missing[] = $address;
	}
}
dd($missing);

/*
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
						$notify = \App\Meta_Model::notifyUser($xcpuser['userId'], $message, $id, 'distribute-notify');
				}
		}
}
*/
