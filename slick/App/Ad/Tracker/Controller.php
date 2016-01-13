<?php
namespace App\Ad;
use Util;
/*
 * @module-type = dashboard
 * @menu-label = URL Tracker
 * 
 * */
class Tracker_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Tracker_Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'add':
					$output = $this->container->addURL($output);
					break;
				case 'edit':
					$output = $this->container->editURL($output);
					break;
				case 'delete':
					$output = $this->container->deleteURL($output);
					break;
				case 'view':
					$output = $this->container->viewURL($output);
					break;
				default:
					$output['view'] = '404';
					break;
			}
		}
		else{
			$output = $this->container->showURLs($output);
		}
		return $output;
	}
	
	protected function showURLs($output)
	{
		$output['view'] = 'list';
		$output['urls'] = $this->model->getAll('tracking_urls', array('siteId' => $this->data['site']['siteId']));
		
		return $output;
	}
	
	protected function addURL($output)
	{
		$output['view'] = 'form';
		$output['form'] = $this->model->getURLForm();
		$output['formType'] = 'Add';
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			$data['userId'] = $this->data['user']['userId'];
			try{
				$add = $this->model->addTrackingURL($data);
			}
			catch(\Exception $e){
				$add = false;
				Util\Session::flash('message', $e->getMessage(), 'error');
			}
			if($add){
				Util\Session::flash('message', 'Tracking URL created!', 'success');
				redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url']);	
			}
		}
		return $output;
	}
	
	protected function editURL($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $output;
		}
		$getURL = $this->model->get('tracking_urls', $this->args[3]);
		if(!$getURL  OR $getURL['siteId'] != $this->data['site']['siteId']){
			$output['view'] = '404';
			return $output;
		}		
		$output['tracking_url'] = $getURL;
		$output['view'] = 'form';
		$output['form'] = $this->model->getURLForm();
		$output['formType'] = 'Edit';
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->editTrackingURL($getURL['urlId'], $data);
			}
			catch(\Exception $e){
				$add = false;
				Util\Session::flash('message', $e->getMessage(), 'error');
			}
			if($add){
				Util\Session::flash('message', 'Tracking URL edited!', 'success');
				redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url']);	
			}
		}
		$output['form']->setValues($getURL);
		return $output;
	}
	
	protected function deleteURL($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $output;
		}
		$getURL = $this->model->get('tracking_urls', $this->args[3]);
		if($getURL AND $getURL['siteId'] == $this->data['site']['siteId']){
			$delete = $this->model->delete('tracking_urls', $getURL['urlId']);
			if($delete){
				Util\Session::flash('message', 'URL deleted!', 'success');
			}
			else{
				Util\Session::flash('message', 'Error deleting tracking URL.', 'error');
			}
		}
		redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url']);	
	}
	
	protected function viewURL($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $output;
		}
		$getURL = $this->model->get('tracking_urls', $this->args[3]);
		if(!$getURL OR $getURL['siteId'] != $this->data['site']['siteId']){
			$output['view'] = '404';
			return $output;
		}
		
		$output['tracking_url'] = $getURL;
		$output['tracking_url']['user'] = $this->model->get('users', $getURL['userId'], array('userId', 'username', 'slug'));
		$output['clicks'] = $this->model->getAll('tracking_clicks', array('urlId' => $getURL['urlId']), array(), 'clickId', 'desc');
		foreach($output['clicks'] as &$click){
			if($click['userId'] == 0){
				$click['user'] = false;
			}
			else{
				$click['user'] = $this->model->get('users', $click['userId'], array('userId', 'username', 'slug'));
			}
		}
		$output['view'] = 'stats';
		return $output;
	}
}
