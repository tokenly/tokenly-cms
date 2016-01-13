<?php
namespace App\RSS;
use Core;
class Proxy_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Core\Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		ob_end_clean();
			
		if(isset($this->args[2])){
			$getFeed = $this->model->get('proxy_url', $this->args[2], array(), 'slug');
			if($getFeed){
				$opts = array(
				  'http'=>array(
					'method'=>"GET",
					'header'=>"FromLink: ".$this->data['site']['url'].'/'.$this->data['app']['url'].'/'.$this->data['module']['url'].'/'.$getFeed['slug']."\r\n"
				  )
				);

				$context = stream_context_create($opts);
				$get = file_get_contents($getFeed['url'], false, $context);
				if($get){
					header('Content-type: application/xml');
					echo trim($get);
					die();
				}
			}
		}
		$output['view'] = '404';
		return $output;
	}
}
