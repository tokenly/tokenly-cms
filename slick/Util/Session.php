<?php
namespace Util;
/**
* Session Utility
*
* A collection of functions / wrappers for various session based functionality
*
* @package [Util]
* @author Nick Rathman <nrathman@ironcladtech.ca>
* 
*/
define('APPEND_ARRAY', '0x1');
define('APPEND_STRING', '0x2');
define('APPEND_NEWLINE', '0x4');
define('APPEND_COMMA', '0x8');
define('INCREMENT_VAL', '0x16');

class Session
{
	protected static $flashKey = 'flash';
	protected static $sesh_updated = false;
	protected static $update_key = 'sesh_update';
	
	
	public static function get($name, $default = false)
	{
		if(isset($_SESSION[$name])){
			return $_SESSION[$name];
		}
		return $default;
	}
	
	public static function set($name, $value, $flag = false)
	{
		if($flag){
			$get = Session::get($name);
			switch($flag){
				case APPEND_ARRAY:
					if(is_array($get)){
						$get[] = $value;
						$value = $get;
					}
					else{
						$value = array($value);
					}
					break;
				case APPEND_STRING:
					if($get){
						$value = $get.$value;
					}
					break;
				case APPEND_NEWLINE:
					if($get){
						$value = $get.PHP_EOL.$value;
					}
					break;
				case APPEND_COMMA:
					$exp = explode(',', $get);
					$exp[] = $value;
					$value = join(',', $exp);
					break;
				case INCREMENT_VAL:
					if(!$get OR is_int($get)){
						$value = intval($get) + $value;
					}
					else{
						$value = floatval($get) + $value;
					}
					break;
			}
		}
		$_SESSION[$name] = $value;
		if(!self::$sesh_updated){
			self::$sesh_updated = true;
			$time = time();
			setcookie(self::$update_key, $time, $time+3600, '/');
		}
		return;
	}
	
	public static function clear($key = false)
	{
		if(!$key){
			//clear whole session
			session_destroy();
		}
		else{
			if(isset($_SESSION[$key])){
				unset($_SESSION[$key]);
			}
		}
		return;
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
		Session::set(self::$flashKey.'-'.$name, $message);
		if($type != ''){
			Session::set(self::$flashKey.'-'.$name.'-type', $type);
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
		$k = self::$flashKey.'-'.$name;
		$message = Session::get($k);
		Session::clear($k);
		return $message;
	}
	
	
}
