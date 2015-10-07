<?php
$_SERVER['HTTP_HOST'] = '';
ini_set('display_errors', 1);
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');

$btc = new \API\Bitcoin(BTC_CONNECT);
$address = $argv[1];

$combine = $btc->combineaddressutxos($address);
var_dump($combine);
