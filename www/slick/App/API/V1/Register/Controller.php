<?php
class Slick_App_API_V1_Register_Controller extends Slick_Core_Controller
{
	public $methods = array('POST');
	
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_API_V1_Register_Model;
	}
	
	public function init($args = array())
	{
		$output = array();
		
		try{
			$create = $this->model->createAccount($args['data']);
		}
		catch(Exception $e){
			http_response_code(400);
			$output['error'] = $e->getMessage();
			return $output;
		}
		
		$output = $create;
		
		return $output;
	}
	
	
}

?>
