<?php
namespace Util;

class Hook
{
	protected static $func_hooks = array();
	
	public static function addHook($class, $method, $function)
	{
		$key = $class.'@'.$method;
		if(!isset(self::$func_hooks[$key])){
			self::$func_hooks[$key] = array();
		}
		
		$item = array();
		$item['class'] = $class;
		$item['method'] = $method;
		$item['function'] = $function;

		self::$func_hooks[$key][] = $item;
		return true;
	}
	
	public static function getMethodHooks($class, $method)
	{
		$key = $class.'@'.$method;
		if(!isset(self::$func_hooks[$key])){
			return false;
		}
		return self::$func_hooks[$key];
	}
	
	public static function getAllHooks()
	{
		return self::$func_hooks;
	}
	
}
