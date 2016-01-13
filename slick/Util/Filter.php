<?php
namespace Util;

class Filter
{
	protected static $func_filters = array();
	
	public static function addFilter($class, $method, $function, $prepend = false, $replace = false)
	{
		$key = $class.'@'.$method;
		if(!isset(self::$func_filters[$key])){
			self::$func_filters[$key] = array();
		}
		
		$item = array();
		$item['class'] = $class;
		$item['method'] = $method;
		$item['function'] = $function;
		$item['replace'] = $replace;
		$item['prepend'] = $prepend;
		
		self::$func_filters[$key][] = $item;
		return true;
	}
	
	public static function getMethodFilters($class, $method)
	{
		$key = $class.'@'.$method;
		if(!isset(self::$func_filters[$key])){
			return false;
		}
		return self::$func_filters[$key];
	}
	
	public static function getAllFilters()
	{
		return self::$func_filters;
	}

	
}
