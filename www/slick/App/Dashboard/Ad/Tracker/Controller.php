<?php
class Slick_App_Dashboard_Ad_Tracker_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_Dashboard_Ad_Tracker_Model;
	}
	
	public function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'add':
					$output = $this->addURL($output);
					break;
				case 'edit':
					$output = $this->editURL($output);
					break;
				case 'delete':
					$output = $this->deleteURL($output);
					break;
				case 'view':
					$output = $this->viewURL($output);
					break;
				default:
					$output['view'] = '404';
					break;
			}
		}
		else{
			$output = $this->showURLs($output);
		}
		return $output;
	}
	
	public function showURLs($output)
	{
		$output['view'] = 'list';
		$output['urls'] = $this->model->getAll('tracking_urls', array('siteId' => $this->data['site']['siteId']));
		
		return $output;
	}
	
	public function addURL($output)
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
			catch(Exception $e){
				$add = false;
				Slick_Util_Session::flash('message', $e->getMessage(), 'error');
			}
			if($add){
				Slick_Util_Session::flash('message', 'Tracking URL created!', 'success');
				$this->redirect($this->site.'/'.$this->data['app']['url'].'/'.$this->data['module']['url']);	
				die();
			}
		}
		return $output;
	}
	
	public function editURL($output)
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
			catch(Exception $e){
				$add = false;
				Slick_Util_Session::flash('message', $e->getMessage(), 'error');
			}
			if($add){
				Slick_Util_Session::flash('message', 'Tracking URL edited!', 'success');
				$this->redirect($this->site.'/'.$this->data['app']['url'].'/'.$this->data['module']['url']);	
				die();
			}
		}
		$output['form']->setValues($getURL);
		return $output;
	}
	
	public function deleteURL($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $output;
		}
		$getURL = $this->model->get('tracking_urls', $this->args[3]);
		if($getURL AND $getURL['siteId'] == $this->data['site']['siteId']){
			$delete = $this->model->delete('tracking_urls', $getURL['urlId']);
			if($delete){
				Slick_Util_Session::flash('message', 'URL deleted!', 'success');
			}
			else{
				Slick_Util_Session::flash('message', 'Error deleting tracking URL.', 'error');
			}
		}
		$this->redirect($this->site.'/'.$this->data['app']['url'].'/'.$this->data['module']['url']);	
		die();
	}
	
	public function viewURL($output)
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
