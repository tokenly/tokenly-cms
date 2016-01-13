<?php
namespace App\API\V1;
class Tca_Controller extends \Core\Controller
{
	public $methods = array('GET');
	
	function __construct()
	{
		parent::__construct();
		$this->model = new \App\Tokenly\TCA_Model;
	}
	
	protected function init($args = array())
	{
		$this->args = $args;
		$output = array();
			
		if(isset($args[1])){
			switch($args[1]){
				case 'check':
					$output = $this->container->checkTokenAccess();
					break;
				default:
					http_response_code(400);
					$output['error'] = 'Invalid Request';
					break;
				
			}
		}
		else{
			http_response_code(400);
			$output['error'] = 'Invalid Request';
		}	
		return $output;
	}
	
	protected function checkTokenAccess()
	{
		$output = array('result' => false);
		$http_code = 200;
		
		if(!isset($this->args[2])){
			$http_code = 400;
			$output['error'] = 'Username required';
		}
		$getUser = $this->model->get('users', $this->args[2], array(), 'username');
		if(!$getUser){
			$http_code = 404;
			$output['error'] = 'Username not found';
		}
		if($http_code == 200){
			$input = $this->args['data'];
			if(isset($input['user'])){
				unset($input['user']);
			}
			if(isset($input['site'])){
				unset($input['site']);
			}
			$ops = array();
			$stack_ops = array();
			$checks = array();			
			foreach($input as $k => $v){
				$exp_k = explode('_', $k);
				$k2 = 0;
				if(isset($exp_k[1])){
					$k2 = intval($exp_k[1]);
				}
				if($exp_k[0] == 'op'){
					$ops[$k2] = $v;
				}
				elseif($exp_k[0] == 'stackop'){
					$stack_ops[$k2] = strtoupper($v);
				}
				else{
					$checks[] = array('asset' => strtoupper($k), 'amount' => floatval($v)); 
				}
			}
			$full_stack = array();
			foreach($checks as $k => $row){
				$stack_item = $row;
				if(isset($ops[$k])){
					$stack_item['op'] = $ops[$k];
				}
				else{
					$stack_item['op'] = '>='; //default to greater or equal than
				}
				if(isset($stack_ops[$k])){
					$stack_item['stackOp'] = $stack_ops[$k];
				}
				else{
					$stack_item['stackOp'] = 'AND';
				}
				$full_stack[] = $stack_item;
			}
			$output['result'] = $this->model->checkAccess($getUser['userId'], $full_stack);
		}
		http_response_code($http_code);
		return $output;
	}
}
