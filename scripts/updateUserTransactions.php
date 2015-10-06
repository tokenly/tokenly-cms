<?php
ini_set('display_errors', 1);
$noForceSSL = true;
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');
$_SERVER['HTTP_HOST'] = SITE_DOMAIN;

$id = intval($argv[1]);
$model = new \App\Tokenly\Inventory_Model;
$meta = new \App\Meta_Model;
$get = $model->get('users', intval($id));
if(!$get){
	die("Invalid user \n");
}

$check = $meta->getUserMeta($get['userId'], 'tx_list_updating');
if(intval($check) === 1){
	die("Check already in proces \n");
}
$meta->updateUserMeta($get['userId'], 'tx_list_updating', 1);
$update = $model->getUserInventoryTransactions($get['userId'], false, true);
if(!$update){
	die("Error updating transactions for ".$get['username']." (".$get['userId'].") \n");
}

$meta->updateUserMeta($get['userId'], 'tx_list_updating', 0);
echo "Updated transactions for ".$get['username']." (".$get['userId'].") \n";
