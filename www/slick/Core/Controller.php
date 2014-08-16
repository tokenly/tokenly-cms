<?php
class Slick_Core_Controller
{
	private $script = '';

	function __construct()
	{

	}

	/**
	*
	* shortened version of header redirect
	*
	*/
	public function redirect($url, $external = 0)
	{
		header('Location: '.$url);
	}

	/**
	 *  
	 *  Set the page headers to a different type (file, json, html, image etc.)
	 * 
	 * */
	public function setHeaders($type)
	{
		
		
	}
}

?>
