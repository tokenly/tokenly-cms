<?php
namespace App\API\V1;
class Register_Controller extends \Core\Controller
{
	public $methods = array('POST');
	
	function __construct()
	{
		parent::__construct();
		$this->model = new Register_Model;
	}
	
	protected function init($args = array())
	{
		$output = array();
		try{
			$create = $this->model->createAccount($args['data']);
		}
		catch(\Exception $e){
			http_response_code(400);
			$output['error'] = $e->getMessage();
			return $output;
		}
		$output = $create;
		return $output;
	}
}
