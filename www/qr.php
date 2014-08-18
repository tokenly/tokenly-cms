<?php
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');

include(SITE_PATH.'/resources/qrcode.php');

if(!isset($_GET['q'])){
	die();
}
$qr =  new QR($_GET['q']);
header('Content-Type: image/gif');
echo $qr->image();
