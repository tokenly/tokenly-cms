<?php
class Slick_App_Dashboard_StoreProduct_Controller extends Slick_App_ModControl
{
    public $data = array();
    public $args = array();

    function __construct()
    {
        parent::__construct();
        $this->model = new Slick_App_Dashboard_StoreProduct_Model;    
    }
    
    public function init()
    {
		$output = parent::init();
		/*
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->showStoreCategories();
					break;
				case 'add':
					$output = $this->addStoreCategory();
					break;
				case 'edit':
					$output = $this->editStoreCategory();
					break;
				case 'delete':
					$output = $this->deleteStoreCategory();
					break;
				default:
					$output = $this->showStoreCategories();
					break;
			}
		}
		else{
			$output = $this->showStoreCategories();
		}
		* */
		$output['template'] = 'admin';
		$output['view'] = 'list';
        
        return $output;
    }
}
