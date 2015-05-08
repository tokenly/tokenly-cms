<?php
namespace API;
require_once(SITE_PATH.'/resources/aws/aws-autoloader.php');
use Core;
use Aws\Common\Aws;
use Aws\S3\Exception\S3Exception;
use Aws\Common\Exception\MultipartUploadException;
use Aws\S3\Model\MultipartUpload\UploadBuilder;
class AWS extends Core\Model;
{
	private $aws = false;
	private $service = false;
	public $serviceType = false;
	
	function __construct($service = 's3')
	{
		$this->serviceType = $service;
		$_SERVER['AWS_ACCESS_KEY_ID'] = AWS_ACCESS_KEY;
		$_SERVER['AWS_SECRET_ACCESS_KEY'] = AWS_SECRET_KEY;
		$this->aws = Aws::factory(AWS_CONFIG_PATH);
		switch($service){
			case 's3':
			default:
				$this->service = $this->aws->get('s3');
				break;
			
		}
	}
	
	public function uploadFile($data)
	{
		if(!isset($data['file'])){
			throw new \Exception('No file set');
		}
		if(!isset($data['bucket'])){
			if(!defined('AWS_DEFAULT_BUCKET')){
				throw new \Exception('No bucket set');
			}
			$data['bucket'] = AWS_DEFAULT_BUCKET;
		}
		if(!isset($data['name'])){
			$data['name'] = basename($data['file']);
		}
		if(isset($data['folder'])){
			$data['name'] = $data['folder'].'/'.$data['name'];
		}
		$uploader = UploadBuilder::newInstance()
			->setClient($this->service)
			->setSource($data['file'])
			->setBucket($data['bucket'])
			->setKey($data['name'])
			->setOption('Metadata', array())
			->setOption('CacheControl', 'max-age=3600')
			->build();
		$success = false;
		try {
			$upload = $uploader->upload();
			$success = true;
			$upload = $upload->toArray();
			
		} catch (MultipartUploadException $e) {
			$uploader->abort();
			throw new \Exception($e->getMessage());
		}
		return $upload['Location'];
	}
	
	
	public function swapService($service)
	{
		$this->serviceType = $service;
		$this->service = $this->aws->get($service);
	}
	
	public function checkItemExists($path, $bucket = AWS_DEFAULT_BUCKET)
	{
		$response = $this->service->doesObjectExist($bucket, $path);
		return $response;
	}
	
	public function getUrl($path, $bucket = AWS_DEFAULT_BUCKET)
	{
		$response = $this->service->getObjectUrl($bucket, $path);
		return $response;
	}
}
