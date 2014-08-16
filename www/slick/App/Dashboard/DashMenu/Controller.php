<?php
class Slick_App_Dashboard_DashMenu_Controller extends Slick_App_ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        
        $this->model = new Slick_App_Dashboard_DashMenu_Model;
        
        
    }
    
    public function init()
    {
		$output = parent::init();
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->showDashMenu();
					break;
				case 'add':
					
					$output = $this->addItem();
					break;
				case 'edit':
					$output = $this->editItem();
					break;
				case 'delete':
					$output = $this->deleteItem();
					break;
				default:
					$output = $this->showDashMenu();
					break;
			}
		}
		else{
			$output = $this->showDashMenu();
		}
		$output['template'] = 'admin';
        
        return $output;
    }
    
    private function showDashMenu()
    {
		$output = array('view' => 'list');
		$getDashMenu = $this->model->fetchAll('SELECT * FROM dash_menu ORDER BY dashGroup ASC, rank ASC');
		$output['itemList'] = $getDashMenu;

		
		return $output;
		
	}
	
	
	private function addItem()
	{
		$output = array('view' => 'form');
		$output['form'] = $this->model->getItemForm();
		$output['formType'] = 'Add';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->addItem($data);
			}
			catch(Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				$this->redirect($this->site.$this->moduleUrl);
				return true;
			}
			
		}
		
		return $output;
		
	}
	

	
	private function editItem()
	{
		
		if(!isset($this->args[3])){
			$this->redirect('/');
			return false;
		}
		
		$getItem = $this->model->get('dash_menu', $this->args[3]);
		if(!$getItem){
			$this->redirect($this->site.'/'.$this->moduleUrl);
			return false;
		}
		
		$output = array('view' => 'form');
		$output['form'] = $this->model->getItemForm($this->args[3]);
		$output['formType'] = 'Edit';
		
		if(posted()){
			$data = $output['form']->grabData();
			$data['siteId'] = $this->data['site']['siteId'];
			try{
				$add = $this->model->editItem($this->args[3], $data);
			}
			catch(Exception $e){
				$output['error'] = $e->getMessage();
				$add = false;
			}
			
			if($add){
				$this->redirect($this->site.$this->moduleUrl);
				return true;
			}
			
		}
		$output['form']->setValues($getItem);
		$output['form']->field('mod-params')->setValue($getItem['params']);

		return $output;
		
	}
	

	
	
	private function deleteItem()
	{
		if(!isset($this->args[3])){
			$this->redirect($this->site.$this->moduleUrl);
			return false;
		}
		
		$getItem = $this->model->get('dash_menu', $this->args[3]);
		if(!$getItem OR $getItem['active'] != 0){
			$this->redirect($this->site.$this->moduleUrl);
			return false;
		}
		
		$delete = $this->model->delete('dash_menu', $this->args[3]);
		$this->redirect($this->site.$this->moduleUrl);
		return true;
	}
	


}

?>
