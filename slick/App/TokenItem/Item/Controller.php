<?php
namespace App\TokenItem;
class Item_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Items_Model;
	}
	
	protected function init()
	{
		$output = parent::init();

		if(!isset($this->args[2])){
			$output['view'] = '404';
			return $output;
		}
		
		$getItem = $this->model->get('token_items', $this->args[2], array(), 'slug');
		if(!$getItem OR $getItem['active'] == 0){
			$output['view'] = '404';
			return $output;
		}
		
		$getItem['properties'] = $this->model->getItemProperties($getItem['id']);
		
		$output['template'] = 'tokenitem';	
		$output['view'] = 'view';
		$output['token_item'] = $getItem;
		$output['title'] = 'Token Item: '.$getItem['name'];

		return $output;
	}

}
