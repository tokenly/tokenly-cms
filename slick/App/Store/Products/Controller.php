<?php
namespace App\Store;
/*
 * @module-type = dashboard
 * @menu-label = Manage Products
 * 
 * */
class Products_Controller extends \App\ModControl
{
    function __construct()
    {
        parent::__construct();
        $this->model = new Products_Model;    
    }
    
    protected function init()
    {
		$output = parent::init();
		/*
        if(isset($this->args[2])){
			switch($this->args[2]){
				case 'view':
					$output = $this->container->showStoreCategories();
					break;
				case 'add':
					$output = $this->container->addStoreCategory();
					break;
				case 'edit':
					$output = $this->container->editStoreCategory();
					break;
				case 'delete':
					$output = $this->container->deleteStoreCategory();
					break;
				default:
					$output = $this->container->showStoreCategories();
					break;
			}
		}
		else{
			$output = $this->container->showStoreCategories();
		}
		* */
		$output['template'] = 'admin';
		$output['view'] = 'list';
        
        return $output;
    }
}
