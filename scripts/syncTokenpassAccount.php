<?php
ini_set('display_errors', 1);
$noForceSSL = true;
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');
$_SERVER['HTTP_HOST'] = SITE_DOMAIN;

if(!isset($argv[1])){
    die('User required'.PHP_EOL);
}

$model = new Drivers\Auth\Tokenpass_Model;

$id = $argv[1];

$get = $model->fetchSingle('SELECT * FROM users WHERE username = :username OR email = :email OR userId = :id OR slug = :slug',
                        array(':username' => $id, ':email' => $id, ':id' => $id, ':slug' => $id));

if(!$get){
    die('User not found'.PHP_EOL);
}


$sync = $model->syncAddresses($get);

var_dump($sync);

echo '..Done'.PHP_EOL;
