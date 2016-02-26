<?php
namespace Util;
class Upload
{

	public function uploadFile($file, $fileType, $location, $preserveName = 0)
	{
		if(is_string($file)){
			if(!isset($_FILES[$file])){
				return false;
			}
			$file = $_FILES[$file];
		}
		$getMime = get_mime($file['tmp_name']);
		
		switch($fileType){
			case 'image':
				$extensions = array('jpg', 'jpeg', 'png', 'gif');
				break;
			default:
				$extensions = array();
				break;
		}
		$blackList = array('php', 'js', 'html', 'htm', 'phtml', 'php4', 'php3', 'phps', 'php5', 'xml', 'sh', 'xhtml', 'htaccess'); 
		
		$explodeExt = explode('.', $file['name']);
		$countExt = count($explodeExt);
		$ext = strtolower($explodeExt[$countExt - 1]);
		
		foreach($blackList as $black){
			if($ext == $black){
				throw new \Exception('Illegal File Type'); 
				return false;
			}
		}
		
		if(!empty($extensions)){
			$found = 0;
			foreach($extensions as $extension){
				if($ext == $extension){
					$found = 1;
				}
			}
			
			if($found == 0){
				throw new \Exception('Invalid Extension');
				return false;
			}
		}
		
		if(!$fileType != ''){
			$found = 0;
			foreach($extensions as $extension){
				$expected = $fileType.'/'.$extension;
				if($getMine == $expected){
					$found = 1;
				}
			}
			
			if($found == 0){
				throw new \Exception('Invalid File Type');
				return false;
			}
		}
		
		if($preserveName == 0){
			$newName = rand(0, 100).'-'.$file['name'];
		}
		else{
			$newName = mb_convert_encoding($file['name'], 'UTF-8');;
		}
		$path = SITE_PATH.'/files/'.$location;

		$file['name'] = $newName;

		if(move_uploaded_file($file['tmp_name'], $path.'/'.$newName)){
			return $newName;
		}
		else{
			throw new \Exception('Error uploading file');
			return false;
		}

	}
	
	public static function downloadFile($path, $speed = null, $multipart = true)
	{
		while (ob_get_level() > 0)
		{
			ob_end_clean();
		}

		if (is_file($path = realpath($path)) === true)
		{
			$file = @fopen($path, 'rb');
			$size = sprintf('%u', filesize($path));
			$speed = (empty($speed) === true) ? 1024 : floatval($speed);

			if (is_resource($file) === true)
			{
				set_time_limit(0);

				if (strlen(session_id()) > 0)
				{
					session_write_close();
				}

				if ($multipart === true)
				{
					$range = array(0, $size - 1);

					if (array_key_exists('HTTP_RANGE', $_SERVER) === true)
					{
						$range = array_map('intval', explode('-', preg_replace('~.*=([^,]*).*~', '$1', $_SERVER['HTTP_RANGE'])));

						if (empty($range[1]) === true)
						{
							$range[1] = $size - 1;
						}

						foreach ($range as $key => $value)
						{
							$range[$key] = max(0, min($value, $size - 1));
						}

						if (($range[0] > 0) || ($range[1] < ($size - 1)))
						{
							header(sprintf('%s %03u %s', 'HTTP/1.1', 206, 'Partial Content'), true, 206);
						}
					}

					header('Accept-Ranges: bytes');
					header('Content-Range: bytes ' . sprintf('%u-%u/%u', $range[0], $range[1], $size));
				}

				else
				{
					$range = array(0, $size - 1);
				}

				header('Pragma: public');
				header('Cache-Control: public, no-cache');
				header('Content-Type: application/octet-stream');
				header('Content-Length: ' . sprintf('%u', $range[1] - $range[0] + 1));
				header('Content-Disposition: attachment; filename="' . basename($path) . '"');
				header('Content-Transfer-Encoding: binary');

				if ($range[0] > 0)
				{
					fseek($file, $range[0]);
				}

				while ((feof($file) !== true) && (connection_status() === CONNECTION_NORMAL))
				{
					echo fread($file, round($speed * 1024)); flush(); sleep(1);
				}

				fclose($file);
			}

			exit();
		}

		else
		{
			header(sprintf('%s %03u %s', 'HTTP/1.1', 404, 'Not Found'), true, 404);
		}

		return false;
	}

}
