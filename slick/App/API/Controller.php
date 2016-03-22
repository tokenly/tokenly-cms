<?php
namespace App\API;
class Controller extends \Core\Controller
{
	function __construct()
	{
		parent::__construct();
		header('Content-Type: application/json');
	}
	
	protected function init()
	{
		//load modifications
		$scan_mods = scandir(FRAMEWORK_PATH.'/Mods');
		foreach($scan_mods as $mod){
			if($mod == '.' OR $mod == '..'){
				continue;
			}
			if(is_dir(FRAMEWORK_PATH.'/Mods/'.$mod)){
				foreach (glob(FRAMEWORK_PATH.'/Mods/'.$mod.'/*.php') as $filename)
				{
					require_once($filename);
				}
			}
			elseif(substr($mod, -1, 4) == '.php'){
				require_once(FRAMEWORK_PATH.'/Mods/'.$mod);
			}
		}		
		
		$output = array();
		if(!isset($_REQUEST['v'])){
			http_response_code(400);
			$output['error'] = 'No API version selected';
		}
		else{
			switch($_REQUEST['v']){
				case '1':
				default:
					$api = new \App\API\V1\Controller;
					$output = $api->init();
					break;
			}
		}
		if(!isset($output['error']) OR $output['error'] == null){
			if(!in_array(http_response_code(), array(200,201,202))){
				http_response_code(200);
			}
		}
		ob_start();
		echo json_encode($output);
		$json = ob_get_contents();
		ob_end_clean();
		echo trim($json);
	}
}
