<?php

/**
*
* runs whenever a new class is called that hasnt been included
* in the page yet
* returns void
*
*/
function slick_autoload($class)
{
	$explode = explode('\\', $class);
	$classDir = FRAMEWORK_PATH.'/';
	$total = count($explode);
	$num = 1;
	foreach($explode as $folder){
		$classDir .= str_replace('_', '/', $folder);
		if($num == $total){
			$classDir .= '.php';
		}
		else{
			$classDir .= '/';
		}
		$num++;
	}
	$fullPath = $classDir;
	if(file_exists($fullPath)){
		include($fullPath);
	}
}
spl_autoload_register('slick_autoload');


//register composer autoloader as secondary autoload
require SITE_BASE.'/vendor/autoload.php';
include(FRAMEWORK_PATH.'/functions.php');
