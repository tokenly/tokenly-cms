<?php
include(FRAMEWORK_PATH.'/functions.php');
/**
*
* runs whenever a new class is called that hasnt been included
* in the page yet
* returns void
*
*/
function slick_autoload($class)
{
	$explode = explode('_', $class);

	$classDir = '';
	$total = count($explode);
	$num = 1;
	foreach($explode as $folder){

		if($num == 1){
			$folder = strtolower($folder);
		}

		$classDir .= $folder;

		if($num == $total){
			$classDir .= '.php';
		}
		else{
			$classDir .= '/';
		}
		$num++;
	}
	$fullPath = SITE_PATH.'/'.$classDir;
	if(file_exists($fullPath)){
		include($fullPath);
	}
}

spl_autoload_register('slick_autoload');

?>
