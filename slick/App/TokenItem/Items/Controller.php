<?php
namespace App\TokenItem;
use Util;
/*
 * @module-type = dashboard
 * @menu-label = Manage Items
 * 
 * */
class Items_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Items_Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'add':
					$output = $this->container->addItem($output);
					break;
				case 'edit':
					$output = $this->container->editItem($output);
					break;
				case 'delete':
					$output = $this->container->deleteItem($output);
					break;
				default:
					$output['view'] = '404';
					break;
			}
		}
		else{
			$output = $this->container->showItems($output);
		}
		return $output;
	}
	
	protected function showItems($output)
	{
		$output['view'] = 'list';
		$output['token_items'] = $this->model->fetchAll('SELECT * FROM token_items ORDER BY rank ASC, name ASC');
		
		return $output;
	}
	
	protected function addItem($output)
	{
		$output['view'] = 'form';
		$output['form'] = $this->model->getItemForm();
		$output['formType'] = 'Add';
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->addTokenItem($data);
			}
			catch(\Exception $e){
				$add = false;
				Util\Session::flash('message', $e->getMessage(), 'error');
			}
			if($add){
				Util\Session::flash('message', 'Token Item created!', 'success');
				redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url']);	
			}
		}
		return $output;
	}
	
	protected function editItem($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $output;
		}
		$getItem = $this->model->get('token_items', $this->args[3]);
		if(!$getItem){
			$output['view'] = '404';
			return $output;
		}		
		$output['token_item'] = $getItem;
		$output['view'] = 'form';
		$output['form'] = $this->model->getItemForm();
		$output['formType'] = 'Edit';
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->editTokenItem($getItem['id'], $data);
			}
			catch(\Exception $e){
				$add = false;
				Util\Session::flash('message', $e->getMessage(), 'error');
			}
			if($add){
				Util\Session::flash('message', 'Token Item edited!', 'success');
				redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url']);	
			}
		}
		$getItem['min_token'] = round($getItem['min_token'] / SATOSHI_MOD, 8);
		$getItem = array_merge($getItem, $this->model->getItemFormProperties($getItem['id']));
		$output['form']->setValues($getItem);
		return $output;
	}
	
	protected function deleteItem($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $output;
		}
		$getItem = $this->model->get('token_items', $this->args[3]);
		if($getItem){
			$delete = $this->model->delete('token_items', $getItem['id']);
			if($delete){
				Util\Session::flash('message', 'Token Item deleted!', 'success');
			}
			else{
				Util\Session::flash('message', 'Error deleting Token Item.', 'error');
			}
		}
		redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url']);	
	}

}
