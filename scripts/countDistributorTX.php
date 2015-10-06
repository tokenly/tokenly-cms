<?php
ini_set('display_errors', 0);
$_SERVER['HTTP_HOST'] = 'letstalkbitcoin.com';
$noForceSSL = true;
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');

$model = new \Core\Model;

$distros = $model->getAll('xcp_distribute', array('complete' => 1));
$count = 0;
foreach($distros as &$distro){
	$distro['txInfo'] = json_decode($distro['txInfo'], true);
	foreach($distro['txInfo'] as $info){
		if(isset($info['result']) AND isset($info['result']['code'])){
			if($info['result']['code'] == 200){
				$count++;
			}
		}
	}	
}

echo $count."\n";
