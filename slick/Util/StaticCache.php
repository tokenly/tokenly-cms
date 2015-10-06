<?php
namespace Util;

class StaticCache
{
	public static $cache_items = array();
	
	public static function cacheData($key, $value)
	{
		self::$cache_items[$key] = $value;
		return $value;
	}
	
	public static function retrieveData($key)
	{
		if(!isset(self::$cache_items[$key])){
			return false;
		}
		return self::$cache_items[$key];
	}
	
	
	
}
