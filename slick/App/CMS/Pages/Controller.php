<?php
namespace App\CMS;
/*
 * @module-type = dashboard
 * @menu-label = Pages
 * 
 * */
class Pages_Controller extends \App\ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        $this->model = new Pages_Model;
    }
    
    public function init()
    {
		$output = parent::init();
        
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->showPages();
					break;
				case 'add':
					$output = $this->addPage();
					break;
				case 'edit':
					$output = $this->editPage();
					break;
				case 'delete':
					$output = $this->deletePage();
					break;
				default:
					$output = $this->showPages();
					break;
			}
		}
		else{
			$output = $this->showPages();
		}
		$output['template'] = 'admin';
        
        return $output;
    }
    
    private function showPages()
    {
		$output = array('view' => 'list');
		$getPages = $this->model->getAll('pages', array('siteId' => $this->data['site']['siteId']));
		$output['pageList'] = $getPages;

		return $output;
	}
	
	private function addPage()
	{
		$output = array('view' => 'form');
		$output['form'] = $this->model->getPageForm(0, $this->data['themeData']);
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->addPage($data);
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

	
	private function editPage()
	{
		if(!isset($this->args[3])){
			redirect($this->site);
		}
		
		$getPage = $this->model->get('pages', $this->args[3]);
		if(!$getPage){
			redirect($this->site.$this->moduleUrl);
		}
		
		$output = array('view' => 'form');
		$output['form'] = $this->model->getPageForm($this->args[3], $this->data['themeData']);
		$output['formType'] = 'Edit';
		$output['thisPage'] = $getPage;
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->editPage($this->args[3], $data);
			}
			catch(\Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				redirect($this->site.$this->moduleUrl);
			}
			
		}
		$output['form']->setValues($getPage);
		return $output;
	}

	private function deletePage()
	{
		if(isset($this->args[3])){
			$getPage = $this->model->get('pages', $this->args[3]);
			if($getPage){
				$delete = $this->model->delete('pages', $this->args[3]);
			}			
		}
		redirect($this->site.$this->moduleUrl);
	}
}
