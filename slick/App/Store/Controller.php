<?php
class Slick_App_Store_Controller extends Slick_App_AppControl
{
    function __construct()
    {
        parent::__construct();
        $this->model = new Slick_App_Store_Model;
        
        
    }
    
    public function init()
    {
		$output = parent::init();
		
		if(!$output['module']){
			$output['view'] = '404';
			$output['title'] = '404 Page Not Found';
		}
		return $output;
    }
    
    public function __install($appId)
    {
		parent::__install($appId);
		
		$meta = new Slick_App_Meta_Model;
		$meta = new Slick_App_Meta_Model;
		$meta->updateAppMeta($appId, 'store-title', '', 'Store Title', 1);
		$meta->updateAppMeta($appId, 'productsPerPage', '', 'Products Per Page', 1);

		
	}
    
    
    
}
