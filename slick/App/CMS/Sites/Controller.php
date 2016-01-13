<?php
namespace App\CMS;
/*
 * @module-type = dashboard
 * @menu-label = Sub-sites
 * 
 * */
class Sites_Controller extends \App\ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        $this->model = new Sites_Model;
    }
    
    protected function init()
    {
		$output = parent::init();
        
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->container->showSites();
					break;
				case 'add':
					$output = $this->container->addSite();
					break;
				case 'edit':
					$output = $this->container->editSite();
					break;
				case 'delete':
					$output = $this->container->deleteSite();
					break;
				default:
					$output = $this->container->showSites();
					break;
			}
		}
		else{
			$output = $this->container->showSites();
		}
		$output['template'] = 'admin';
        
        return $output;
    }
    
    protected function showSites()
    {
		$output = array('view' => 'siteList');
		$getSites = $this->model->getAll('sites');
		$output['siteList'] = $getSites;

		return $output;
	}
	
	
	protected function addSite()
	{
		$output = array('view' => 'siteForm');
		$output['form'] = $this->model->getSiteForm();
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->addSite($data);
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
	
	protected function editSite()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getSite = $this->model->get('sites', $this->args[3]);
		if(!$getSite){
			redirect($this->site.$this->moduleUrl);
		}
		$getSite['apps'] = $this->model->getSiteApps($this->args[3]);
		
		$output = array('view' => 'siteForm');
		$output['form'] = $this->model->getSiteForm($this->args[3]);
		$output['formType'] = 'Edit';
		$output['thisSite'] = $getSite;
		
		if(posted()){
			$data = $output['form']->grabData();
			try{
				$add = $this->model->editSite($this->args[3], $data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->site.$this->moduleUrl);
			}
		}
		$output['form']->setValues($getSite);
		return $output;
	}
	
	protected function deleteSite()
	{
		if(isset($this->args[3])){
			if($this->model->count('sites') > 1){
				$getSite = $this->model->get('sites', $this->args[3]);
				if($getSite AND $getSite['isDefault'] != 1){
					$delete = $this->model->delete('sites', $this->args[3]);
				}				
			}
		}
		redirect($this->site.$this->moduleUrl);
	}
}
