<?php
namespace App\Account;
class Controller extends \App\AppControl
{
    function __construct()
    {
        parent::__construct();
    }
    
    protected function init()
    {
		$output = parent::init();
		return $output;
    }
    
	protected function __install($appId)
	{
		$update = parent::__install($appId);
		if(!$update){
			return false;
		}
		$meta = new \App\Meta_Model;
		$meta->updateAppMeta($appId, 'avatarWidth', 150, 'Avatar Width (px)', 1);
		$meta->updateAppMeta($appId, 'avatarHeight', 150, 'Avatar Height (px)', 1);
		$meta->updateAppMeta($appId, 'disableRegister', 0, 'Disable New User Registration', 1, 'bool');
	}
    
}
