<?php
namespace App\Store;
class Controller extends App\AppControl
{
    function __construct()
    {
        parent::__construct();
        $this->model = new Model;
    }
    
    protected function init()
    {
		$output = parent::init();
		if(!$output['module']){
			$output['view'] = '404';
		}
		return $output;
    }
    
    protected function __install($appId)
    {
		parent::__install($appId);
		$meta = new \App\Meta_Model;
		$meta->updateAppMeta($appId, 'store-title', '', 'Store Title', 1);
		$meta->updateAppMeta($appId, 'productsPerPage', '', 'Products Per Page', 1);
	}
}
