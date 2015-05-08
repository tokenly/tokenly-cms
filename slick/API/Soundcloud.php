<?php
namespace API;
use Core;

class Soundcloud extends Core\Model;
{
	private $api_url = 'http://api.soundcloud.com';
	
	public function downloadTrack($trackId, $path)
	{
		$url = $this->api_url.'/tracks/'.$trackId.'/stream?client_id='.SOUNDCLOUD_ID;
		$get = @file_get_contents($url);
		if(!$get){
			throw new \Exception('Error retrieving download');
		}
		if(!is_dir(dirname($path))){
			$make = @mkdir(dirname($path));
			if(!$make){
				throw new \Exception('Error creating download folder');
			}
		}
		$save = file_put_contents($path, $get);
		if(!$save){
			throw new \Exception('Error saving file');
		}
		return true;
	}
	

	
}
