<?php
namespace App\CMS;
/*
 * @module-type = dashboard
 * @menu-label = Stats
 * 
 * */
class Stats_Controller extends \App\ModControl
{
    public $data = array();
    public $args = array();
    
    function __construct()
    {
        parent::__construct();
        $this->model = new Stats_Model;
    }
    
    protected function init()
    {
		$output = parent::init();
		$output['view'] = 'stats';
		$output['template'] = 'admin';
		$output['stats'] = $this->model->getStats();
        
        return $output;
    }
}
