<?php
ini_set('display_errors', 0);
header('Access-Control-Allow-Origin: *');
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');
session_start();

$api = new \App\API\Controller;
$api->init();


