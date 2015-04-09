<?php
/*
 * @module-type = dashboard
 * @menu-label = Disqus Comments
 * 
 * */
class Slick_App_Blog_Disqus_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
	}
	
    public function init()
    {

		$output = parent::init();
        $output['view'] = 'index';
        $output['template'] = 'admin';

        return $output;
    }
}
