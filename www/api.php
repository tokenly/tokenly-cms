<?php
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');
session_start();

$api = new Slick_App_API_Controller;
$api->init();


