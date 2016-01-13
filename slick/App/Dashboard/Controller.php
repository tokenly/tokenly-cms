<?php
namespace App\Dashboard;
class Controller extends \App\AppControl
{
    function __construct()
    {
        parent::__construct();
        $this->model = new Model;
    }
    
    protected function init()
    {
		$getModule = $this->model->getModuleFromArgs($this->args); //loads a app/module combo based on URL args
		$this->module = $getModule['module'];
		if($this->module){
			$this->app['url'] .= '/'.$getModule['app']['url']; //tack the app URL on to the dashboard app URL
		}
		else{
			return array('view' => '404');
		}
		unset($this->args[0]);
		$this->args = array_values($this->args); //bump the arguments back 1 index to mimic the app
		$output = parent::init();
		if(!isset($output['title'])){
			$output['title'] = $output['module']['name']; //assign default title
		}
		if($getModule['app']){
			$output['app']['url'] = $this->app['url'];
		}		
		return $output;
    }
}
