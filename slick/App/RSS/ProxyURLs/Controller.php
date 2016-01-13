<?php
namespace App\RSS;
/*
 * @module-type = dashboard
 * @menu-label =  Proxy URLs
 * 
 * */
class ProxyURLs_Controller extends \App\ModControl
{
    function __construct()
    {
        parent::__construct();
        $this->model = new ProxyURLs_Model;
    }
    
    protected function init()
    {
		$output = parent::init();
        
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->container->showProxies();
					break;
				case 'add':
					$output = $this->container->addProxy();
					break;
				case 'edit':
					$output = $this->container->editProxy();
					break;
				case 'delete':
					$output = $this->container->deleteProxy();
					break;
				default:
					$output = $this->container->showProxies();
					break;
			}
		}
		else{
			$output = $this->container->showProxies();
		}
		$output['template'] = 'admin';
        return $output;
    }
    
    protected function showProxies()
    {
		$output = array('view' => 'list');
		$output['proxyList'] = $this->model->getAll('proxy_url');
		return $output;
	}
	
	
	protected function addProxy()
	{
		$output = array('view' => 'form');
		$output['form'] = $this->model->getProxyForm();
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->addProxy($data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->site.$this->moduleUrl);
			}	
		}
		return $output;
	}
	
	protected function editProxy()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getProxy = $this->model->get('proxy_url', $this->args[3]);
		if(!$getProxy){
			redirect($this->site.$this->moduleUrl);
		}
		
		$output = array('view' => 'form');
		$output['form'] = $this->model->getProxyForm($this->args[3]);
		$output['formType'] = 'Edit';
		
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->editProxy($this->args[3], $data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->site.$this->moduleUrl);
			}
		}
		$output['form']->setValues($getProxy);
		return $output;
	}
	
	protected function deleteProxy()
	{
		if(isset($this->args[3])){
			$getProxy = $this->model->get('proxy_url', $this->args[3]);
			if($getProxy){
				$delete = $this->model->delete('proxy_url', $this->args[3]);
			}
		}
		redirect($this->site.$this->moduleUrl);
	}
}
