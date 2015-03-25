<?php
ini_set('display_errors', 0);
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');

include(SITE_PATH.'/resources/qrcode.php');

if(!isset($_GET['q'])){
	die();
}
QRcode::png($_GET['q']);
