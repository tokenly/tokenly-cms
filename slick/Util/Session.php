<?php
/**
* Session Utility
*
* A collection of functions / wrappers for various session based functionality
*
* @package [Util]
* @author Nick Rathman <nrathman@ironcladtech.ca>
* 
*/
class Slick_Util_Session
{
	protected static $flashKey = 'flash';
	
	function __construct()
	{
		
	}
	
	/**
	* Saves a temporary message to session for use in next page
	*
	* @param $name string - key name for message
	* @param $message string
	* @param $type string - saves an additional field as message "type" (e.g success, error)
	* @return void
	*/
	public static function flash($name, $message = '', $type = '')
	{
		$_SESSION[self::$flashKey.'-'.$name] = $message;
		if($type != ''){
			$_SESSION[self::$flashKey.'-'.$name.'-type'] = $type;
		}
	}
	
	/**
	* Retrieves a message set with flash() function and immedietely unsets it from session
	* 
	* @param $name string - key name for message
	* @return string|false
	*/
	public static function getFlash($name)
	{
		if(!isset($_SESSION[self::$flashKey.'-'.$name])){
			return false;
		}
		$message = $_SESSION[self::$flashKey.'-'.$name];
		unset($_SESSION[self::$flashKey.'-'.$name]);
		return $message;
	}
	
	
}
