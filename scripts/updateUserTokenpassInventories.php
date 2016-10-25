<?php
ini_set('display_errors', 1);
$noForceSSL = true;
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');
$_SERVER['HTTP_HOST'] = SITE_DOMAIN;

$model = new \Drivers\Auth\Tokenpass_Model;
$users = $model->getAll('users');
foreach($users as $user){
    if($user['auth'] == ''){
        echo 'Skipping user '.$user['username'].PHP_EOL;
        continue;
    }
    $sync = $model->syncAddresses($user);
    if($sync){
        echo 'User '.$user['username'].' inventory synced'.PHP_EOL;
    }
    else{
        echo 'Failed syncing inventory for '.$user['username'].PHP_EOL;
    }
}
