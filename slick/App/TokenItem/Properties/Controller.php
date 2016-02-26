<?php
namespace App\TokenItem;
use Util;
/*
 * @module-type = dashboard
 * @menu-label = Item Properties
 * 
 * */
class Properties_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Properties_Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'add':
					$output = $this->container->addProperty($output);
					break;
				case 'edit':
					$output = $this->container->editProperty($output);
					break;
				case 'delete':
					$output = $this->container->deleteProperty($output);
					break;
				default:
					$output['view'] = '404';
					break;
			}
		}
		else{
			$output = $this->container->showProperties($output);
		}
		return $output;
	}
	
	protected function showProperties($output)
	{
		$output['view'] = 'list';
		$output['properties'] = $this->model->fetchAll('SELECT * FROM token_itemPropertyTypes ORDER BY rank ASC, name ASC');
		
		return $output;
	}
	
	protected function addProperty($output)
	{
		$output['view'] = 'form';
		$output['form'] = $this->model->getPropertyForm();
		$output['formType'] = 'Add';
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->addItemProperty($data);
			}
			catch(\Exception $e){
				$add = false;
				Util\Session::flash('message', $e->getMessage(), 'error');
			}
			if($add){
				Util\Session::flash('message', 'Token Item Property Type created!', 'success');
				redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url']);	
			}
		}
		return $output;
	}
	
	protected function editProperty($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $output;
		}
		$getProp = $this->model->get('token_itemPropertyTypes', $this->args[3]);
		if(!$getProp){
			$output['view'] = '404';
			return $output;
		}		
		$output['property'] = $getProp;
		$output['view'] = 'form';
		$output['form'] = $this->model->getPropertyForm();
		$output['formType'] = 'Edit';
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->editItemProperty($getProp['id'], $data);
			}
			catch(\Exception $e){
				$add = false;
				Util\Session::flash('message', $e->getMessage(), 'error');
			}
			if($add){
				Util\Session::flash('message', 'Token Item Property Type edited!', 'success');
				redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url']);	
			}
		}
		$output['form']->setValues($getProp);
		return $output;
	}
	
	protected function deleteProperty($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $output;
		}
		$getProp = $this->model->get('token_itemPropertyTypes', $this->args[3]);
		if($getProp){
			$delete = $this->model->delete('token_itemPropertyTypes', $getProp['id']);
			if($delete){
				Util\Session::flash('message', 'Property Type deleted!', 'success');
			}
			else{
				Util\Session::flash('message', 'Error deleting Property Type.', 'error');
			}
		}
		redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url']);	
	}

}
