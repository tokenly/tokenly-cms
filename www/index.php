<?php
require_once('../conf/config.php');
include(FRAMEWORK_PATH.'/autoload.php');
//setup session cookie to work accross subdomains
$expDomain = explode('.', $_SERVER['HTTP_HOST']);
if(count($expDomain) > 2){
	unset($expDomain[0]);
}
$domainJoin = join('.', $expDomain);
ini_set('session.cookie_domain', '.'.$domainJoin);
session_start();

//use output buffer to clean out any potential pre-output white space that creeps up
ob_start();
$ltb = new Slick_App_Controller;
$ltb->init();

$outputSite = trim(ob_get_contents());
ob_end_clean();
echo $outputSite;
