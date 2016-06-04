<?php
ini_set('display_errors', 1); //toggle PHP debugging
define('SITE_BASE', '/var/www/html'); //base path to installation, typically one level up from web root
define('SITE_PATH', SITE_BASE.'/www'); //web root
define('FRAMEWORK_PATH', SITE_BASE.'/slick');
define('SITE_NAME', ''); //default system name (not important)
define('SITE_DOMAIN', ''); //default system domain (without http://)

//mysql credentials
define('MYSQL_DB', '');
define('MYSQL_USER', '');
define('MYSQL_PASS', '');
define('MYSQL_HOST', 'localhost');

define('DATE_FORMAT', 'F j\, Y \a\t g:i A'); //default display date formatting
define('DATE_DEFAULT_TIMEZONE', 'America/Los_Angeles');

define('PRIMARY_TOKEN_FIELD', 12); //fieldId for main token profile field (LTBcoin address)

define('ENCRYPT_KEY', ''); //encryption key for encrypting some data into database

if(file_exists(SITE_BASE.'/conf/api.php')){
	include(SITE_BASE.'/conf/api.php');
}
