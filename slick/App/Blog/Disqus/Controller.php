<?php
namespace App\Blog;
/*
 * @module-type = dashboard
 * @menu-label = Disqus Comments
 * 
 * */
class Disqus_Controller extends \App\ModControl
{
	function __construct()
	{
		parent::__construct();
	}
	
    protected function init()
    {
		$output = parent::init();
        $output['view'] = 'index';
        $output['template'] = 'admin';

        return $output;
    }
}
