<?php
namespace App\Ad;
use App, Util\Session;
/*
 * @module-type = dashboard
 * @menu-label = Adspace Manager
 * 
 * */
 
 
class Adspace_Controller extends App\ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Adspace_Model;
	}
	
	protected function init()
	{
		$output = parent::init();
		$output['template'] = 'admin';
		if(isset($this->args[2])){
			switch($this->args[2]){
				case 'add':
					$output = $this->container->addAdspace($output);
					break;
				case 'edit':
					$output = $this->container->editAdspace($output);
					break;
				case 'delete':
					$output = $this->container->deleteAdspace($output);
					break;
				default:
					$output['view'] = '404';
					break;
			}
		}
		else{
			$output = $this->container->showAdspaces($output);
		}	
		
		return $output;
	}
	
	protected function showAdspaces($output)
	{
		$output['adspaces'] = $this->model->getAll('adspaces', array(), array(), 'label', 'asc');
		$output['view'] = 'list';
		return $output;
	}
	
	protected function addAdspace($output)
	{

		$output['form'] = $this->model->getAdspaceForm();
		$output['view'] = 'form';
		
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$create = $this->model->createAdspace($data);
			}
			catch(\Exception $e){
				Session::flash('message', $e->getMessage(), 'error');
				$create = false;
			}
			
			if($create){
				Session::flash('message', 'Adspace created!', 'success');
				redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url']);	
			}
		}
		
		return $output;
	}
	
	protected function editAdspace($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $output;
		}
		$get = $this->model->get('adspaces', $this->args[3]);
		if(!$get){
			$output['view'] = '404';
			return $output;
		}
		$get['items'] = json_decode($get['items'], true);		
		if(!is_array($get['items'])){
			$get['items'] = array();
		}
		$output['adspace'] = $get;
		$output['adspace']['orig_items'] = $get['items'];
		
		if(isset($this->args[4]) AND isset($this->args[5])){
			switch($this->args[4]){
				case 'edit-ad':
					$output = $this->container->editAdspaceAd($output);
					break;
				case 'delete-ad':
					$output = $this->container->deleteAdspaceAd($output);
					break;
				case 'archive-ad':
					$output = $this->container->archiveAdspaceAd($output);
					break;
				case 'unarchive-ad':
					$output = $this->container->unarchiveAdspaceAd($output);
					break;
				default:
					$output['view'] = '404';
					break;
			}
			
			return $output;
		}

		$output['main_form'] = $this->model->getAdspaceForm();
		$output['ad_form'] = $this->model->getAdForm();
		if(posted()){
			if(isset($_POST['save-ad-order']) AND isset($_POST['ad_list'])){
				//save order of ad list
				if(!is_array($_POST['ad_list'])){
					$_POST['ad_list'] = array($_POST['ad_list']);
				}
				$new_list = array();
				foreach($_POST['ad_list'] as $fk => $ak){
					if(isset($output['adspace']['items'][$ak])){
						$new_list[] = $output['adspace']['items'][$ak];
					}
				}
				$update = $this->model->edit('adspaces', $output['adspace']['adspaceId'], array('items' => json_encode($new_list)));
				if($update){
					Session::flash('message', 'List order saved!', 'success');
				}
				else{
					Session::flash('message', 'Error saving ad list order', 'error');
				}
				redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url'].'/edit/'.$get['adspaceId']);
			}
			elseif(isset($_POST['new-ad'])){
				//add a new advertisement to adspace
				$data = $output['ad_form']->grabData();
				try{
					$update = $this->model->addUrlToAdspace($get, $data);
				}
				catch(\Exception $e){
					Session::flash('message', $e->getMessage(), 'error');
					$update = false;
				}
				
				if($update){
					Session::flash('message', 'Advertisement added to adspace!', 'success');
				}
				
				redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url'].'/edit/'.$get['adspaceId']);
			}
			else{
				//save adspace settings
				$data = $output['main_form']->grabData();
				try{
					$update = $this->model->editAdspace($get['adspaceId'], $data);
				}
				catch(\Exception $e){
					Session::flash('message', $e->getMessage(), 'error');
					$update = false;
				}
				
				if($update){
					Session::flash('message', 'Adspace settings updated!', 'success');
				}
				
				redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url'].'/edit/'.$get['adspaceId']);
			}
		}
		$adspace_data = $get;
		$output['main_form']->setValues($adspace_data);
		
		$output['view'] = 'manage';
		
		$output['total_ads'] = count($output['adspace']['items']);
		$output['active_ads'] = 0;
		$output['archived_ads'] = 0;
		$time = time();
		foreach($output['adspace']['items'] as $item){
			if(isset($item['archived']) AND $item['archived'] == 1){
				$output['archived_ads']++;
			}
			else{
				if($time >= $item['start_date'] AND $time <= $item['end_date']){
					$output['active_ads']++;
				}
			}
		}
		
		$show_archived = false;
		if(isset($_GET['archive']) AND intval($_GET['archive']) === 1){
			$show_archived = true;
		}
		foreach($output['adspace']['items'] as $k => $row){
			if(!$show_archived){
				if(isset($row['archived']) AND $row['archived'] == 1){
					unset($output['adspace']['items'][$k]);
				}
			}
			else{
				if(!isset($row['archived']) OR $row['archived'] != 1){
					unset($output['adspace']['items'][$k]);
				}
			}
		}
		$output['show_archived'] = $show_archived;
		
		return $output;
	}
	
	protected function deleteAdspace($output)
	{
		if(!isset($this->args[3])){
			$output['view'] = '404';
			return $output;
		}
		$get = $this->model->get('adspaces', $this->args[3]);
		if($get){
			$delete = $this->model->delete('adspaces', $get['adspaceId']);
			if($delete){
				Session::flash('message', 'Adspace deleted!', 'success');
			}
			else{
				Session::flash('message', 'Error deleting adspace.', 'error');
			}
		}
		redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url']);	
	}
	
	protected function editAdspaceAd($output)
	{
		if(!isset($this->args[5])){
			$output['view'] = '404';
			return $output;
		}
		$found_ad = false;
		$k = intval($this->args[5]);
		if(isset($output['adspace']['items'][$k])){
			$found_ad = $output['adspace']['items'][$k];
			$url_data = $this->model->get('tracking_urls', $found_ad['urlId']);
			if(!$url_data){
				$found_ad = false;
			}
			else{
				$found_ad['data'] = $url_data;
			}
		}
		if(!$found_ad){
			$output['view'] = '404';
			return $output;
		}
		$output['ad'] = $found_ad;
		$output['ad_key'] = $k;
		$output['view'] = 'edit-ad';
		$output['form'] = $this->model->getEditAdForm();
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$update = $this->model->editAdspaceAd($output['adspace'], $k, $data);
			}
			catch(\Exception $e){
				Session::flash('message', $e->getMessage(), 'error');
				$update = false;
			}
			
			if($update){
				Session::flash('message', 'Advertisement settings updated!', 'success');
				redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url'].'/edit/'.$output['adspace']['adspaceId']);
			}
			else{
				redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url'].'/edit/'.$output['adspace']['adspaceId'].'/edit-ad/'.$k);
			}
		}
		$output['form']->setValues(array('start_date' => date('Y/m/d H:i', $output['ad']['start_date']),
										 'end_date' => date('Y/m/d H:i', $output['ad']['end_date'])));
		
		return $output;
	}
	
	
	protected function deleteAdspaceAd($output)
	{
		if(!isset($this->args[5])){
			$output['view'] = '404';
			return $output;
		}
		try{
			$delete = $this->model->deleteUrlFromAdspace($output['adspace'], intval($this->args[5]));
		}
		catch(\Exception $e){
			Session::flash('message', $e->getMessage(), 'error');
			$delete = false;
		}
		
		if($delete){
			Session::flash('message', 'Advertisment removed', 'success');
		}
		
		redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url'].'/edit/'.$output['adspace']['adspaceId']);
	}
	
	protected function archiveAdspaceAd($output, $toggle_archive = 1)
	{
		if(!isset($this->args[5])){
			$output['view'] = '404';
			return $output;
		}

		$k = intval($this->args[5]);
		if(!is_array($output['adspace']['items'])){
			$output['adspace']['items'] = json_decode($output['adspace']['items'], true);
			if(!is_array($output['adspace']['items'])){
				$output['adspace']['items'] = array();
			}
		}
		foreach($output['adspace']['items'] as $ik => $item){
			if($ik == $k){
				$output['adspace']['items'][$ik]['archived'] = $toggle_archive;
				break;
			}
		}
		
		$edit = $this->model->edit('adspaces', $output['adspace']['adspaceId'], array('items' => json_encode($output['adspace']['items'])));
		if($edit){
			$un = '';
			if($toggle_archive == 0){
				$un = 'un';
			}
			Session::flash('message', 'Adspace advertisement '.$un.'archived', 'success');
		}
		else{
			Session::flash('message', 'Error archiving ad', 'error');
		}
		$andQuery = '';
		if($toggle_archive == 0){
			$andQuery = '?archive=1';
		}
		redirect($this->site.$this->data['app']['url'].'/'.$this->data['module']['url'].'/edit/'.$output['adspace']['adspaceId'].$andQuery.'#manage-ads');		
	}
	
	protected function unarchiveAdspaceAd($output)
	{
		return $this->container->archiveAdspaceAd($output, 0);
	}	
}
